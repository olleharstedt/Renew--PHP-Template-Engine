<?

/*

	Should be identical to Tagnode, except it does not insert a closing </tag> tag.

*/

class Nonclosingtagnode extends Node
{
	public $word;

	function __construct($word, $depth)
	{
		$this->name = "WordNode";
		$this->word = $word;
		$this->my_depth = $depth;
		$this->id = Node::$nr++;
	}

	function evaluate()
	{
		$tabs = "";
		for ($i = 0; $i < $this->my_depth; $i++)
		{
			$tabs = $tabs . "\t";
		}

		$result = $tabs . "<" . $this->word;

		// Line-children of a tag will be quoted exactly, and treated as textnodes, that is
		// note evaluated, but just printing their word variable.
		foreach ($this->linechildren as $linechild)
		{
			$result .= " " . $linechild->word;
		}

		// If a child is attribute, don't close bracket.
		foreach ($this->children as $child)
		{
			if ($child->name == "attributenode")
			{
				$result .= $child->evaluate();
			}
		}
		// Close bracket after all attributes are added.
		$result = $result . ">\n";
		// Evaluate the rest.
		foreach ($this->children as $child)
		{
			if ($child->name == "attributenode");
			else
			{
				$result .= $child->evaluate();
			}
		}
		return $result;
	}

	function myprint()
	{
		$result = $this->name .  ", " . $this->word . ", " . $this->my_depth;
		if ($this->getParent())
			$result = $result . ", parent: " . " " . $this->getParent()->id . "\n";
		foreach ($this->children as $child)
		{
			$result = $result .  "\t" . $child->myprint();
		}
		return $result;
	}

	function __toString()
	{
		return "Tagnode ";
	}

}
