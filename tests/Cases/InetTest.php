<?php

namespace evseevnn\Cassandra\Tests;

use evseevnn\Cassandra;

class QueryInetTest extends Setup\QueryTestCase
{

		public function testInetColumn()
		{
				self::$connection->query('CREATE TABLE InetTest (foo inet PRIMARY KEY, bar inet)');
				self::$connection->query(
						'INSERT INTO InetTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '192.168.1.2', 'bar' => '192.168.2.5']
				);
				$result = self::$connection->query('SELECT * FROM InetTest WHERE foo = :foo', ['foo' => '192.168.1.2']);
				$this->assertEquals('192.168.2.5', $result[0]['bar']);
				$this->assertEquals('192.168.1.2', $result[0]['foo']);
		}

		public function testInetMap()
		{
				self::$connection->query('CREATE TABLE InetMapTest (foo inet PRIMARY KEY, bar map<inet,inet>)');
				self::$connection->query(
						'INSERT INTO InetMapTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '192.168.1.2', 'bar' => ['192.168.2.5' => '192.68.2.16']]
				);
				$result = self::$connection->query('SELECT * FROM InetMapTest WHERE foo = :foo', ['foo' => '192.168.1.2']);
				$this->assertEquals(['192.168.2.5' => '192.68.2.16'], $result[0]['bar']);
				$this->assertEquals('192.168.1.2', $result[0]['foo']);
		}

		public function testInetSet()
		{
				self::$connection->query('CREATE TABLE InetSetTest (foo inet PRIMARY KEY, bar set<inet>)');
				self::$connection->query(
						'INSERT INTO InetSetTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '192.168.1.2', 'bar' => ['192.168.2.5', '2001:470:5284:201:d1c6:6f4a:b7c9:2f9e']]
				);
				$result = self::$connection->query('SELECT * FROM InetSetTest WHERE foo = :foo', ['foo' => '192.168.1.2']);
				$this->assertEquals(['2001:470:5284:201:d1c6:6f4a:b7c9:2f9e', '192.168.2.5'], $result[0]['bar']);
				$this->assertEquals('192.168.1.2', $result[0]['foo']);
				//according to Spec, this should always be returned alphabetically.
				self::$connection->query(
						'INSERT INTO InetSetTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '254.220.120.254', 'bar' => ['2001:470:5284:201:d1c6:6f4a:b7c9:2f9e', '192.168.2.5']]
				);
				$result = self::$connection->query('SELECT * FROM InetSetTest WHERE foo = :foo', ['foo' => '254.220.120.254']);
				$this->assertEquals(['2001:470:5284:201:d1c6:6f4a:b7c9:2f9e', '192.168.2.5'], $result[0]['bar']);
		}

		public function testInetList()
		{
				self::$connection->query('CREATE TABLE InetListTest (foo inet PRIMARY KEY, bar list<inet>)');
				self::$connection->query(
						'INSERT INTO InetListTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '192.168.1.2', 'bar' => ['192.168.2.5', '2001:470:5284:201:d1c6:6f4a:b7c9:2f9e']]
				);
				$result = self::$connection->query('SELECT * FROM InetListTest WHERE foo = :foo', ['foo' => '192.168.1.2']);
				$this->assertEquals(['192.168.2.5', '2001:470:5284:201:d1c6:6f4a:b7c9:2f9e'], $result[0]['bar']);
				$this->assertEquals('192.168.1.2', $result[0]['foo']);
				//according to Spec, this should be returned in index order
				self::$connection->query(
						'INSERT INTO InetListTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '254.220.120.254', 'bar' => ['2001:470:5284:201:d1c6:6f4a:b7c9:2f9e', '192.168.2.5']]
				);
				$result = self::$connection->query('SELECT * FROM InetListTest WHERE foo = :foo', ['foo' => '254.220.120.254']);
				$this->assertEquals(['2001:470:5284:201:d1c6:6f4a:b7c9:2f9e', '192.168.2.5'], $result[0]['bar']);
		}

}