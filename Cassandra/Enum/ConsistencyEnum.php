<?php
namespace Cassandra\Enum;

class ConsistencyEnum {
	const ANY = 0x0000;
	const ONE = 0x0001;
	const TWO = 0x0002;
	const THREE = 0x0003;
	const QUORUM = 0x0004;
	const ALL = 0x0005;
	const LOCAL_QUORUM = 0x0006;
	const EACH_QUORUM = 0x0007;
	const LOCAL_ONE = 0x0010;
}