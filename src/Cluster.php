<?php
namespace evseevnn\Cassandra;
use evseevnn\Cassandra\Cluster\Node;
use evseevnn\Cassandra\Exception\ClusterException;

class Cluster {

	/**
	 * @var array
	 */
	private $nodes;

	/**
	 * @var array
	 */
	private $usedNodes;

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
	 * @throws Exception\ClusterException
	 */
	public function getNode($random = FALSE) {
		if (empty($this->nodes)) {
			$this->nodes = $this->usedNodes;
			$this->usedNodes = array();
			throw new ClusterException('Node list is empty.');
		}

		if ($random) {
			$nodeKey = array_rand($this->nodes);
		}
		else {
			$nodeKey = array_keys($this->nodes)[0];
		}

		$node = $this->nodes[$nodeKey];

		try {
			if ((array)$node === $node) {
				$this->usedNodes[$nodeKey] = $node;
				$node = new Node($nodeKey, $node);
				unset($this->nodes[$nodeKey]);
			} else {
				$this->usedNodes[$nodeKey] = $node;
				$node = new Node($node);
				unset($this->nodes[$nodeKey]);
			}

		} catch (\InvalidArgumentException $e) {
			trigger_error($e->getMessage());
		}

		return $node;
	}
}