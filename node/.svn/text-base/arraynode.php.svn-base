<?

class Arraynode extends Node
{

	public $word;

	function __construct($value, $depth)
	{
		$this->name = "Arraynode";
		$this->value = $value;
		$this->my_depth = $depth;
		$this->id = Node::$nr++;
	}

	/*
		Textnode can be both data to td or 
		information to attribute.
	*/
	function evaluate()
	{
		return $this->value;
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
		return "Arraynode ";
	}
}
