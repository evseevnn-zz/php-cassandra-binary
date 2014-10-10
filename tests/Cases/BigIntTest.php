<?php

namespace evseevnn\Cassandra\Tests;
use evseevnn\Cassandra;

class QueryBigIntTest extends Setup\QueryTestCase {

    public function testBigIntRow()
    {
        self::$connection->query('CREATE TABLE BigIntTest (foo bigint PRIMARY KEY, bar bigint)');
        self::$connection->query('INSERT INTO BigIntTest (foo, bar) VALUES (:foo, :bar)', ['foo' => '2', 'bar' => '52']);
        $result = self::$connection->query('SELECT * FROM BigIntTest WHERE foo = :foo', ['foo' => '2']);
        $this->assertEquals('52', $result[0]['bar']);
        $this->assertEquals('2', $result[0]['foo']);
    }

    public function testBigIntMap()
    {
        self::$connection->query('CREATE TABLE BigIntMapTest (foo bigint PRIMARY KEY, bar map<bigint,bigint>)');
        self::$connection->query('INSERT INTO BigIntMapTest (foo, bar) VALUES (:foo, :bar)', ['foo' => '2', 'bar' => ['52' => '25']]);
        $result = self::$connection->query('SELECT * FROM BigIntMapTest WHERE foo = :foo', ['foo' => '2']);
        $this->assertEquals(['52' => '25'], $result[0]['bar']);
        $this->assertEquals('2', $result[0]['foo']);
    }

    public function testBigIntSet()
    {
        self::$connection->query('CREATE TABLE BigIntSetTest (foo bigint PRIMARY KEY, bar set<bigint>)');
        self::$connection->query('INSERT INTO BigIntSetTest (foo, bar) VALUES (:foo, :bar)', ['foo' => '2', 'bar' => ['25', '52']]);
        $result = self::$connection->query('SELECT * FROM BigIntSetTest WHERE foo = :foo', ['foo' => '2']);
        $this->assertEquals([25, 52], $result[0]['bar']);
        $this->assertEquals('2', $result[0]['foo']);
        //according to Spec, this should always be returned alphabetically.
        self::$connection->query('INSERT INTO BigIntSetTest (foo, bar) VALUES (:foo, :bar)', ['foo' => '22', 'bar' => ['52', '25']]);
        $result = self::$connection->query('SELECT * FROM BigIntSetTest WHERE foo = :foo', ['foo' => '22']);
        $this->assertEquals([25, 52], $result[0]['bar']);
    }

    public function testBigIntList()
    {
        self::$connection->query('CREATE TABLE BigIntListTest (foo bigint PRIMARY KEY, bar list<bigint>)');
        self::$connection->query('INSERT INTO BigIntListTest (foo, bar) VALUES (:foo, :bar)', ['foo' => '2', 'bar' => ['52', '25']]);
        $result = self::$connection->query('SELECT * FROM BigIntListTest WHERE foo = :foo', ['foo' => '2']);
        $this->assertEquals(['52', '25'], $result[0]['bar']);
        $this->assertEquals('2', $result[0]['foo']);
        //according to Spec, this should be returned in index order
        self::$connection->query('INSERT INTO BigIntListTest (foo, bar) VALUES (:foo, :bar)', ['foo' => '22', 'bar' => ['25', '52']]);
        $result = self::$connection->query('SELECT * FROM BigIntListTest WHERE foo = :foo', ['foo' => '22']);
        $this->assertEquals(['25', '52'], $result[0]['bar']);
    }

}