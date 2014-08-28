<?php
namespace markdunphy\Cassandra\Protocol\Response\DataStream;
use markdunphy\Cassandra\Enum\DataTypeEnum;
use markdunphy\Cassandra\Protocol\Response\DataStream;

final class TypeReader {

	/**
	 * @param DataStream $stream
	 * @return mixed
	 */
	public static function readFromStream(DataStream $stream) {
		$data = [
			'type' => $stream->readShort()
		];
		switch ($data['type']) {
			case DataTypeEnum::CUSTOM:
				$data['name'] = $stream->readString();
				break;
			case DataTypeEnum::COLLECTION_LIST:
			case DataTypeEnum::COLLECTION_SET:
				$data['value'] = self::readFromStream($stream);
				break;
			case DataTypeEnum::COLLECTION_MAP:
				$data['key'] = self::readFromStream($stream);
				$data['value'] = self::readFromStream($stream);
				break;
		}
		return $data;
	}

}