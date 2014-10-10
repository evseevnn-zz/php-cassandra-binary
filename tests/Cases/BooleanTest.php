<?php

namespace evseevnn\Cassandra\Tests;
use evseevnn\Cassandra;

class QueryBooleanTest extends Setup\QueryTestCase {

    public function testBooleanColumn()
    {
        self::$connection->query('CREATE TABLE BooleanTest (foo boolean PRIMARY KEY, bar boolean)');
        self::$connection->query('INSERT INTO BooleanTest (foo, bar) VALUES (:foo, :bar)', ['foo' => true, 'bar' => false]);
        $result = self::$connection->query('SELECT * FROM BooleanTest WHERE foo = :foo', ['foo' => true]);
        $this->assertEquals(false, $result[0]['bar']);
        $this->assertEquals(true, $result[0]['foo']);
    }

    public function testBooleanMap()
    {
        self::$connection->query('CREATE TABLE BooleanMapTest (foo boolean PRIMARY KEY, bar map<boolean,boolean>)');
        self::$connection->query('INSERT INTO BooleanMapTest (foo, bar) VALUES (:foo, :bar)', ['foo' => true, 'bar' => [false => true]]);
        $result = self::$connection->query('SELECT * FROM BooleanMapTest WHERE foo = :foo', ['foo' => true]);
        $this->assertEquals([false => true], $result[0]['bar']);
        $this->assertEquals(true, $result[0]['foo']);
    }

    public function testBooleanSet()
    {
        self::$connection->query('CREATE TABLE BooleanSetTest (foo boolean PRIMARY KEY, bar set<boolean>)');
        self::$connection->query('INSERT INTO BooleanSetTest (foo, bar) VALUES (:foo, :bar)', ['foo' => true, 'bar' => [true, false]]);
        $result = self::$connection->query('SELECT * FROM BooleanSetTest WHERE foo = :foo', ['foo' => true]);
        $this->assertEquals([false, true], $result[0]['bar']);
        $this->assertEquals(true, $result[0]['foo']);
        //according to Spec, this should always be returned alphabetically.
        self::$connection->query('INSERT INTO BooleanSetTest (foo, bar) VALUES (:foo, :bar)', ['foo' => true, 'bar' => [false, true]]);
        $result = self::$connection->query('SELECT * FROM BooleanSetTest WHERE foo = :foo', ['foo' => true]);
        $this->assertEquals([false, true], $result[0]['bar']);
    }

    public function testBooleanList()
    {
        self::$connection->query('CREATE TABLE BooleanListTest (foo boolean PRIMARY KEY, bar list<boolean>)');
        self::$connection->query('INSERT INTO BooleanListTest (foo, bar) VALUES (:foo, :bar)', ['foo' => true, 'bar' => [false, true]]);
        $result = self::$connection->query('SELECT * FROM BooleanListTest WHERE foo = :foo', ['foo' => true]);
        $this->assertEquals([false, true], $result[0]['bar']);
        $this->assertEquals(true, $result[0]['foo']);
        //according to Spec, this should be returned in index order - seems for booleans it just doesn't care
        self::$connection->query('INSERT INTO BooleanListTest (foo, bar) VALUES (:foo, :bar)', ['foo' => false, 'bar' => [true, false]]);
        $result = self::$connection->query('SELECT * FROM BooleanListTest WHERE foo = :foo', ['foo' => false]);
        $this->assertEquals([false, true], $result[0]['bar']);
    }

}