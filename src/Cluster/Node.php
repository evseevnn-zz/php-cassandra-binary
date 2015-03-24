<?php
namespace evseevnn\Cassandra\Cluster;

use evseevnn\Cassandra\Exception\ConnectionException;

class Node
{

	const STREAM_TIMEOUT = 2;

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
	public function __construct($host, array $options = [])
	{
		$this->host = $host;
		if (strstr($this->host, ':'))
		{
			$this->port = (int)substr(strstr($this->host, ':'), 1);
			$this->host = substr($this->host, 0, -1 - strlen($this->port));
			if (!$this->port)
			{
				throw new \InvalidArgumentException('Invalid port number');
			}
		}

		$this->options = array_merge($this->options, $options);
	}

	/**
	 * @return resource
	 * @throws \Exception
	 */
	public function getConnection()
	{
		if ( ! empty($this->socket)) return $this->socket;

		$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

		socket_set_option($this->socket, getprotobyname('TCP'), TCP_NODELAY, 1);
		socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, ["sec" => self::STREAM_TIMEOUT, "usec" => 0]);
		socket_set_nonblock($this->socket);

		$maxAttempts = 3;
		$attempts = 0;

		while ( ! @socket_connect($this->socket, $this->host, $this->port))
		{
			$attempts++;
			$err = substr(socket_last_error($this->socket), -2);

			// Connection OK!
			if ($err === "56")
			{
				break;
			}

			// 61: server found but port unavailable (connection refused)
			// 37: ip does not exist, i wait
			// 01: host not found
			if ($err === "61" || $err === "37" || $err === "01")
			{
				socket_close($this->socket);
				throw new ConnectionException('Unable to connect. Socket last error code : ' . $err);
			}

			// if timeout reaches then call exit();
			if ($attempts > $maxAttempts)
			{
				socket_close($this->socket);
				throw new ConnectionException('Unable to connect. Socket last error code (connection timeout) : ' . $err);
			}

			usleep(100000);
		}

		// Re-block the socket if needed
		socket_set_block($this->socket);

		return $this->socket;
	}

	/**
	 * @return array
	 */
	public function getOptions()
	{
		return $this->options;
	}

	/**
	 * @return string
	 */
	public function getHost()
	{
		return $this->host;
	}
}