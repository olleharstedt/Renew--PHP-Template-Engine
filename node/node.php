<?

/**

	Base class for the nodes.

*/

class Node {

	protected $parent;
	protected $children = array();
	protected $linechildren = array();	// Nodes on the same line, after this node
	protected $my_depth;
	public $id;
	public $name;
	public $word;

	static public $nr = 0;

	function __construct($p) {
		$this->name = "Node";
		$this->parent = $p;
		$this->id = Node::$nr++;	// For debug purpose
		$this->word = "";
	}

	function addChild($child)
	{
		$this->children[] = $child;
	}

	function addLineChild($child) { $this->linechildren[] = $child; }

	function setParent($node) { $this->parent = $node; }
	function getParent() { return $this->parent; }

	function setDepth($d) { $this->my_depth = $d; }
	function getDepth() { return $this->my_depth; }

	function evaluate()
	{
		$result = "";
		foreach ($this->children as $child)
		{
			$result = $result . $child->evaluate();
		}
		return $result;
	}

	/**
		//Build the syntax tree.
	function build($line_data)
	{
		// Check indentation level of line compared to this node.
		if (count_tabs($line_data) <= $this->my_depth)
			return;

		$child = get_wordnode($line_data);
		$this->children[] = $child;
		$child->build($line_data);
	}
	*/
	
	function __toString() {
		return "Node ";
	}

	function myprint()
	{
		$result = $this->name .  ", " . $this->my_depth . "\n";
		foreach ($this->children as $child)
		{
			$result = $result .  "\t" . $child->myprint();
		}
		return $result;
	}
}
