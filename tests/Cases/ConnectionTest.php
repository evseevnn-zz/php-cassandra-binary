<?php

namespace evseevnn\Cassandra\Tests;

use evseevnn\Cassandra;

class ConnectionTest extends \PHPUnit_Framework_TestCase
{

	public function testCreateConnection()
	{
		$connection = new Cassandra\Database(['127.0.0.1:9042']);
		$connection->connect();
	}

	public function testDoubleConnect()
	{
		$connection = new Cassandra\Database(['127.0.0.1:9042']);
		$connection->connect();
		$connection->connect();
	}

	public function testNonOpenPortConnection()
	{
		$start = time();
		try {
			$connection = new Cassandra\Database(['127.0.0.1:9201']);
			$connection->connect();
		} catch (Cassandra\Exception\ClusterException $e) {
			$this->assertEquals($e->getMessage(), 'Node list is empty, possibly because nodes are unreachable.');
		}
		$timespan = (time()-$start);
		$this->assertLessThanOrEqual(1,$timespan);
	}

	public function testWrongIPConnection()
	{
		$start = time();
		try {
			$connection = new Cassandra\Database(
				['127.0.0.2:9201'],
				null,
				['connect_timeout_ms' => 2500]);
			$connection->connect();
		} catch (Cassandra\Exception\ClusterException $e) {
			$this->assertEquals($e->getMessage(), 'Node list is empty, possibly because nodes are unreachable.');
		};
		$timespan = (time()-$start);
		$this->assertLessThanOrEqual(1,$timespan);
	}

	public function testTimeoutConnection()
	{
		if (PHP_OS == 'Linux') {
			shell_exec('sudo iptables -A OUTPUT -p tcp -m tcp --sport 9042 -j DROP');
		} else {
			$this->markTestSkipped("Your OS " . PHP_OS . " can not run timeout tests, since they depend on iptables");
		}
		$start = time();
		try {
			$connection = new Cassandra\Database(
				[ '127.0.0.1:9042' ],
				null,
				['connect_timeout_ms' => 2500]
			);
			$connection->connect();
		} catch (Cassandra\Exception\ClusterException $e) {
			$catched = true;
		}
		$this->assertEquals(true, $catched);
		$timespan = (time()-$start);
		shell_exec('sudo iptables -D OUTPUT -p tcp -m tcp --sport 9042 -j DROP');
		$this->assertLessThanOrEqual(3,$timespan);
		$this->assertGreaterThanOrEqual(2, $timespan);
	}

}