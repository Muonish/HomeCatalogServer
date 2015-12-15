<?php
	session_start();
	function db_connect() {
		$dbhost = "localhost";
		$dbuser = "root";
		$dbpasswd = "123321";
		$database = "home_catalog";
		
		$db = mysql_connect($dbhost, $dbuser, $dbpasswd) or die("Cannot connect to MySQL.");
		
		mysql_select_db($database) or die("Cannot connect to db.");
		mysql_query("SET NAMES utf8");
		
		return $db;
	}
	
	function db_close($db) {
		mysql_close($db) or die("Cannot close db.");
	}
	
	function quote($var) {
	    return mysql_real_escape_string($var);
	}
	
?>