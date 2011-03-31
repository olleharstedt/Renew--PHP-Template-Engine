<?

require_once(PARSER_DIR . "node/node.php");

foreach (glob(PARSER_DIR . "node/*.php") as $filename) { require_once($filename); }

require_once(PARSER_DIR . "parser.php");
