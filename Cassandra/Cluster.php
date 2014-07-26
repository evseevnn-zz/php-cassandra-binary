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
	 * @return Node
	 * @throws \InvalidArgumentException
	 * @throws Exception\ClusterException
	 */
	public function getRandomNode() {
		if (empty($this->nodes)) throw new ClusterException('Node list is empty.');
		shuffle($this->nodes);
		while(!empty($this->nodes)) {
			$endNode = end($this->nodes);
			try {
				if ((array)$endNode === $endNode) {
					$host = key($this->nodes);
					$node = new Node($host, $endNode);
					unset($this->nodes[$host]);
				} elseif (is_string($endNode)) {
					$node = new Node($endNode);
					unset($this->nodes[$endNode]);
				} else {
					trigger_error('Incorrect type for info of node.');
					unset($this->nodes[$endNode]);
				}
				break;
			} catch (\InvalidArgumentException $e) {
				trigger_error($e->getMessage());
			}
		}

		if (empty($node)) throw new \InvalidArgumentException('Incorrect connection parameters for all nodes.');

		return $node;
	}
}