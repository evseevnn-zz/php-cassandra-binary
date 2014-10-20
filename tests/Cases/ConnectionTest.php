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

		public function testNonFunctioningConnection()
		{
				$this->markTestSkipped('needs better connection handling');
				$connection = new Cassandra\Database(['127.0.0.1:9201']);
				$connection->connect();
		}

}