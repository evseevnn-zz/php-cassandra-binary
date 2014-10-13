<?php

namespace evseevnn\Cassandra\Tests;

use evseevnn\Cassandra;

class QueryDoubleTest extends Setup\QueryTestCase
{

		public function testDoubleRow()
		{
				self::$connection->query('CREATE TABLE DoubleTest (foo double PRIMARY KEY, bar double)');
				self::$connection->query(
						'INSERT INTO DoubleTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '2', 'bar' => '52']
				);
				$result = self::$connection->query('SELECT * FROM DoubleTest WHERE foo = :foo', ['foo' => '2']);
				$this->assertEquals('52', $result[0]['bar']);
				$this->assertEquals('2', $result[0]['foo']);
		}

		public function testDoubleMap()
		{
				self::$connection->query('CREATE TABLE DoubleMapTest (foo double PRIMARY KEY, bar map<double,double>)');
				self::$connection->query(
						'INSERT INTO DoubleMapTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '2', 'bar' => ['52' => '25']]
				);
				$result = self::$connection->query('SELECT * FROM DoubleMapTest WHERE foo = :foo', ['foo' => '2']);
				$this->assertEquals(['52' => '25'], $result[0]['bar']);
				$this->assertEquals('2', $result[0]['foo']);
		}

		public function testDoubleSet()
		{
				self::$connection->query('CREATE TABLE DoubleSetTest (foo double PRIMARY KEY, bar set<double>)');
				self::$connection->query(
						'INSERT INTO DoubleSetTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '2', 'bar' => ['25', '52']]
				);
				$result = self::$connection->query('SELECT * FROM DoubleSetTest WHERE foo = :foo', ['foo' => '2']);
				$this->assertEquals([25, 52], $result[0]['bar']);
				$this->assertEquals('2', $result[0]['foo']);
				//according to Spec, this should always be returned alphabetically.
				self::$connection->query(
						'INSERT INTO DoubleSetTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '22', 'bar' => ['52', '25']]
				);
				$result = self::$connection->query('SELECT * FROM DoubleSetTest WHERE foo = :foo', ['foo' => '22']);
				$this->assertEquals([25, 52], $result[0]['bar']);
		}

		public function testDoubleList()
		{
				self::$connection->query('CREATE TABLE DoubleListTest (foo double PRIMARY KEY, bar list<double>)');
				self::$connection->query(
						'INSERT INTO DoubleListTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '2', 'bar' => ['52', '25']]
				);
				$result = self::$connection->query('SELECT * FROM DoubleListTest WHERE foo = :foo', ['foo' => '2']);
				$this->assertEquals(['52', '25'], $result[0]['bar']);
				$this->assertEquals('2', $result[0]['foo']);
				//according to Spec, this should be returned in index order
				self::$connection->query(
						'INSERT INTO DoubleListTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '22', 'bar' => ['25', '52']]
				);
				$result = self::$connection->query('SELECT * FROM DoubleListTest WHERE foo = :foo', ['foo' => '22']);
				$this->assertEquals(['25', '52'], $result[0]['bar']);
		}

}