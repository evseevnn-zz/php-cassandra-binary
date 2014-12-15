<?php

namespace evseevnn\Cassandra\Tests;

use evseevnn\Cassandra;

class QueryTimestampTest extends Setup\QueryTestCase
{
	public function testRetrievedTimestampRow()
	{
		self::$connection->query('CREATE TABLE TimestampTest (p_key text, c_key timestamp, some_data double,
		PRIMARY KEY (p_key, c_key));');
		self::$connection->query(
			'INSERT INTO TimestampTest (p_key, c_key, some_data) VALUES (:p_key, :c_key, :some_data)',
			['p_key' => '2014', 'c_key' => 1417302000, 'some_data' => 0.12321]
		);
		$result = self::$connection->query(
			'SELECT * FROM TimestampTest WHERE p_key = :p_key',
			['p_key' => '2014']
		);
		$this->assertFloatEquals(0.12321, $result[0]['some_data']);
		$this->assertFloatEquals(1417302000, $result[0]['c_key']);
	}
}
