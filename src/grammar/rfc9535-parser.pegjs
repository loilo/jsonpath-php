{{
  function buildLogicalExpression($head, $tail) {
    return array_reduce($tail, function ($result, $element) {
      return (object) [
        'type' => 'LogicalBinary',
        'operator' => $element[1],
        'left' => $result,
        'right' => $element[3],
      ];
    }, $head);
  }

  function buildUnaryExpression($not, $query) {
    if ($not) {
      return (object) [
        'type' => "LogicalUnary",
        'operator' => "!",
        'expr' => $query,
      ];
    } else {
      return $query;
    }
  }
}}

// jsonpath-query			= root-identifier segments
JsonpathQuery
  = RootIdentifier segments:Segments {
      return (object) [
        'type' => "Root", 
        'segments' => $segments,
      ];
    }

// segments						= *(S segment)
Segments = segments:(S @Segment)* 

// S									 = *B				; optional blank space
S = B*

// B									 = %x20 /		; Space
//											 %x09 /		; Horizontal tab
//											 %x0A /		; Line feed or New line
//											 %x0D			; Carriage return
B = "\x20"
  / "\x09"
  / "\x0A"
  / "\x0D"

// root-identifier		 = "$"
RootIdentifier = "$"

// selector						= name-selector	/
//											 wildcard-selector /
//											 slice-selector /
//											 index-selector /
//											 filter-selector
Selector
  = NameSelector
  / WildcardSelector
  / SliceSelector
  / IndexSelector
  / FilterSelector

// name-selector			 = string-literal
NameSelector = literal:StringLiteral {
  return (object) [
    'type' => "NameSelector",
    'member' => $literal,
  ];
}

// string-literal			= %x22 *double-quoted %x22 /		 ; "string"
//											 %x27 *single-quoted %x27			 ; 'string'
StringLiteral
  = "\x22" literals:DoubleQuoted* "\x22" { return join('', $literals); }
  / "\x27" literals:SingleQuoted* "\x27" { return join('', $literals); }

// double-quoted			 = unescaped /
//											 %x27			/										; '
//											 ESC %x22	/										; \"
//											 ESC escapable
DoubleQuoted
  = Unescaped
  / "\x27"
  / ESC @"\x22"
  / ESC @Escapable

// single-quoted			 = unescaped /
//											 %x22			/										; "
//											 ESC %x27	/										; \'
//											 ESC escapable
SingleQuoted
  = Unescaped
  / "\x22"
  / ESC @"\x27"
  / ESC @Escapable

// ESC								 = %x5C													 ; \	backslash
ESC = "\x5c"

// unescaped					 = %x20-21 /											; see RFC 8259
//													; omit 0x22 "
//											 %x23-26 /
//													; omit 0x27 '
//											 %x28-5B /
//													; omit 0x5C \
//											 %x5D-D7FF /
//													; skip surrogate code points
//											 %xE000-10FFFF
Unescaped
  = [\u0020-\u0021]
  / [\u0023-\u0026]
  / [\u0028-\u005B]
  / [\u005D-\uD7FF]
  / [\uE000-\uFFFF] // BMP
  / $([\ud800-\udbff] [\udc00-\udfff])	// Surrogate Pair

// escapable					 = %x62 / ; b BS backspace U+0008
//											 %x66 / ; f FF form feed U+000C
//											 %x6E / ; n LF line feed U+000A
//											 %x72 / ; r CR carriage return U+000D
//											 %x74 / ; t HT horizontal tab U+0009
//											 "/"	/ ; / slash (solidus) U+002F
//											 "\"	/ ; \ backslash (reverse solidus) U+005C
//											 (%x75 hexchar) ;	uXXXX			U+XXXX
Escapable
  = "\x62" { return "\b";}
  / "\x66" { return "\f";}
  / "\x6E" { return "\n";}
  / "\x72" { return "\r";}
  / "\x74" { return "\t";}
  / "/"		{ return "/";}
  / "\\"	 { return "\\";}
  / chars:("\x75" @Hexchar) { return join('', array_map(fn ($char) => chr_unicode($char), $chars)); }

