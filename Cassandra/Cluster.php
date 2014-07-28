<?php
namespace Cassandra;
use Cassandra\Cluster\Node;
use Cassandra\Exception\ClusterException;

class Cluster {

	/**
	 * @var array
	 */
	private $nodes;

	/**
	 * @param array $nodes
	 */
	public function __construct(array $nodes = []) {
		$this->nodes = $nodes;
	}

	/**
	 * @param string $host
	 */
	public function appendNode($host) {
		$this->nodes[] = $host;
	}

	/**
	 * Returns a random node from the cluster's node list
	 * @throws \InvalidArgumentException
	 * @throws Exception\ClusterException
	 * @return Node|null
	 */
	public function getRandomNode() {
		if (empty($this->nodes)) throw new ClusterException('Node list is empty.');
		$randomKey = array_rand($this->nodes);
		$randomValue = $this->nodes[$randomKey];
		$node = null;
		if (is_array($randomValue)) {
			// $randomKey is an IP address and $randomValue is an array of options we pass to Node.
			$node = new Node($randomKey, $randomValue);
		} else {
			// $randomKey is an index and $randomValue is the IP address of the node.
			$node = new Node($randomValue);
		}
		unset($this->nodes[$randomKey]);
		return $node;
	}
}
