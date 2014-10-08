<?php
	require_once("MysqliClass.php");
	$mysqli = new MysqliClass(array(
			"host"		=>	"127.0.0.1",
			"user"		=>	"root",
			"password"	=>	"87654321",
			"bd"		=>	"db_exemple"
			));
	$mysqli->query("SELECT name,email from user WHERE id = ?;",
		array(
			array(
				"type"=>"i",
				"content"=>"26"
				)
			)
		);
?>