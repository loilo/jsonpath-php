import * as fs from 'node:fs'
import peggy from 'peggy'
import phpeggy from 'phpeggy'

const pegjsGrammarFile = import.meta.dirname + '/rfc9535-parser.pegjs'
const phpParserFile = import.meta.dirname + '/../PeggyParser.php'

const grammar = fs.readFileSync(pegjsGrammarFile, 'utf-8')
const parser = peggy.generate(grammar, {
	plugins: [phpeggy],
	phpeggy: {
		parserNamespace: 'Loilo\\JsonPath',
		parserClassName: 'PeggyParser'
	}
})

fs.writeFileSync(phpParserFile, parser)
