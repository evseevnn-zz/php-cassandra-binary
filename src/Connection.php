<?php
namespace evseevnn\Cassandra;
use evseevnn\Cassandra\Cluster\Node;
use evseevnn\Cassandra\Enum;
use evseevnn\Cassandra\Exception\ConnectionException;
use evseevnn\Cassandra\Protocol\Frame;
use evseevnn\Cassandra\Protocol\Request;
use evseevnn\Cassandra\Protocol\Response;

class Connection {

	/**
	 * @var Cluster
	 */
	private $cluster;

	/**
	 * @var int
	 */
	private $connMaxAttempts;

	/**
	 * @var int
	 */
	private $connAttempts;

	/**
	 * @var UseRandomNOdes
	 */
	private $useRandomNodes;

	/**
	 * @var Node
	 */
	private $node;

	/**
	 * @var resource
	 */
	private $connection;

	/**
	 * @param Cluster $cluster
	 */
	public function __construct(Cluster $cluster, $useRandomNodes) {
		$this->cluster = $cluster;
		$this->useRandomNodes = $useRandomNodes;
		$this->connMaxAttempts = 2;
		$this->connAttempts = 0;
	}

	public function connect() {

		try {

			$this->node = $this->cluster->getNode($this->useRandomNodes);
			$this->connection = $this->node->getConnection();

		} catch (ConnectionException $e) {
			$this->connect();

		} catch (Exception\ClusterException $e) {

			if ($this->connAttempts >= $this->connMaxAttempts) {
				throw new ConnectionException('I tried to connect to Database ' . $this->connMaxAttempts . ' times with no response.');
			}

			$this->connAttempts++;

			$this->cluster->resetNodes();

			$this->connect();
		}
	}

	/**
	 * @return bool
	 */
	public function disconnect() {
		$socketShutdown = socket_shutdown($this->connection);
		socket_close($this->connection);
		return $socketShutdown;
	}

	/**
	 * @return bool
	 */
	public function isConnected() {
		return $this->connection !== null;
	}

	/**
	 * @param Request $request
	 * @return \evseevnn\Cassandra\Protocol\Response
	 */
	public function sendRequest(Request $request) {
		$frame = new Frame(Enum\VersionEnum::REQUEST, $request->getType(), $request);
		socket_write($this->connection, $frame);
		return $this->getResponse();
	}

	/**
	 * @param $length
	 * @throws Exception\ConnectionException
	 * @return string
	 */
	private function fetchData($length) {
		$data = @socket_read($this->connection, $length);
		while (strlen($data) < $length) {
			$data .= @socket_read($this->connection, $length);
		}
		if (socket_last_error($this->connection) == 110) {
			throw new ConnectionException('Connection timed out');
		}

		return $data;
	}

	private function getResponse() {
		$data = $this->fetchData(8);
		$data = unpack('Cversion/Cflags/cstream/Copcode/Nlength', $data);
		if ($data['length']) {
			$body = $this->fetchData($data['length']);
		} else {
			$body = '';
		}

		return new Response($data['opcode'], $body);
	}

	/**
	 * @return \evseevnn\Cassandra\Cluster\Node
	 */
	public function getNode() {
		return $this->node;
	}
}
