<?php
namespace evseevnn\Cassandra\Protocol\Response;

use evseevnn\Cassandra\Enum\DataTypeEnum;

class DataStream {

	/**
	 * @var string
	 */
	private $data;

	/**
	 * @var int
	 */
	private $length;

	/**
	 * @param string $binary
	 */
	public function __construct($binary) {
		$this->data = $binary;
		$this->length = strlen($binary);
	}


	/**
	 * Read data from stream.
	 *
	 * @param int $length
	 * @throws \Exception
	 * @return string
	 */
	protected function read($length) {
		if ($this->length < $length) {
			throw new \Exception('Reading while at end of stream');
		}
		$output = substr($this->data, 0, $length);
		$this->data = substr($this->data, $length);
		$this->length -= $length;
		return $output;
	}

	/**
	 * Read single character.
	 *
	 * @return int
	 */
	public function readChar() {
		return unpack('C', $this->read(1))[1];
	}

	/**
	 * Read unsigned short.
	 *
	 * @return int
	 */
	public function readShort() {
		return unpack('n', $this->read(2))[1];
	}

	/**
	 * Read unsigned int.
	 *
	 * @return int
	 */
    public function readInt($isCollectionElement = false) {
        if ($isCollectionElement) {
            $length = $this->readShort();
            return unpack('N', $this->read($length))[1];
        }
        return unpack('N', $this->read(4))[1];
    }

	/**
  	 * Read unsigned big int;
  	 *
  	 * @return int;
  	 */
 	function readBigInt() {
   		$data = $this->data;
   		$arr = unpack('N2', $data);

		if (PHP_INT_SIZE == 4) {
    		$hi = $arr[1];
	    	$lo = $arr[2];
    		$isNeg = $hi  < 0;

	    	// Check for a negative
     		if ($isNeg) {
       			$hi = ~$hi & (int)0xffffffff;
       			$lo = ~$lo & (int)0xffffffff;

	       		if ($lo == (int)0xffffffff) {	
    	     		$hi++;
        	 		$lo = 0;
       			} else {
         			$lo++;
       			}
     		}

     		// Force 32bit words in excess of 2G to pe positive - we deal wigh sign
     		// explicitly below
     		if ($hi & (int)0x80000000) {
       			$hi &= (int)0x7fffffff;
       			$hi += 0x80000000;
     		}

    		if ($lo & (int)0x80000000) {
      			$lo &= (int)0x7fffffff;
      			$lo += 0x80000000;
    		}

     		$value = $hi * 4294967296 + $lo;

     		if ($isNeg) {
       			$value = 0 - $value;
     		}
   		} else {
     		if ($arr[2] & 0x80000000) {
       			$arr[2] = $arr[2] & 0xffffffff;
     		}

     		if ($arr[1] & 0x80000000) {
       			$arr[1] = $arr[1] & 0xffffffff;
       			$arr[1] = $arr[1] ^ 0xffffffff;
       			$arr[2] = $arr[2] ^ 0xffffffff;
       			$value = 0 - $arr[1]*4294967296 - $arr[2] - 1;
     		} else {
       			$value = $arr[1]*4294967296 + $arr[2];
     		}
   		}

		return $value;
 	}

	/**
	 * Read string.
	 *
	 * @return string
	 */
	public function readString() {
		$length = $this->readShort();
		return $this->read($length);
	}

	/**
	 * Read long string.
	 *
	 * @return string
	 */
	public function readLongString() {
		$length = $this->readInt();
		return $this->read($length);
	}

	/**
	 * Read bytes.
	 *
	 * @return string
	 */
	public function readBytes() {
		$length = $this->readInt();
		return $this->read($length);
	}

	/**
	 * Read uuid.
	 *
	 * @return string
	 */
	public function readUuid() {
		$uuid = '';
		$data = $this->read(16);

		for ($i = 0; $i < 16; ++$i) {
			if ($i == 4 || $i == 6 || $i == 8 || $i == 10) {
				$uuid .= '-';
			}
			$uuid .= str_pad(dechex(ord($data{$i})), 2, '0', STR_PAD_LEFT);
		}

		return $uuid;
	}

