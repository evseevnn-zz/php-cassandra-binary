PHP library for Cassandra
=========================

[![Build Status](https://travis-ci.org/LarsFronius/php-cassandra-binary.svg?branch=master)](https://travis-ci.org/LarsFronius/php-cassandra-binary)
<a href="https://codeclimate.com/github/evseevnn/php-cassandra-binary"><img src="https://codeclimate.com/github/evseevnn/php-cassandra-binary.png" /></a>
<a href="https://scrutinizer-ci.com/g/evseevnn/php-cassandra-binary/"><img src="https://scrutinizer-ci.com/g/evseevnn/php-cassandra-binary/badges/quality-score.png?b=master" /></a>
<a href="https://scrutinizer-ci.com/g/evseevnn/php-cassandra-binary/"><img src="https://scrutinizer-ci.com/g/evseevnn/php-cassandra-binary/badges/build.png?b=master" /></a>


Cassandra client library for PHP, using the native binary protocol.

## Roadmap for version 0.2.0
* UUID generation
* timestamp only with microsecond
* using v2 protocol
* speedup
* the ability to specify the settings (setup default consistency level and more)
* more fixes

## Known issues
* decimal and timestamps have bugs especially in collections (map,set,list)
* connection handling e.g. timeouts

## New feature for this fork
* on Database class constructor, now you can choose whether you want to connect to your nodes randomically or sequentially

## Installation

PHP 5.4+ is required. There is no need for additional libraries.
PHP Sockets extension is required to use Cassandra's binary protocol.

Append dependency into composer.json

```
	...
	"require": {
		...
		"evseevnn/php-cassandra-binary": "dev-master"
	}
	...
```

## Base Using

```php
<?php

$nodes = [
	'127.0.0.1',
	'192.168.0.2:8882' => [
		'username' => 'admin',
		'password' => 'pass'
	]
];

// Connect to database.
$database = new evseevnn\Cassandra\Database($nodes, 'my_keyspace');
$database->connect();

// Run query.
$users = $database->query('SELECT * FROM "users" WHERE "id" = :id', ['id' => 'c5420d81-499e-4c9c-ac0c-fa6ba3ebc2bc']);

var_dump($users);
/*
	result:
		array(
			[0] => array(
				'id' => 'c5420d81-499e-4c9c-ac0c-fa6ba3ebc2bc',
				'name' => 'userName',
				'email' => 'user@email.com'
			)
		)
*/

// Keyspace can be changed at runtime
$database->setKeyspace('my_other_keyspace');
// Get from other keyspace
$urlsFromFacebook = $database->query('SELECT * FROM "urls" WHERE "host" = :host', ['host' => 'facebook.com']);

```

## Using transaction

```php
	$database->beginBatch();
	// all INSERT, UPDATE, DELETE query append into batch query stack for execution after applyBatch
	$uuid = $database->query('SELECT uuid() as "uuid" FROM system.schema_keyspaces LIMIT 1;')[0]['uuid'];
	$database->query(
			'INSERT INTO "users" ("id", "name", "email") VALUES (:id, :name, :email);',
			[
				'id' => $uuid,
				'name' => 'Mark',
				'email' => 'mark@facebook.com'
			]
		);

	$database->query(
			'DELETE FROM "users" WHERE "email" = :email;',
			[
				'email' => 'durov@vk.com'
			]
		);
	$result = $database->applyBatch();
```

## Supported datatypes

All types are supported.

* *ascii, varchar, text*
  Result will be a string.
* *bigint, counter, varint*
  Converted to strings using bcmath.
* *blob*
  Result will be a string.
* *boolean*
  Result will be a boolean as well.
* *decimal*
  Converted to strings using bcmath.
* *double, float, int*
  Result is using native PHP datatypes.
* *timestamp*
  Converted to integer. Milliseconds precision is lost.
* *uuid, timeuuid, inet*
  No native PHP datatype available. Converted to strings.
* *list, set*
  Converted to array (numeric keys).
* *map*
  Converted to keyed array.
