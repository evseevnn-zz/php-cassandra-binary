<?php

namespace evseevnn\Cassandra\Tests;

use evseevnn\Cassandra;

class QueryVarIntTest extends Setup\QueryTestCase
{

		public function testVarIntColumn()
		{
				self::$connection->query('CREATE TABLE VarIntTest (foo varint PRIMARY KEY, bar varint)');
				self::$connection->query(
						'INSERT INTO VarIntTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '2', 'bar' => '52']
				);
				$result = self::$connection->query('SELECT * FROM VarIntTest WHERE foo = :foo', ['foo' => '2']);
				$this->assertEquals('52', $result[0]['bar']);
				$this->assertEquals('2', $result[0]['foo']);
		}

		public function testVarIntMap()
		{
				$this->markTestSkipped("varints don't work in collections yet");
				self::$connection->query('CREATE TABLE VarIntMapTest (foo varint PRIMARY KEY, bar map<varint,varint>)');
				self::$connection->query(
						'INSERT INTO VarIntMapTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '2', 'bar' => ['52' => '25']]
				);
				$result = self::$connection->query('SELECT * FROM VarIntMapTest WHERE foo = :foo', ['foo' => '2']);
				$this->assertEquals(['52' => '25'], $result[0]['bar']);
				$this->assertEquals('2', $result[0]['foo']);
		}

		public function testVarIntSet()
		{
				$this->markTestSkipped("varints don't work in collections yet");
				self::$connection->query('CREATE TABLE VarIntSetTest (foo varint PRIMARY KEY, bar set<varint>)');
				self::$connection->query(
						'INSERT INTO VarIntSetTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '2', 'bar' => ['25', '52']]
				);
				$result = self::$connection->query('SELECT * FROM VarIntSetTest WHERE foo = :foo', ['foo' => '2']);
				$this->assertEquals([25, 52], $result[0]['bar']);
				$this->assertEquals('2', $result[0]['foo']);
				//according to Spec, this should always be returned alphabetically.
				self::$connection->query(
						'INSERT INTO VarIntSetTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '22', 'bar' => ['52', '25']]
				);
				$result = self::$connection->query('SELECT * FROM VarIntSetTest WHERE foo = :foo', ['foo' => '22']);
				$this->assertEquals([25, 52], $result[0]['bar']);
		}

		public function testVarIntList()
		{
				$this->markTestSkipped("varints don't work in collections yet");
				self::$connection->query('CREATE TABLE VarIntListTest (foo varint PRIMARY KEY, bar list<varint>)');
				self::$connection->query(
						'INSERT INTO VarIntListTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '2', 'bar' => ['52', '25']]
				);
				$result = self::$connection->query('SELECT * FROM VarIntListTest WHERE foo = :foo', ['foo' => '2']);
				$this->assertEquals(['52', '25'], $result[0]['bar']);
				$this->assertEquals('2', $result[0]['foo']);
				//according to Spec, this should be returned in index order
				self::$connection->query(
						'INSERT INTO VarIntListTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '22', 'bar' => ['25', '52']]
				);
				$result = self::$connection->query('SELECT * FROM VarIntListTest WHERE foo = :foo', ['foo' => '22']);
				$this->assertEquals(['25', '52'], $result[0]['bar']);
		}

}