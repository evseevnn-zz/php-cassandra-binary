<?php

namespace evseevnn\Cassandra\Tests;

use evseevnn\Cassandra;

class QueryCounterTest extends Setup\QueryTestCase
{
    public function testCounterIncreaseDecrease()
    {
        self::$connection->query('CREATE TABLE CounterTest (row_key text, test_counter counter, PRIMARY KEY (row_key));');

        self::$connection->query(
            'UPDATE CounterTest SET "test_counter" = "test_counter" + :increase_value where "row_key" = :row_key',
            ['increase_value' => 1000, 'row_key' => 'KEY']
        );
        $result = self::$connection->query(
            'SELECT * FROM CounterTest WHERE row_key = :row_key', ['row_key' => 'KEY']
        );
        $this->assertFloatEquals(1000, $result[0]['test_counter']);

        self::$connection->query(
            'UPDATE CounterTest SET "test_counter" = "test_counter" + :increase_value where "row_key" = :row_key',
            ['increase_value' => -2000, 'row_key' => 'KEY']
        );

        $result = self::$connection->query(
            'SELECT * FROM CounterTest WHERE row_key = :row_key', ['row_key' => 'KEY']
        );
        $this->assertFloatEquals(-1000, $result[0]['test_counter']);
    }
}
