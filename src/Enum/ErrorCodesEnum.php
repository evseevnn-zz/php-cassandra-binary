<?php
namespace markdunphy\Cassandra\Enum;

class ErrorCodesEnum {
	const SERVER_ERROR = 0x0000;
	const PROTOCOL_ERROR = 0x000A;
	const BAD_CREDENTIALS = 0x0100;
	const UNAVAILABLE_EXCEPTION = 0x1000;
	const OVERLOADED = 0x1001;
	const IS_BOOTSTRAPPING = 0x1002;
	const TRUNCATE_ERROR = 0x1003;
	const WRITE_TIMEOUT = 0x1100;
	const READ_TIMEOUT = 0x1200;
	const SYNTAX_ERROR = 0x2000;
	const UNAUTHORIZED = 0x2100;
	const INVALID = 0x2200;
	const CONFIG_ERROR = 0x2300;
	const ALREADY_EXIST = 0x2400;
	const UNPREPARED = 0x2500;
}