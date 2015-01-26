<?php

namespace Mysql\Pdo;

use \PDOStatement;

class PdoResponseFactory {

	public function make(PDOStatement $statement) {
		return new PdoResponse($statement);
	}

}