<?

/**
	New line-node.
*/

class NlNode extends Node
{

	public $word;

	function __construct()
	{
		$this->word = "";
	}

	function myprint()
	{
		$result = " NL ";
		foreach ($this->children as $child)
		{
			$result = $result .  " " . $child->myprint();
		}
		return $result;
	}

	function __toString()
	{
		return "NlNode ";
	}
}
