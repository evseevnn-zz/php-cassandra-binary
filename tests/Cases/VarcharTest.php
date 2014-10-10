<?php

namespace evseevnn\Cassandra\Tests;
use evseevnn\Cassandra;

class QueryVarcharTest extends Setup\QueryTestCase {

    public function testVarcharColumn()
    {
        self::$connection->query('CREATE TABLE VarcharTest (foo varchar PRIMARY KEY, bar varchar)');
        self::$connection->query('INSERT INTO VarcharTest (foo, bar) VALUES (:foo, :bar)', ['foo' => 'baz', 'bar' => 'barbaz']);
        $result = self::$connection->query('SELECT * FROM VarcharTest WHERE foo = :foo', ['foo' => 'baz']);
        $this->assertEquals('barbaz', $result[0]['bar']);
        $this->assertEquals('baz', $result[0]['foo']);
    }

    public function testVarcharMap()
    {
        self::$connection->query('CREATE TABLE VarcharMapTest (foo varchar PRIMARY KEY, bar map<varchar,varchar>)');
        self::$connection->query('INSERT INTO VarcharMapTest (foo, bar) VALUES (:foo, :bar)', ['foo' => 'baz', 'bar' => ['barbaz' => 'bazbar']]);
        $result = self::$connection->query('SELECT * FROM VarcharMapTest WHERE foo = :foo', ['foo' => 'baz']);
        $this->assertEquals(['barbaz' => 'bazbar'], $result[0]['bar']);
        $this->assertEquals('baz', $result[0]['foo']);
    }

    public function testVarcharSet()
    {
        self::$connection->query('CREATE TABLE VarcharSetTest (foo varchar PRIMARY KEY, bar set<varchar>)');
        self::$connection->query('INSERT INTO VarcharSetTest (foo, bar) VALUES (:foo, :bar)', ['foo' => 'baz', 'bar' => ['barbaz', 'bazbar']]);
        $result = self::$connection->query('SELECT * FROM VarcharSetTest WHERE foo = :foo', ['foo' => 'baz']);
        $this->assertEquals(['barbaz', 'bazbar'], $result[0]['bar']);
        $this->assertEquals('baz', $result[0]['foo']);
        //according to Spec, this should always be returned alphabetically.
        self::$connection->query('INSERT INTO VarcharSetTest (foo, bar) VALUES (:foo, :bar)', ['foo' => 'baz2', 'bar' => ['bazbar', 'barbaz']]);
        $result = self::$connection->query('SELECT * FROM VarcharSetTest WHERE foo = :foo', ['foo' => 'baz2']);
        $this->assertEquals(['barbaz', 'bazbar'], $result[0]['bar']);
    }

    public function testVarcharList()
    {
        self::$connection->query('CREATE TABLE VarcharListTest (foo varchar PRIMARY KEY, bar list<varchar>)');
        self::$connection->query('INSERT INTO VarcharListTest (foo, bar) VALUES (:foo, :bar)', ['foo' => 'baz', 'bar' => ['barbaz', 'bazbar']]);
        $result = self::$connection->query('SELECT * FROM VarcharListTest WHERE foo = :foo', ['foo' => 'baz']);
        $this->assertEquals(['barbaz', 'bazbar'], $result[0]['bar']);
        $this->assertEquals('baz', $result[0]['foo']);
        //according to Spec, this should be returned in index order
        self::$connection->query('INSERT INTO VarcharListTest (foo, bar) VALUES (:foo, :bar)', ['foo' => 'baz2', 'bar' => ['bazbar', 'barbaz']]);
        $result = self::$connection->query('SELECT * FROM VarcharListTest WHERE foo = :foo', ['foo' => 'baz2']);
        $this->assertEquals(['bazbar', 'barbaz'], $result[0]['bar']);
    }

}