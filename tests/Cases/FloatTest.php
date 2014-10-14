<?php

namespace evseevnn\Cassandra\Tests;

use evseevnn\Cassandra;

class QueryFloatTest extends Setup\QueryTestCase
{

		public function testFloatRow()
		{
				self::$connection->query('CREATE TABLE FloatTest (foo float PRIMARY KEY, bar float)');
				self::$connection->query('INSERT INTO FloatTest (foo, bar) VALUES (:foo, :bar)', ['foo' => 0.2, 'bar' => 5.2]);
				$result = self::$connection->query('SELECT * FROM FloatTest WHERE foo = :foo', ['foo' => 0.2]);
				$this->assertFloatEquals(5.2, $result[0]['bar']);
				$this->assertFloatEquals(0.2, $result[0]['foo']);
		}

		public function testFloatMap()
		{
				self::$connection->query('CREATE TABLE FloatMapTest (foo float PRIMARY KEY, bar map<float,float>)');
				self::$connection->query(
						'INSERT INTO FloatMapTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => 0.2, 'bar' => [5.2 => 2.5]]
				);
				$result = self::$connection->query('SELECT * FROM FloatMapTest WHERE foo = :foo', ['foo' => 0.2]);
				$this->assertFloatEquals([5.2 => 2.5], $result[0]['bar']);
				$this->assertFloatEquals(0.2, $result[0]['foo']);
		}

		public function testFloatSet()
		{
				self::$connection->query('CREATE TABLE FloatSetTest (foo float PRIMARY KEY, bar set<float>)');
				self::$connection->query(
						'INSERT INTO FloatSetTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => 0.2, 'bar' => [2.5, 5.2]]
				);
				$result = self::$connection->query('SELECT * FROM FloatSetTest WHERE foo = :foo', ['foo' => 0.2]);
				$this->assertFloatEquals([2.5, 5.2], $result[0]['bar']);
				$this->assertFloatEquals(0.2, $result[0]['foo']);
				//according to Spec, this should always be returned alphabetically.
				self::$connection->query(
						'INSERT INTO FloatSetTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => 2.2, 'bar' => [5.2, 2.5]]
				);
				$result = self::$connection->query('SELECT * FROM FloatSetTest WHERE foo = :foo', ['foo' => 2.2]);
				$this->assertFloatEquals([2.5, 5.2], $result[0]['bar']);
		}

		public function testFloatList()
		{
				self::$connection->query('CREATE TABLE FloatListTest (foo float PRIMARY KEY, bar list<float>)');
				self::$connection->query(
						'INSERT INTO FloatListTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => 0.2, 'bar' => [5.2, 2.5]]
				);
				$result = self::$connection->query('SELECT * FROM FloatListTest WHERE foo = :foo', ['foo' => 0.2]);
				$this->assertFloatEquals([5.2, 2.5], $result[0]['bar']);
				$this->assertFloatEquals(0.2, $result[0]['foo']);
				//according to Spec, this should be returned in index order
				self::$connection->query(
						'INSERT INTO FloatListTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => 2.2, 'bar' => [2.5, 5.2]]
				);
				$result = self::$connection->query('SELECT * FROM FloatListTest WHERE foo = :foo', ['foo' => 2.2]);
				$this->assertFloatEquals([2.5, 5.2], $result[0]['bar']);
		}

}