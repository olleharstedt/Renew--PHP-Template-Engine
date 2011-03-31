<?

/*

	Semi-compiler for html mellanlager.

	Rules for code:
		Tabs only in beginning of line. Indentation is part of language as in Python; a "reserved word".
		No opening or closing brackets needed. It's marked by indentation instead.

	Example code:

	table
		tr 
			td
				data
			td
				mer data
			td
				img
					src
						/img/hej.jpg


	Pre:	Reads keywords from file keywords.txt

*/

class Parser {

	private $file;
	private $word;
	private $line;

	private $keywords = array();		// Keywords like html, head, div, src. Read from file.
	public static $abbrevations = array();	// doctype => <!doctype html> and such
	public $vars = array();			// For assigned variables
	private $funcs = array();			// For assigned functions/control structures

	function __construct($filename) {
		$this->file = fopen($filename, 'r');

		$this->read_keywords();
			
		// when to close file?
	}
	
	/*
		Used if you want to use several files, e.g. loading abbrevations from some file and the actual html from another.
		After setFile, you should execute buildSyntaxTree and evaluate.
	*/

	function setFile($fn)
	{
		if (isset($this->file))
			fclose($this->file);

		$this->file = fopen($fn, 'r');

		if ($this->file == FALSE)
			throw new Exception("Error: Could not load file");
	}

	//function getFile() { return $this->file; }
	/**
		Read keywords from file keywords.txt.
		That file must be in "current" directory.
	*/
	private function read_keywords()
	{
		$kfile = fopen(PARSER_DIR . "keywords.txt", 'r');
		while ($line = fgets($kfile))
		{
			if ($line[0] != '#')
			{
				$words = preg_split("/[\t\n\s]+/", $line);
				//dump($words);
				//echo "<br>";
				if (!isset($words[1]))	// TODO: This is not correct
					exit("Error in keywords.txt - missing node type");
				$this->keywords[$words[0]] = $words[1];
				if ($words[1] == "abbrevationnode")
				{
					Parser::$abbrevations[$words[0]] = implode(" ", array_slice($words, 2)) . "\n";
				}
			}
		}	
		fclose($kfile);
		//dump($this->keywords);
		//dump($this->abbrevations);
		//dump(get_declared_classes());

	}

	/**
		Extract symbols from line, return as array with nodes;
		E.g.
		
		Tabnode Tabnode Tagnode

		Tabnode Tagnode Wordnode Tabnode Commentnode
	*/
	function nextLine()
	{
		if (feof($this->file))
			return "eof";

		$line = fgets($this->file);
		$result = array();
		$word = "";
		$depth_level = 0;
		
		for ($i = 0; $i < strlen($line); $i++)
		{
			$char = $line[$i];
			switch ($char)
			{
				case "\n":
					if ($word)
						$result[] = $this->newNode($word, $depth_level);		// newNode() uses reflection.
					//$result[] = new NlNode();
					break;
				case "\t":
					if ($word != "")
					{
						$result[] = $this->newNode($word, $depth_level);
						$word = "";
					}
					$result[] = new TabNode();
					$depth_level++;
					break;
				case " ":
					if ($word != "")
					{
						$result[] = $this->newNode($word, $depth_level);
						$word = "";
					}
					break;
				case "-":	// Comment
					if (isset($line[$i+1]) && $line[$i+1] == '-')
					{
						return $result;
					}
					else
						$word .= $char;
					break;
				case '"':	// quote => return hole line as text if this is the first non-tab char
					if ($this->only_tabs($result))
					{
						$result[] = new Textnode(substr($line, $i+1), $depth_level);
						return $result;
					}
					else
						$word = $word . $char;	// never forget this!!
					break;
				default:
					$word = $word . $char;
			}
		}

		/*	Debug info
		foreach ($result as $r)
		{
			echo $r->myprint();
		}
		echo "<br>";
		*/
		return $result;
	}

	/*
		Helper function to determine if nodes consists of only tabs. If that is the case, we know that no characters have been encountered yet on this line. Useful for determine if " (quote) is at the beginning of line.

		Pre: $nodes is an array of nodes
		Post: true if $nodes only har TabNodes; false otherwise.
	*/
	private function only_tabs($nodes)
	{
		foreach ($nodes as $n)
		{
			switch (get_class($n))
			{
				case "TabNode":
					break;
				default:
					return false;
			}
		}
		return true;
	}

