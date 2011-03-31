<?

class Textnode extends Node
{

	public $word;

	function __construct($word, $depth)
	{
		$this->name = "Textnode";
		$this->word = $word;
		$this->my_depth = $depth;
		$this->id = Node::$nr++;
	}

	/*
		Textnode can be both data to td or information to attribute.
		If children?
	*/
	function evaluate()
	{
		$result = $this->word;
		foreach ($this->linechildren as $linechild)
		{
			$result .= " " . $linechild->word;
		}
		return $result;
	}

	function myprint()
	{
		$result = $this->name .  ", " . $this->word . ", " . $this->my_depth;
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
		return "Textnode ";
	}
}
