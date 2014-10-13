<?php

namespace evseevnn\Cassandra\Tests;

use evseevnn\Cassandra;

class QueryTextTest extends Setup\QueryTestCase
{

		public function testTextColumn()
		{
				self::$connection->query('CREATE TABLE TextTest (foo text PRIMARY KEY, bar text)');
				self::$connection->query(
						'INSERT INTO TextTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => 'baz', 'bar' => 'barbaz']
				);
				$result = self::$connection->query('SELECT * FROM TextTest WHERE foo = :foo', ['foo' => 'baz']);
				$this->assertEquals('barbaz', $result[0]['bar']);
				$this->assertEquals('baz', $result[0]['foo']);
		}

		public function testTextMap()
		{
				self::$connection->query('CREATE TABLE TextMapTest (foo text PRIMARY KEY, bar map<text,text>)');
				self::$connection->query(
						'INSERT INTO TextMapTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => 'baz', 'bar' => ['barbaz' => 'bazbar']]
				);
				$result = self::$connection->query('SELECT * FROM TextMapTest WHERE foo = :foo', ['foo' => 'baz']);
				$this->assertEquals(['barbaz' => 'bazbar'], $result[0]['bar']);
				$this->assertEquals('baz', $result[0]['foo']);
		}

		public function testTextSet()
		{
				self::$connection->query('CREATE TABLE TextSetTest (foo text PRIMARY KEY, bar set<text>)');
				self::$connection->query(
						'INSERT INTO TextSetTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => 'baz', 'bar' => ['barbaz', 'bazbar']]
				);
				$result = self::$connection->query('SELECT * FROM TextSetTest WHERE foo = :foo', ['foo' => 'baz']);
				$this->assertEquals(['barbaz', 'bazbar'], $result[0]['bar']);
				$this->assertEquals('baz', $result[0]['foo']);
				//according to Spec, this should always be returned alphabetically.
				self::$connection->query(
						'INSERT INTO TextSetTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => 'baz2', 'bar' => ['bazbar', 'barbaz']]
				);
				$result = self::$connection->query('SELECT * FROM TextSetTest WHERE foo = :foo', ['foo' => 'baz2']);
				$this->assertEquals(['barbaz', 'bazbar'], $result[0]['bar']);
		}

		public function testTextList()
		{
				self::$connection->query('CREATE TABLE TextListTest (foo text PRIMARY KEY, bar list<text>)');
				self::$connection->query(
						'INSERT INTO TextListTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => 'baz', 'bar' => ['barbaz', 'bazbar']]
				);
				$result = self::$connection->query('SELECT * FROM TextListTest WHERE foo = :foo', ['foo' => 'baz']);
				$this->assertEquals(['barbaz', 'bazbar'], $result[0]['bar']);
				$this->assertEquals('baz', $result[0]['foo']);
				//according to Spec, this should be returned in index order
				self::$connection->query(
						'INSERT INTO TextListTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => 'baz2', 'bar' => ['bazbar', 'barbaz']]
				);
				$result = self::$connection->query('SELECT * FROM TextListTest WHERE foo = :foo', ['foo' => 'baz2']);
				$this->assertEquals(['bazbar', 'barbaz'], $result[0]['bar']);
		}

}