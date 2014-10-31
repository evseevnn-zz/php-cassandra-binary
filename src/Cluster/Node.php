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
		'password' => null,
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
	public function getConnection($connect_timeout_ms = 10000) {
		if (!empty($this->socket)) return $this->socket;

		$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)
			or new ConnectionException('Unable to create socket');
		socket_set_option($this->socket, getprotobyname('TCP'), TCP_NODELAY, 1);
		socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, ["sec" => self::STREAM_TIMEOUT, "usec" => 0]);
		socket_set_option($this->socket, SOL_SOCKET, SO_SNDTIMEO, ["sec" => self::STREAM_TIMEOUT, "usec" => 0]);

		if (!($this->socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP))) {
			throw new ConnectionException("Error Creating Socket: ".socket_strerror(socket_last_error()));
		}

		socket_set_nonblock($this->socket);

		$error = NULL;
		$attempts = 0;

		$connected = null;
		while (!($connected = @socket_connect($this->socket, $this->host, $this->port)) && $attempts++ < $connect_timeout_ms) {
			$error = socket_last_error();

			if ($error == SOCKET_EISCONN) {
				$connected = true;
				break;
			}

			if ($error != SOCKET_EINPROGRESS && $error != SOCKET_EALREADY) {
				socket_close($this->socket);
				throw new ConnectionException("Error Connecting Cassandra Socket: ".socket_strerror($error));
			}

			if ($error == 37) {
				socket_close($this->socket);
				throw new ConnectionException("Error Connecting Cassandra Socket: ".socket_strerror($error));
			}
			usleep(1000);
		}

		if (!$connected) {
			socket_close($this->socket);
			throw new ConnectionException("Error Connecting Cassandra Socket: Connect Timed Out After {$connect_timeout_ms} seconds.");
		}

		socket_set_block($this->socket);

		return $this->socket;
	}

	/**
	 * @return array
	 */
	public function getOptions() {
		return $this->options;
	}
}