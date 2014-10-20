<?php

namespace evseevnn\Cassandra\Tests;

use evseevnn\Cassandra;

class QueryBlobTest extends Setup\QueryTestCase
{

		public function testBlobColumn()
		{
				self::$connection->query('CREATE TABLE BlobTest (foo blob PRIMARY KEY, bar blob)');
				self::$connection->query('INSERT INTO BlobTest (foo, bar) VALUES (:foo, :bar)', ['foo' => '2', 'bar' => '52']);
				$result = self::$connection->query('SELECT * FROM BlobTest WHERE foo = :foo', ['foo' => '2']);
				$this->assertEquals('52', $result[0]['bar']);
				$this->assertEquals('2', $result[0]['foo']);
		}

		public function testBlobMap()
		{
				self::$connection->query('CREATE TABLE BlobMapTest (foo blob PRIMARY KEY, bar map<blob,blob>)');
				self::$connection->query(
						'INSERT INTO BlobMapTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '2', 'bar' => ['52' => '25']]
				);
				$result = self::$connection->query('SELECT * FROM BlobMapTest WHERE foo = :foo', ['foo' => '2']);
				$this->assertEquals(['52' => '25'], $result[0]['bar']);
				$this->assertEquals('2', $result[0]['foo']);
		}

		public function testBlobSet()
		{
				self::$connection->query('CREATE TABLE BlobSetTest (foo blob PRIMARY KEY, bar set<blob>)');
				self::$connection->query(
						'INSERT INTO BlobSetTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '2', 'bar' => ['25', '52']]
				);
				$result = self::$connection->query('SELECT * FROM BlobSetTest WHERE foo = :foo', ['foo' => '2']);
				$this->assertEquals([25, 52], $result[0]['bar']);
				$this->assertEquals('2', $result[0]['foo']);
				//according to Spec, this should always be returned alphabetically.
				self::$connection->query(
						'INSERT INTO BlobSetTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '22', 'bar' => ['52', '25']]
				);
				$result = self::$connection->query('SELECT * FROM BlobSetTest WHERE foo = :foo', ['foo' => '22']);
				$this->assertEquals([25, 52], $result[0]['bar']);
		}

		public function testBlobList()
		{
				self::$connection->query('CREATE TABLE BlobListTest (foo blob PRIMARY KEY, bar list<blob>)');
				self::$connection->query(
						'INSERT INTO BlobListTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '2', 'bar' => ['52', '25']]
				);
				$result = self::$connection->query('SELECT * FROM BlobListTest WHERE foo = :foo', ['foo' => '2']);
				$this->assertEquals(['52', '25'], $result[0]['bar']);
				$this->assertEquals('2', $result[0]['foo']);
				//according to Spec, this should be returned in index order
				self::$connection->query(
						'INSERT INTO BlobListTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '22', 'bar' => ['25', '52']]
				);
				$result = self::$connection->query('SELECT * FROM BlobListTest WHERE foo = :foo', ['foo' => '22']);
				$this->assertEquals(['25', '52'], $result[0]['bar']);
		}

}