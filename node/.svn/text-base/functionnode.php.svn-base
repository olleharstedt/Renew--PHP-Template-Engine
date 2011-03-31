<?

class Functionnode extends Node
{
	public $word;

	function __construct($word, $function, $depth)
	{
		$this->name = "FunctionNode";
		$this->func = $function;
		$this->word = $word;
		$this->my_depth = $depth;
		$this->id = Node::$nr++;		// Static counter
	}

	function evaluate()
	{
		$eval_children = array();
		foreach ($this->linechildren as $child)
		{
			$eval_children[] = $child->evaluate();
		}
		foreach ($this->children as $child)
		{
			$eval_children[] = $child->evaluate();
		}

		return call_user_func_array($this->func, $eval_children);
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
		return $this->name . ": " . $this->word . " ";
	}

}
