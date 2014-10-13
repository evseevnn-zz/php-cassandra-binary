<?php

namespace evseevnn\Cassandra\Tests;

use evseevnn\Cassandra;

class QueryAsciiTest extends Setup\QueryTestCase
{

		public function testAsciiColumn()
		{
				self::$connection->query('CREATE TABLE AsciiTest (foo ascii PRIMARY KEY, bar ascii)');
				self::$connection->query(
						'INSERT INTO AsciiTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => 'baz', 'bar' => 'barbaz']
				);
				$result = self::$connection->query('SELECT * FROM AsciiTest WHERE foo = :foo', ['foo' => 'baz']);
				$this->assertEquals('barbaz', $result[0]['bar']);
				$this->assertEquals('baz', $result[0]['foo']);
		}

		public function testAsciiMap()
		{
				self::$connection->query('CREATE TABLE AsciiMapTest (foo ascii PRIMARY KEY, bar map<ascii,ascii>)');
				self::$connection->query(
						'INSERT INTO AsciiMapTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => 'baz', 'bar' => ['barbaz' => 'bazbar']]
				);
				$result = self::$connection->query('SELECT * FROM AsciiMapTest WHERE foo = :foo', ['foo' => 'baz']);
				$this->assertEquals(['barbaz' => 'bazbar'], $result[0]['bar']);
				$this->assertEquals('baz', $result[0]['foo']);
		}

		public function testAsciiSet()
		{
				self::$connection->query('CREATE TABLE AsciiSetTest (foo ascii PRIMARY KEY, bar set<ascii>)');
				self::$connection->query(
						'INSERT INTO AsciiSetTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => 'baz', 'bar' => ['barbaz', 'bazbar']]
				);
				$result = self::$connection->query('SELECT * FROM AsciiSetTest WHERE foo = :foo', ['foo' => 'baz']);
				$this->assertEquals(['barbaz', 'bazbar'], $result[0]['bar']);
				$this->assertEquals('baz', $result[0]['foo']);
				//according to Spec, this should always be returned alphabetically.
				self::$connection->query(
						'INSERT INTO AsciiSetTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => 'baz2', 'bar' => ['bazbar', 'barbaz']]
				);
				$result = self::$connection->query('SELECT * FROM AsciiSetTest WHERE foo = :foo', ['foo' => 'baz2']);
				$this->assertEquals(['barbaz', 'bazbar'], $result[0]['bar']);
		}

		public function testAsciiList()
		{
				self::$connection->query('CREATE TABLE AsciiListTest (foo ascii PRIMARY KEY, bar list<ascii>)');
				self::$connection->query(
						'INSERT INTO AsciiListTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => 'baz', 'bar' => ['barbaz', 'bazbar']]
				);
				$result = self::$connection->query('SELECT * FROM AsciiListTest WHERE foo = :foo', ['foo' => 'baz']);
				$this->assertEquals(['barbaz', 'bazbar'], $result[0]['bar']);
				$this->assertEquals('baz', $result[0]['foo']);
				//according to Spec, this should be returned in index order
				self::$connection->query(
						'INSERT INTO AsciiListTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => 'baz2', 'bar' => ['bazbar', 'barbaz']]
				);
				$result = self::$connection->query('SELECT * FROM AsciiListTest WHERE foo = :foo', ['foo' => 'baz2']);
				$this->assertEquals(['bazbar', 'barbaz'], $result[0]['bar']);
		}

}