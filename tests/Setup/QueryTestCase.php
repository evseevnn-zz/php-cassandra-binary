<?php
/**
 * Created by IntelliJ IDEA.
 * User: lfronius
 * Date: 09/10/14
 * Time: 16:59
 * To change this template use File | Settings | File Templates.
 */

namespace evseevnn\Cassandra\Tests\Setup;

use evseevnn\Cassandra;

abstract class QueryTestCase extends \PHPUnit_Framework_TestCase
{

		protected static $connection;

		public static function setUpBeforeClass()
		{
				self::$connection = new Cassandra\Database(['127.0.0.1:9042']);
				self::$connection->connect();
				self::$connection->query("DROP KEYSPACE IF EXISTS testkeyspace;");
				self::$connection->query(
						"CREATE KEYSPACE testkeyspace WITH replication = {   'class': 'SimpleStrategy',   'replication_factor': '1' };"
				);
				self::$connection->query("USE testkeyspace;");
		}

		public static function tearDownAfterClass()
		{
				self::$connection->query("DROP KEYSPACE testkeyspace;");
		}

	    public static function assertFloatEquals($expected, $actual)
	    {
		        self::assertEquals($expected, $actual, '', 0.00001);
	    }

}