<?

/*
	Dont know if its meaningfull for this class to have a value. It should only operate on the
	parser array storing keys and values for variables.
*/

class Variablenode extends Node
{

	public $word;

	function __construct($value, $word, $depth, &$parser)
	{
		$this->name = "Variablenode";
		$this->value = $value;			// is this meaningful?
		$this->word = $word;
		$this->my_depth = $depth;
		$this->id = Node::$nr++;
		$this->parser = $parser;		// key-value array from the parser
		//$this->parser_vars[$this->word] = "asdal;skd";
	}

	/*
		Can be unbound, used for the first time, without children, cast error
		Used for the first time with children, evaluate children as value
		Used before without children, evaluate value as is
		Used before, now with children, evaluate, substitue ? with evaluated children.

		Should work both with ? and $n system.
		?-system replaces ?:s with arguments. First arg replaces first ?, second arg the second ? and so on.
		$n-system replaces $n with args. First arg replaces ALL $1, second all $2, and so on.

		Presence of both ? and $n in the same variable throws error.
	*/
	function evaluate()
	{
		$val = $this->parser->vars[$this->word];
		if ($val)
		{
			if (count($this->children) > 0)		// has value and children - replace ? or $n with children
			{
				$result = $val;

				// look for ?
				$has_qm = preg_match("/\?/", $val);
				if ($has_qm == 1)	// has ?
				{
					foreach ($this->children as $child)
					{
						$v = $child->evaluate();
						// Strip spaces in beginning and end
						$v = rtrim($v);
						$v = ltrim($v);
						$result = preg_replace("/\?/", $v, $result, 1);
					}
					return $result;
				}

				$has_dollar = preg_match('/\$\d/', $val);
				if ($has_dollar == 1)	// has $n
				{
					$n = 1;	// counter for $n
					foreach ($this->children as $child)
					{
						$v = $child->evaluate();
						// Strip spaces in beginning and end
						$v = rtrim($v);
						$v = ltrim($v);
						$result = preg_replace('/\$' . $n . '/', $v, $result);	// replace all $n with arg
						$n++;
					}
					return $result;
				}
				else
					throw new Exception('Variable has children, but neither ? nor $n');

			}
			else	// no children, just return value
			{
				return $val;
			}
		}
		else	// no value, check for assignments
		{
			if (count($this->children) > 0)
			{
				foreach ($this->children as $child)
				{
					$this->value .= $child->evaluate();
				}
				$this->children = null;
				$this->parser->vars[$this->word] = $this->value;
			}
			else
				return "Error: Variable has no value";
		}
	}

	function myprint()
	{
		$result = $this->name .  ", " . $this->word . ", " . $this->my_depth . ", assoc val = " . $this->parser->vars[$this->word];
		if ($this->getParent())
			$result = $result . ", parent: " . $this->getParent()->word . " " . $this->getParent()->id;
		foreach ($this->linechildren as $linechild)
		{
			$result .= $linechild->word;
		}
		$result .= "\n";
		foreach ($this->children as $child)
		{
			$result = $result .  "\t" . $child->myprint();
		}
		return $result;
	}

	function __toString()
	{
		return "Arraynode ";
	}
}
