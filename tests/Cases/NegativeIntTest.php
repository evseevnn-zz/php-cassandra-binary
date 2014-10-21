<?php

namespace evseevnn\Cassandra\Tests;

use evseevnn\Cassandra;

class QueryNegativeIntTest extends Setup\QueryTestCase
{

		public function testIntRow()
		{
				self::$connection->query('CREATE TABLE IntTest (foo int PRIMARY KEY, bar int)');
				self::$connection->query('INSERT INTO IntTest (foo, bar) VALUES (:foo, :bar)', ['foo' => '-2', 'bar' => '-52']);
				$result = self::$connection->query('SELECT * FROM IntTest WHERE foo = :foo', ['foo' => '-2']);
				$this->assertEquals('-52', $result[0]['bar']);
				$this->assertEquals('-2', $result[0]['foo']);
		}

		public function testIntMap()
		{
				self::$connection->query('CREATE TABLE IntMapTest (foo int PRIMARY KEY, bar map<int,int>)');
				self::$connection->query(
						'INSERT INTO IntMapTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '-2', 'bar' => ['-52' => '-25']]
				);
				$result = self::$connection->query('SELECT * FROM IntMapTest WHERE foo = :foo', ['foo' => '-2']);
				$this->assertEquals(['-52' => '-25'], $result[0]['bar']);
				$this->assertEquals('-2', $result[0]['foo']);
		}

		public function testIntSet()
		{
				self::$connection->query('CREATE TABLE IntSetTest (foo int PRIMARY KEY, bar set<int>)');
				self::$connection->query(
						'INSERT INTO IntSetTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '-2', 'bar' => ['-25', '-52']]
				);
				$result = self::$connection->query('SELECT * FROM IntSetTest WHERE foo = :foo', ['foo' => '-2']);
				$this->assertEquals([-52, -25], $result[0]['bar']);
				$this->assertEquals('-2', $result[0]['foo']);
				//according to Spec, this should always be returned alphabetically.
				self::$connection->query(
						'INSERT INTO IntSetTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '-22', 'bar' => ['-52', '-25']]
				);
				$result = self::$connection->query('SELECT * FROM IntSetTest WHERE foo = :foo', ['foo' => '-22']);
				$this->assertEquals([-52, -25], $result[0]['bar']);
		}

		public function testIntList()
		{
				self::$connection->query('CREATE TABLE IntListTest (foo int PRIMARY KEY, bar list<int>)');
				self::$connection->query(
						'INSERT INTO IntListTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '-2', 'bar' => ['-52', '-25']]
				);
				$result = self::$connection->query('SELECT * FROM IntListTest WHERE foo = :foo', ['foo' => '-2']);
				$this->assertEquals(['-52', '-25'], $result[0]['bar']);
				$this->assertEquals('-2', $result[0]['foo']);
				//according to Spec, this should be returned in index order
				self::$connection->query(
						'INSERT INTO IntListTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '-22', 'bar' => ['-25', '-52']]
				);
				$result = self::$connection->query('SELECT * FROM IntListTest WHERE foo = :foo', ['foo' => '-22']);
				$this->assertEquals(['-25', '-52'], $result[0]['bar']);
		}

}