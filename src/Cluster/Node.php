<?php
namespace evseevnn\Cassandra\Cluster;

use evseevnn\Cassandra\Exception\ConnectionException;

class Node {

	const STREAM_TIMEOUT = 10;

	/**
	 * @var string
	 */
	private $host;

	/**
	 * @var int
	 */
	private $port = 9042;

	/**
	 * @var resource
	 */
	private $socket;

	/**
	 * @var array
	 */
	private $options = [
		'username' => null,
		'password' => null
	];

	/**
	 * @param string $host
	 * @param array $options
	 * @throws \InvalidArgumentException
	 */
	public function __construct($host, array $options = []) {
		$this->host = $host;
		if (strstr($this->host, ':')) {
			$this->port = (int)substr(strstr($this->host, ':'), 1);
			$this->host = substr($this->host, 0, -1 - strlen($this->port));
			if (!$this->port) {
				throw new \InvalidArgumentException('Invalid port number');
			}
		}
		$this->options = array_merge($this->options, $options);
	}

	/**
	 * @return resource
	 * @throws \Exception
	 */
	public function getConnection() {
		if (!empty($this->socket)) return $this->socket;

		$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

		socket_set_option($this->socket, getprotobyname('TCP'), TCP_NODELAY, 1);
		socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, ["sec" => self::STREAM_TIMEOUT, "usec" => 0]);
		socket_set_nonblock($this->socket);

		$time = time();
		$timeout = 1; // seconds

		// Loop until a connection is successfully made or timeout reached
		while (!@socket_connect($this->socket, $this->host, $this->port)) {
			$err = socket_last_error($this->socket);

			// Connection OK!
			if($err === 10056) {
				break;
			}

			// If we reach timeout, throw exception and exit.
			if ((time() - $time) >= $timeout) {

				socket_set_block($this->socket);
				socket_close($this->socket);
				info('Timeout reached! Unable to connect to ' . $this->host . ' on port ' . $this->port);
				throw new ConnectionException("Unable to connect to Cassandra node.");
			}

			// 250 ms sleeping time to waitNo the next attempt.
			usleep(250000);
		}

		// Re-block the socket if needed
		socket_set_block($this->socket);

		return $this->socket;
	}

	/**
	 * @return array
	 */
	public function getOptions() {
		return $this->options;
	}

	/**
	 * @return string
	 */
	public function getHost() {
		return $this->host;
	}
}