<?php

namespace evseevnn\Cassandra\Tests;
use evseevnn\Cassandra;

class QueryDecimalTest extends Setup\QueryTestCase {

    public function testDecimalColumn()
    {
        $this->markTestSkipped('Decimal is all broken');
        self::$connection->query('CREATE TABLE DecimalTest (foo decimal PRIMARY KEY, bar decimal)');
        self::$connection->query('INSERT INTO DecimalTest (foo, bar) VALUES (:foo, :bar)', ['foo' => 0.12, 'bar' => 0.23]);
        $result = self::$connection->query('SELECT * FROM DecimalTest WHERE foo = :foo', ['foo' => 0.12]);
        $this->assertEquals(0.23, $result[0]['bar']);
        $this->assertEquals(0.12, $result[0]['foo']);
    }

    public function testDecimalMap()
    {
        $this->markTestSkipped('Decimal is all broken');
        self::$connection->query('CREATE TABLE DecimalMapTest (foo decimal PRIMARY KEY, bar map<decimal,decimal>)');
        self::$connection->query('INSERT INTO DecimalMapTest (foo, bar) VALUES (:foo, :bar)', ['foo' => 0.12, 'bar' => [0.23 => 0.52]]);
        $result = self::$connection->query('SELECT * FROM DecimalMapTest WHERE foo = :foo', ['foo' => 0.12]);
        $this->assertEquals([0.23 => 0.52], $result[0]['bar']);
        $this->assertEquals(0.12, $result[0]['foo']);
    }

    public function testDecimalSet()
    {
        $this->markTestSkipped('Decimal is all broken');
        self::$connection->query('CREATE TABLE DecimalSetTest (foo decimal PRIMARY KEY, bar set<decimal>)');
        self::$connection->query('INSERT INTO DecimalSetTest (foo, bar) VALUES (:foo, :bar)', ['foo' => 0.12, 'bar' => [0.23, 0.52]]);
        $result = self::$connection->query('SELECT * FROM DecimalSetTest WHERE foo = :foo', ['foo' => 0.12]);
        $this->assertEquals([0.23, 0.52], $result[0]['bar']);
        $this->assertEquals(0.12, $result[0]['foo']);
        //according to Spec, this should always be returned alphabetically.
        self::$connection->query('INSERT INTO DecimalSetTest (foo, bar) VALUES (:foo, :bar)', ['foo' => 'baz2', 'bar' => [0.52, 0.23]]);
        $result = self::$connection->query('SELECT * FROM DecimalSetTest WHERE foo = :foo', ['foo' => 'baz2']);
        $this->assertEquals([0.23, 0.52], $result[0]['bar']);
    }

    public function testDecimalList()
    {
        $this->markTestSkipped('Decimal is all broken');
        self::$connection->query('CREATE TABLE DecimalListTest (foo decimal PRIMARY KEY, bar list<decimal>)');
        self::$connection->query('INSERT INTO DecimalListTest (foo, bar) VALUES (:foo, :bar)', ['foo' => 0.12, 'bar' => [0.23, 0.52]]);
        $result = self::$connection->query('SELECT * FROM DecimalListTest WHERE foo = :foo', ['foo' => 0.12]);
        $this->assertEquals([0.23, 0.52], $result[0]['bar']);
        $this->assertEquals(0.12, $result[0]['foo']);
        //according to Spec, this should be returned in index order
        self::$connection->query('INSERT INTO DecimalListTest (foo, bar) VALUES (:foo, :bar)', ['foo' => 'baz2', 'bar' => [0.52, 0.23]]);
        $result = self::$connection->query('SELECT * FROM DecimalListTest WHERE foo = :foo', ['foo' => 'baz2']);
        $this->assertEquals([0.52, 0.23], $result[0]['bar']);
    }

}