// hexchar						 = non-surrogate /
//											 (high-surrogate "\" %x75 low-surrogate)
Hexchar
  = code:NonSurrogate { return [$code]; }
  / pair:(@HighSurrogate "\\" "\x75" @LowSurrogate) { return $pair; }

// non-surrogate			 = ((DIGIT / "A"/"B"/"C" / "E"/"F") 3HEXDIG) /
//												("D" %x30-37 2HEXDIG )
NonSurrogate
  = ((DIGIT / [ABCEF]i) HEXDIG|3|)	{ return hexdec($this->text()); }
  / ("D"i [\u0030-\u0037] HEXDIG|2|) { return hexdec($this->text()); }

// high-surrogate			= "D" ("8"/"9"/"A"/"B") 2HEXDIG
HighSurrogate = "D"i [89AB]i HEXDIG|2| { return hexdec($this->text()); }

// low-surrogate			 = "D" ("C"/"D"/"E"/"F") 2HEXDIG
LowSurrogate = "D"i [CDEF]i HEXDIG|2| { return hexdec($this->text()); }

// HEXDIG							= DIGIT / "A" / "B" / "C" / "D" / "E" / "F"
HEXDIG = DIGIT / [ABCDEF]i

// wildcard-selector	 = "*"
WildcardSelector = "*" { return (object) [ 'type' => "WildcardSelector" ]; }

// index-selector			= int												; decimal integer
IndexSelector = index:int { return (object) [ 'type' => "IndexSelector", 'index' => $index ]; }

// int								 = "0" /
//											 (["-"] DIGIT1 *DIGIT)			; - optional
int
  = "0" { return 0; }
  / (("-")? DIGIT1 DIGIT*) {
      $int_min = -(2 ** 53) + 1;
      $int_max = 2 ** 53 - 1;
      $number = filter_var($this->text(), FILTER_VALIDATE_INT);
      if ($number !== false && $number >= $int_min && $number <= $int_max) {
        return $number;
      } else {
        throw new \Exception('Index must be within the range of I-JSON: ' . $number);
      }
    }

// DIGIT1							= %x31-39										; 1-9 non-zero digit
DIGIT1 = [\x31-\x39]

// slice-selector			= [start S] ":" S [end S] [":" [S step ]]
SliceSelector = start:(@Start S)? ":" S end:(@End S)? step:(":" @(S @Step)?)? {
  return (object) [
    'type' => "SliceSelector",
    'start' => $start,
    'end' => $end,
    'step' => $step,
  ];
}

// start							 = int			 ; included in selection
Start = int
// end								 = int			 ; not included in selection
End = int
// step								= int			 ; default: 1
Step = int

// filter-selector		 = "?" S logical-expr
FilterSelector = "?" S expr:LogicalExpr { return (object) [ 'type' => "FilterSelector", 'expr' => $expr ]; }

// logical-expr				= logical-or-expr
LogicalExpr = LogicalOrExpr

// logical-or-expr		 = logical-and-expr *(S "||" S logical-and-expr)
//												 ; disjunction
//												 ; binds less tightly than conjunction
LogicalOrExpr = head:LogicalAndExpr tail:(S "||" S LogicalAndExpr)* {
  return buildLogicalExpression($head, $tail);
}

// logical-and-expr		= basic-expr *(S "&&" S basic-expr)
//												 ; conjunction
//												 ; binds more tightly than disjunction
LogicalAndExpr = head:BasicExpr tail:(S "&&" S BasicExpr)* {
  return buildLogicalExpression($head, $tail);
}

// basic-expr					= paren-expr /
//											 comparison-expr /
//											 test-expr
BasicExpr
  = ParenExpr
  / ComparisonExpr
  / TestExpr

