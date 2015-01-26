<?php

namespace Mysql;

interface ResponseInterface {

	function value(); // 1
	function values(); // [1, 2]
	function keyValue(); // [1 => 'a', 2 => 'c']
	function keyValues(); // [1 => ['a', 'b'], 2 => ['c']]
	function row();
	function keyRow();
	function keyRows();
	function count();

}