	/**
	 * Read timestamp.
	 *
	 * Cassandra is using the default java date representation, which is the
	 * milliseconds since epoch. Since we cannot use 64 bits integers without
	 * extra libraries, we are reading this as two 32 bits numbers and calculate
	 * the seconds since epoch.
	 *
	 * @return int
	 */
	public function readTimestamp() {
		return round($this->readInt() * 4294967.296 + ($this->readInt() / 1000));
	}

	/**
	 * Read list.
	 *
	 * @param $valueType
	 * @return array
	 */
	public function readList($valueType) {
		$list = array();
		$count = $this->readShort();
		for ($i = 0; $i < $count; ++$i) {
			$list[] = $this->readByType($valueType, true);
		}
		return $list;
	}

	/**
	 * Read map.
	 *
	 * @param $keyType
	 * @param $valueType
	 * @return array
	 */
	public function readMap($keyType, $valueType) {
		$map = array();
		$count = $this->readShort();
		for ($i = 0; $i < $count; ++$i) {
			$map[$this->readByType($keyType, true)] = $this->readByType($valueType, true);
		}
		return $map;
	}

	/**
	 * Read float.
	 *
	 * @return float
	 */
	public function readFloat() {
		return unpack('f', strrev($this->read(4)))[1];
	}

	/**
	 * Read double.
	 *
	 * @return double
	 */
	public function readDouble() {
		return unpack('d', strrev($this->read(8)))[1];
	}

	/**
	 * Read boolean.
	 *
	 * @return bool
	 */
	public function readBoolean() {
		return (bool)$this->readChar();
	}

	/**
	 * Read inet.
	 *
	 * @return string
	 */
	public function readInet() {
		return inet_ntop($this->data);
	}

	/**
	 * Read variable length integer.
	 *
	 * @return string
	 */
	public function readVarint() {
		list($higher, $lower) = array_values(unpack('N2', $this->data));
		return $higher << 32 | $lower;
	}

	/**
	 * Read variable length decimal.
	 *
	 * @return string
	 */
	public function readDecimal() {
		$scale = $this->readInt();
		$value = $this->readVarint();
		$len = strlen($value);
		return substr($value, 0, $len - $scale) . '.' . substr($value, $len - $scale);
	}

	/**
	 * @param array $type
	 * @param bool $isCollectionElement for collection element used other alg. a temporary solution
	 * @return mixed
	 */
	public function readByType(array $type, $isCollectionElement = false) {
		switch ($type['type']) {
			case DataTypeEnum::ASCII:
			case DataTypeEnum::VARCHAR:
			case DataTypeEnum::TEXT:
				return $isCollectionElement ? $this->readString() : $this->data;
			case DataTypeEnum::BIGINT:
        		return $this->readBigInt();
			case DataTypeEnum::COUNTER:
			case DataTypeEnum::VARINT:
				return $this->readVarint();
			case DataTypeEnum::CUSTOM:
			case DataTypeEnum::BLOB:
				return $this->readBytes();
			case DataTypeEnum::BOOLEAN:
				return $this->readBoolean();
			case DataTypeEnum::DECIMAL:
				return $this->readDecimal();
			case DataTypeEnum::DOUBLE:
				return $this->readDouble();
			case DataTypeEnum::FLOAT:
				return $this->readFloat();
			case DataTypeEnum::INT:
				return $this->readInt($isCollectionElement);
			case DataTypeEnum::TIMESTAMP:
				return $this->readTimestamp();
			case DataTypeEnum::UUID:
				return $this->readUuid();
			case DataTypeEnum::TIMEUUID:
				return $this->readUuid();
			case DataTypeEnum::INET:
				return $this->readInet();
			case DataTypeEnum::COLLECTION_LIST:
			case DataTypeEnum::COLLECTION_SET:
				return $this->readList($type['value']);
			case DataTypeEnum::COLLECTION_MAP:
				return $this->readMap($type['key'], $type['value']);
		}

		trigger_error('Unknown type ' . var_export($type, true));
		return null;
	}
}