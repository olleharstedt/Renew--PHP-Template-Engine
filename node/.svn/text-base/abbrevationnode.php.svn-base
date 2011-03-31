<?

class Abbrevationnode extends Node
{
	public $word;

	function __construct($word, $depth)
	{
		$this->name = "AbbrevationNode";
		$this->word = $word;
		$this->abb = $word;
		$this->my_depth = $depth;
		$this->id = Node::$nr++;

	}

	function evaluate()
	{
		return Parser::$abbrevations[$this->word];
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
