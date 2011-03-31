<?

class TabNode extends Node
{
	function __construct()
	{
	}

	function myprint()
	{
		$result = "TAB ";
		foreach ($this->children as $child)
		{
			$result = $result .  " " . $child->myprint();
		}
		return $result;
	}
}
