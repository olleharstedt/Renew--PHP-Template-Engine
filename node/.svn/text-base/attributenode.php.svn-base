<?

class Attributenode extends Node
{
	public $word;

	function __construct($word, $depth)
	{
		$this->name = "attributenode";
		$this->word = $word;
		$this->my_depth = $depth;
		$this->id = Node::$nr++;
	}

	function evaluate()
	{
		/*
		$tabs = "";
		for ($i = 0; $i < $this->my_depth; $i++)
		{
			$tabs = $tabs . "\t";
		}
		*/
		// Evaluate children and put them in quotes.
		$result =  " " . $this->word . "=\"";
		/*
		foreach ($this->children as $child)
		{
			$result .= $child->evaluate();
		}
		*/
		if (isset($this->children[0]))		// first child special case concerning space
		{
			$result .= $this->children[0]->evaluate();
		}
		for ($i = 1; $i < count($this->children); $i++)
		{
			$result .= " " . $this->children[$i]->evaluate();		// add a space before each new attribute
		}
		$result = $result . "\"";
		return $result;
	}

	function myprint()
	{
		$result = $this->name .  ", " . $this->word . ", " . $this->my_depth;
		if ($this->getParent())
			$result = $result . ", parent: " . $this->getParent()->word . " " . $this->getParent()->id . "\n";
		foreach ($this->children as $child)
		{
			$result = $result .  "\t" . $child->myprint();
		}
		return $result;
	}

	function __toString()
	{
		return "AttributeNode ";
	}
}
