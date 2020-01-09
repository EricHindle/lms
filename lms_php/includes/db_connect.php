<?php
	require 'ret-config.php';
	$mypdo = new PDO( 'mysql:host='.HOST.';dbname='.DATABASE.'', USER, PASSWORD );
?>