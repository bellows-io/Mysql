<?php

namespace Mysql;

interface ConnectionInterface {

	/**
	 * Executes the sql string
	 * @param  String            $sql       A Mysql string
	 * @param  mixed             $arguments An array of parameters to inject into the $sql
	 * @return ResponseInterface            The response from the server
	 */
	function prepare($sql, array $arguments = null);

	function insert($table, array $data);

	function multiInsert($table, array $multiData);

	function update($table, array $data, array $match);

	function delete($table, array $match);
}