// paren-expr					= [logical-not-op S] "(" S logical-expr S ")"
//																				 ; parenthesized expression
ParenExpr = not:(@LogicalNotOp S)? "(" S expr:LogicalExpr S ")" {
  if ($not) {
    return (object) [
      'type' => "LogicalUnary",
      'operator' => "!",
      'expr' => $expr,
    ];
  }

  return $expr;
}

// logical-not-op			= "!"							 ; logical NOT operator
LogicalNotOp = "!"

// test-expr					 = [logical-not-op S]
//											(filter-query / ; existence/non-existence
//											 function-expr) ; LogicalType or
//																			; NodesType
TestExpr = not:(@LogicalNotOp S)? query:(FilterQuery / FunctionExpr) {
  return buildUnaryExpression($not, (object) [
    'type' => "TestExpr",
    'query' => $query,
  ]);
}

// filter-query				= rel-query / jsonpath-query
FilterQuery = RelQuery / JsonpathQuery

// rel-query					 = current-node-identifier segments
RelQuery = CurrentNodeIdentifier segments:Segments {
  return (object) [
    'type' => 'CurrentNode',
    'segments' => $segments,
  ];
}

// current-node-identifier = "@"
CurrentNodeIdentifier = "@"

// comparison-expr		 = comparable S comparison-op S comparable
ComparisonExpr = left:Comparable S op:ComparisonOp S right:Comparable {
  return (object) [
    'type' => "ComparisonExpr",
    'left' => $left,
    'operator' => $op,
    'right' => $right,
  ];
}

// literal						 = number / string-literal /
//											 true / false / null
Literal = literal:(Number / StringLiteral / True / False / Null) {
  return (object) [
    'type' => "Literal",
    'member' => $literal,
  ];
}

// comparable					= literal /
//											 singular-query / ; singular query value
//											 function-expr		; ValueType
Comparable = Literal / SingularQuery / FunctionExpr

// comparison-op			 = "==" / "!=" /
//											 "<=" / ">=" /
//											 "<"	/ ">"
ComparisonOp
  = "=="
  / "!="
  / "<="
  / ">="
  / "<"
  / ">"

// singular-query			= rel-singular-query / abs-singular-query
SingularQuery = RelSingularQuery / AbsSingularQuery

// rel-singular-query	= current-node-identifier singular-query-segments
RelSingularQuery = CurrentNodeIdentifier segments:SingularQuerySegments {
  return (object) [
    'type' => 'CurrentNode',
    'segments' => $segments,
  ];
}

// abs-singular-query	= root-identifier singular-query-segments
AbsSingularQuery = RootIdentifier segments:SingularQuerySegments {
  return (object) [
    'type' => "Root",
    'segments' => $segments,
  ];
}

// singular-query-segments = *(S (name-segment / index-segment))
SingularQuerySegments = segment:(S @(NameSegment / IndexSegment))* {
  return $segment;
}

// name-segment				= ("[" name-selector "]") /
//											 ("." member-name-shorthand)
NameSegment
  = ("[" selector:NameSelector "]" { return [$selector]; }) 
  / ("." selector:MemberNameShorthand { return [$selector]; }) 

// index-segment			 = "[" index-selector "]"
IndexSegment = "[" selector:IndexSelector "]" { return [$selector]; }

// number							= (int / "-0") [ frac ] [ exp ] ; decimal number
Number = int:(int / "-0") frac:(Frac)? exp:(Exp)? {
  return floatval(join('', [$int, $frac ?? '', $exp ? "e{$exp}" : '']));
}

// frac								= "." 1*DIGIT									; decimal fraction
Frac = $("." DIGIT+)