	/**
		Uses reflection to create new object of corresponding type
		in the keywords list.
		If the word is not in the keyword list, a textNode will be created.

		Pre: the word, and its depth-level/indentation.
		Post: An instance of the correct node type.

		TODO: Add error handling
	*/
	function newNode($word, $depth_level)
	{
		// in_array(needle, haystack)

		// Check first char for special sign.
		switch ($word[0])
		{
			case '$':		// Dynamic variable, single or array
							// Can be unbound, used for the first time, without children, cast error
							// Used for the first time with children, evaluate children as value
							// Used before without children, evaluate value as is
							// Used before, now with children, evaluate, substitue ? with evaluated children
							// If followed by single digit, this is arg to abbrevation - return as is (textnode).
				if (isset($word[1]))
				{
					$is_digit = preg_match('/\d/', $word[1]);
					if ($is_digit == 1)
					{
						return new Textnode($word, $depth_level);
					}
				}
				$word = substr($word, 1);
				if (!isset($this->vars[$word]))
				{
					//return new Textnode("*TEMPLATE ERROR: No such variable: " . $substr($word, 1) . "* ", $depth_level);
					$this->vars[$word] = "";
					return new Variablenode("", $word, $depth_level, $this);
				}
				else
				{
					$val = $this->vars[$word];
					//echo "val = $val";
					return new Variablenode($val, $word, $depth_level, $this);
				}
				break;
			case '@':		// Assigned function
				if (isset($this->funcs[substr($word, 1)]))
					return new Functionnode(substr($word, 1), $this->funcs[substr($word, 1)], $depth_level);
				else
					return new Textnode("*TEMPLATE ERROR: No such function: " . $word . "* ", $depth_level);
				break;
			case '#':		// Tag id
				break;
			case '.':		// Tag class
				break;
			case '<':		// Don't do anything with original html tags.
				break;
			default:		// Final: check keyword, otherwise text node.
				break;
		}

		if (!isset($this->keywords[$word]))
		{
			$node = new Textnode($word, $depth_level);
			return $node;
		}

		$rc = null;

		// Fetch class name from keyword
		$symbol_name = $this->keywords[$word];

		if(class_exists($symbol_name))
		{
			$rc = new ReflectionClass($symbol_name);
			$instance = $rc->newInstance($word, $depth_level);
			return $instance;
		}
		else
		{
			echo "No such class: {$symbol_name}. Check keywords.txt. ";
			return null;
		}
	}

	/**
		Build up the parse tree.
		Line by line.
		Returns parse tree.
		
		With first symbol in line:
			1. Check for parent
		Symbols after the first in same line:
			1. Add as linenode to the first symbol
	*/
	function buildSyntaxTree()
	{
		// Root node?
		$root_node = new Node(null);
		//$root_node->setParent($root_node);	
		$root_node->setDepth(-1);
		$current_node = $root_node;					// This is the "marker" node.
		$i = 0;

		$parse = true;
		while($parse)
		{
			$line_data = $this->nextLine();
			if ($line_data == "eof")		// OBS: array() == null, empty array equals true
			{
				break;
			}
			if (!$line_data)	// If line start with --, nextLine will return an empty array
				continue;

			// Find first non-tab node
			$symbol = $line_data[0];		// symbol = node
			$n = 0;
			for ($i = 0; get_class($symbol) == "TabNode" && $i < count($line_data); $i++)
			{
				$symbol = $line_data[$i];
				$n = $i;	// Remember $i to for-loop after switch
			}

			// Only first node is placed in syntax tree.
			// Other nodes in this line will be line-children of the first node.
			if ($symbol)
			{
			switch (get_class($symbol))
			{
				case "TabNode":
					break;
				case "NlNode":
					break;
				case "CommentNode": 	// Don't analyse further.
					break;
				// Default for tags, text, attributes
				default:
					if ($symbol->getDepth() > $current_node->getDepth())
					{
						// Higher depth
						$current_node->addChild($symbol);
						$symbol->setParent($current_node);
						$current_node = $symbol;
						break;
					}
					if ($symbol->getDepth() == $current_node->getDepth())
					{
						// Equal depth
						$parent = $current_node->getParent();
						$parent->addChild($symbol);
						$symbol->setParent($parent);
						$current_node = $symbol;
						break;
					}
					if ($symbol->getDepth() < $current_node->getDepth())
					{
						// Lower depth
						// We don't know how much lower.
						// Loop back up the tree until you find same depth
						while ($current_node->getDepth() > $symbol->getDepth())
						{
							$current_node = $current_node->getParent();
						}
						// Now depth is equal
						$parent = $current_node->getParent();
						$parent->addChild($symbol);
						$symbol->setParent($parent);
						$current_node = $symbol;
						break;
					}
					break; 
			}
			}

			for ($i = $n + 1; $i < count($line_data); $i++)		// will this add nlnode too?
			{
				$symbol->addLineChild($line_data[$i]);
			}

		}

		return $root_node;
	}
	
	/**
		Assigns a value/function to a variable.
		Return true if the slot is free; otherwise false.
		If $value is callable, it will be stored in $this->funcs.
		Otherwise, it will be stored in $this->vars.
		Will not override taken slot.
	*/
	function assign($key, $value)
	{
		if (is_callable($value))
		{
			if (!isset($this->funcs[$key]))
			{
				$this->funcs[$key] = $value;
				return true;
			}
		}
		else if (!isset($this->vars[$key]))
		{
			$this->vars[$key] = $value; 
			return true;
		}

		return false;

	}

	/**
		Count tabs in line to find indentation level.
		Indentation level equals depth in syntax tree.
	*/
	function count_tabs(array $line_data)
	{
		$i = 0;
		foreach ($line_data as $symbol)
		{
			if (get_class($symbol) == "TabNode")
				$i++;
		}
		return $i;
	}

	function get_wordnode($line_data)
	{
		foreach ($line_data as $symbol)
		{
			if (get_class($symbol) == "WordNode")
				return $symbol;
		}
		return null;
	}
}
