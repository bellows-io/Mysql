<?php

namespace Mysql\Pdo;

use \Mysql\ResponseInterface;
use \PDOStatement;
use \PDO;

class PdoResponse implements ResponseInterface {

	protected $statement;

	public function __construct(PDOStatement $statement, $executed = false) {
		$this->statement = $statement;
		$this->executed = false;
	}

	public function value() {
		$this->lazyExecute();
		$this->validateColumns();

		foreach ($this->statement as $row) {
			return $row[0];
		}
		return null;
	}

	public function values() {
		$this->lazyExecute();
		$this->validateColumns();
		$out = [];
		foreach ($this->statement as $row) {
			$out[] = $row[0];
		}
		return $out;
	}

	public function keyValue() {
		$this->lazyExecute();
		$this->validateColumns();
		$out = [];
		foreach ($this->statement as $row) {
			$out[$row[0]] = $row[1];
		}
		return $out;
	}

	public function keyValues() {
		$this->lazyExecute();
		$this->validateColumns();
		$out = [];
		foreach ($this->statement as $row) {
			if (! isset($out[$row[0]])) {
				$out[$row[0]] = [];
			}
			$out[$row[0]][] = $row[1];
		}
		return $out;
	}

	public function row() {
		$this->lazyExecute();
		$this->validateColumns();
		$out = [];
		foreach ($this->statement as $row) {
			foreach ($row as $key => $value) {
				if (is_string($key)) {
					$out[$key] = $value;
				}
			}
			return $out;
		}
		return $out;
	}

	public function keyRow() {
		$this->lazyExecute();
		$this->validateColumns();
		$out = [];
		foreach ($this->statement as $row) {
			$index = $row[0];
			$out[$index] = [];
			foreach ($row as $key => $value) {
				if (is_string($key)) {
					$out[$index][$key] = $value;
				}
			}
		}
		return $out;
	}

	public function keyRows() {
		$this->lazyExecute();
		$this->validateColumns();
		$out = [];
		foreach ($this->statement as $row) {
			$rowData = [];
			foreach ($row as $key => $value) {
				if (is_string($key)) {
					if (! isset($rowData[$key])) {
						$rowData[$key] = [];
					}
					$rowData[$key] = $value;
				}
			}
			if (! isset($out[$row[0]])) {
				$out[$row[0]] = [];
			}
			$out[$row[0]][] = $rowData;
		}
		return $out;
	}

	public function count() {
		$this->lazyExecute();
		return $this->statement->rowCount();
	}

	protected function validateColumns() {
		if (! $this->statement->columnCount()) {
			throw new \Exception("Result set has no columns");
		}
	}

	protected function lazyExecute() {
		$this->statement->setFetchMode(PDO::FETCH_BOTH);
		$this->statement->execute();
	}

}