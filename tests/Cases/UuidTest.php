<?php

namespace evseevnn\Cassandra\Tests;

use evseevnn\Cassandra;

class QueryUuidTest extends Setup\QueryTestCase
{

		public function testUuidColumn()
		{
				self::$connection->query('CREATE TABLE UuidTest (foo uuid PRIMARY KEY, bar uuid)');
				self::$connection->query(
						'INSERT INTO UuidTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '456e4567-e89b-12d3-a456-426655440000', 'bar' => 'abce4567-e89b-12d3-a456-426655440000']
				);
				$result = self::$connection->query('SELECT * FROM UuidTest WHERE foo = :foo', ['foo' => '456e4567-e89b-12d3-a456-426655440000']);
				$this->assertEquals('abce4567-e89b-12d3-a456-426655440000', $result[0]['bar']);
				$this->assertEquals('456e4567-e89b-12d3-a456-426655440000', $result[0]['foo']);
		}

		public function testUuidMap()
		{
				self::$connection->query('CREATE TABLE UuidMapTest (foo uuid PRIMARY KEY, bar map<uuid,uuid>)');
				self::$connection->query(
						'INSERT INTO UuidMapTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '456e4567-e89b-12d3-a456-426655440000', 'bar' => ['abce4567-e89b-12d3-a456-426655440000' => 'cdfe4567-e89b-12d3-a456-426655440000']]
				);
				$result = self::$connection->query('SELECT * FROM UuidMapTest WHERE foo = :foo', ['foo' => '456e4567-e89b-12d3-a456-426655440000']);
				$this->assertEquals(['abce4567-e89b-12d3-a456-426655440000' => 'cdfe4567-e89b-12d3-a456-426655440000'], $result[0]['bar']);
				$this->assertEquals('456e4567-e89b-12d3-a456-426655440000', $result[0]['foo']);
		}

		public function testUuidSet()
		{
				self::$connection->query('CREATE TABLE UuidSetTest (foo uuid PRIMARY KEY, bar set<uuid>)');
				self::$connection->query(
						'INSERT INTO UuidSetTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '456e4567-e89b-12d3-a456-426655440000', 'bar' => ['abce4567-e89b-12d3-a456-426655440000', 'cdfe4567-e89b-12d3-a456-426655440000']]
				);
				$result = self::$connection->query('SELECT * FROM UuidSetTest WHERE foo = :foo', ['foo' => '456e4567-e89b-12d3-a456-426655440000']);
				$this->assertEquals(['abce4567-e89b-12d3-a456-426655440000', 'cdfe4567-e89b-12d3-a456-426655440000'], $result[0]['bar']);
				$this->assertEquals('456e4567-e89b-12d3-a456-426655440000', $result[0]['foo']);
				//according to Spec, this should always be returned alphabetically.
				self::$connection->query(
						'INSERT INTO UuidSetTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '123e4567-e89b-12d3-a456-426655440000', 'bar' => ['cdfe4567-e89b-12d3-a456-426655440000', 'abce4567-e89b-12d3-a456-426655440000']]
				);
				$result = self::$connection->query('SELECT * FROM UuidSetTest WHERE foo = :foo', ['foo' => '123e4567-e89b-12d3-a456-426655440000']);
				$this->assertEquals(['abce4567-e89b-12d3-a456-426655440000', 'cdfe4567-e89b-12d3-a456-426655440000'], $result[0]['bar']);
		}

		public function testUuidList()
		{
				self::$connection->query('CREATE TABLE UuidListTest (foo uuid PRIMARY KEY, bar list<uuid>)');
				self::$connection->query(
						'INSERT INTO UuidListTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '456e4567-e89b-12d3-a456-426655440000', 'bar' => ['abce4567-e89b-12d3-a456-426655440000', 'cdfe4567-e89b-12d3-a456-426655440000']]
				);
				$result = self::$connection->query('SELECT * FROM UuidListTest WHERE foo = :foo', ['foo' => '456e4567-e89b-12d3-a456-426655440000']);
				$this->assertEquals(['abce4567-e89b-12d3-a456-426655440000', 'cdfe4567-e89b-12d3-a456-426655440000'], $result[0]['bar']);
				$this->assertEquals('456e4567-e89b-12d3-a456-426655440000', $result[0]['foo']);
				//according to Spec, this should be returned in index order
				self::$connection->query(
						'INSERT INTO UuidListTest (foo, bar) VALUES (:foo, :bar)',
						['foo' => '123e4567-e89b-12d3-a456-426655440000', 'bar' => ['cdfe4567-e89b-12d3-a456-426655440000', 'abce4567-e89b-12d3-a456-426655440000']]
				);
				$result = self::$connection->query('SELECT * FROM UuidListTest WHERE foo = :foo', ['foo' => '123e4567-e89b-12d3-a456-426655440000']);
				$this->assertEquals(['cdfe4567-e89b-12d3-a456-426655440000', 'abce4567-e89b-12d3-a456-426655440000'], $result[0]['bar']);
		}

}