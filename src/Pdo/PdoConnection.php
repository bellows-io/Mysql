<?php

namespace Mysql\Pdo;

use \PDO;
use \Mysql\ConnectionInterface;

class PdoConnection implements ConnectionInterface {

	protected $handle;
	protected $lastQuery;
	protected $responseFactory;

	public function __construct(PDO $handle, PdoResponseFactory $responseFactory) {
		$this->handle = $handle;
		$this->responseFactory = $responseFactory;
	}

	public function prepare($sql, array $data = array()) {
		$statement = $this->handle->prepare($sql);
		$this->lastQuery = $sql;
		if ($arguments) {
			$bindNames = isset($arguments[0]);
			$i = 0;
			foreach ($arguments as $name => $value) {
				$valueType = is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR;
				$statement->bindParam($bindNames ? $name : ++$i, $value, $valueType);
			}
		}
		return $this->responseFactory->make($statement);
	}

	public function insert($table, array $data) {
		$this->validateTableName($table);

		$keys = array_keys($data);
		$sql = "INSERT INTO `$table`
					(`".implode('`,`', $keys)."')
					VALUES (:".implode(', :', $keys).')';

		return $this->prepare($sql, $data)->count();
	}

	public function update($table, array $data, array $match) {
		$this->validateTableName($table);

		$keys = array_keys($data);
		$sql = "UPDATE `$table`";
		$commas = false;
		$args = [];
		foreach ($data as $key => $value) {
			$this->validateColumnName($key);
			if ($commas) {
				$sql .= ",";
			} else {
				$commas = true;
			}
			$sql .= " SET `$key` = ?";
			$args[] = $value;
		}
		if ($match) {
			$sql .= $this->makeWhere($match, $args);
		}
		return $this->prepare($sql, $args)->count();
	}

	public function delete($table, array $match) {
		$args = [];

		$this->validateTableName($table);
		$sql = "DELETE FROM `$table`";
		$sql .= $this->makeWhere($match, $args);

		return $this->prepare($sql, $args)->count();
	}


	protected function validateTableName($table) {
		if (! is_string($table)) {
			throw new \Exception("Table must be a string");
		}
		if (strpos('`', $table) !== false) {
			throw new \Exception("Table may not contain the '`' character");
		}
	}
	protected function validateColumnName($column) {
		if (! is_string($table)) {
			throw new \Exception("Table must be a string");
		}
		if (strpos('`', $table) !== false) {
			throw new \Exception("Table may not contain the '`' character");
		}
	}

	protected function makeWhere($map, &$queryInputs) {
		$sql = " WHERE ";
		$and = false;
		foreach ($map as $column => $value) {
			$this->validateColumnName($column);
			if ($and) {
				$sql .= " AND ";
			} else {
				$and = true;
			}
			$sql .= " `$column` = ?";
			$queryInputs[] = $value;
		}
		return $sql;
	}


}