// exp								 = "e" [ "-" / "+" ] 1*DIGIT		; decimal exponent
Exp = "e"i sign:([-+])? digits:DIGIT+ {
  $digits_string = join('', $digits);

  // Remove leading zeros as they don't pass FILTER_VALIDATE_INT
  $digits_string = preg_replace('/^0*(.)/', '$1', join('', $digits));

  $sign_string = $sign ?: '';

  $exponent_string = $sign_string . $digits_string;
  $exponent_int = filter_var($exponent_string, FILTER_VALIDATE_INT);
  if ($exponent_int !== false) {
    return $exponent_int;
  } else {
    throw new \Exception('Exponent must be within the range of I-JSON: ' . $exponent_string);
  }
}

// true								= %x74.72.75.65								; true
True = "true" {
  return true;
}

// false							 = %x66.61.6c.73.65						 ; false
False = "false" {
  return false;
}

// null								= %x6e.75.6c.6c								; null
Null = "null" {
  return null;
}

// function-name			 = function-name-first *function-name-char
FunctionName = $(FunctionNameFirst FunctionNameChar*)

// function-name-first = LCALPHA
FunctionNameFirst = LCALPHA

// function-name-char	= function-name-first / "_" / DIGIT
FunctionNameChar = FunctionNameFirst / "_" / DIGIT

// LCALPHA						 = %x61-7A	; "a".."z"
LCALPHA = [\x61-\x7A]

// function-expr			 = function-name "(" S [function-argument
//													*(S "," S function-argument)] S ")"
FunctionExpr = name:FunctionName "(" S args:(@FunctionArgument @(S "," S @FunctionArgument)*)? S ")" {
  if (!is_array($args)) {
    throw new \Exception('Function expression args must be array, got ' . gettype($args) . ' instead');
  }
  
  $head = $args[0];
  $tail = $args[1];
  return (object) [
    'type' => "FunctionExpr",
    'name' => $name,
    'args' => [$head, ...$tail],
  ];
}

// function-argument	 = literal /
//											 filter-query / ; (includes singular-query)
//											 logical-expr /
//											 function-exprcl
// NOTE: to prefer the function expr when the function is given as a argument.
FunctionArgument = Literal / FilterQuery / FunctionExpr / LogicalExpr

// segment						 = child-segment / descendant-segment
Segment = ChildSegment / DescendantSegment

// child-segment			 = bracketed-selection /
//											 ("."
//												(wildcard-selector /
//												 member-name-shorthand))
ChildSegment
  = BracketedSelection
  / ("." selector:(WildcardSelector / MemberNameShorthand) { return [$selector]; })

BracketedSelection = "[" S head:Selector tail:(S "," S @Selector)* S "]" {
  return [$head, ...$tail];
}

// member-name-shorthand = name-first *name-char
MemberNameShorthand = NameFirst NameChar* { 
  return (object) [
    'type' => "MemberNameShorthand",
    'member' => $this->text(),
  ];
}

// name-first					= ALPHA /
//											 "_"	 /
//											 %x80-10FFFF	 ; any non-ASCII Unicode character
NameFirst
  = ALPHA
  / "_"
  / [\u0080-\uD7FF] // BMP
                    // skip surrogate code points
  / [\uE000-\uFFFF] // BMP
  / $([\ud800-\udbff] [\udc00-\udfff])	// Surrogate Pair

// name-char					 = DIGIT / name-first
NameChar = DIGIT / NameFirst

// DIGIT							 = %x30-39							; 0-9
DIGIT = [0-9]

// ALPHA							 = %x41-5A / %x61-7A		; A-Z / a-z
ALPHA = [a-z]i

// descendant-segment	= ".." (bracketed-selection /
//														 wildcard-selector /
//														 member-name-shorthand)
DescendantSegment = ".." selectors:(BracketedSelection / WildcardSelector / MemberNameShorthand) {
  if (is_array($selectors)) {
    return (object) [
      'type' => "DescendantSegment",
      'selectors' => $selectors,
    ];
  } else {
    return (object) [
      'type' => "DescendantSegment",
      'selectors' => [$selectors],
    ];
  }
}
