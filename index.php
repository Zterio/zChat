<?php
	ini_set("display_errors",1);
	define("zChat",1);
	require("Mysql.php");
	require("Core.php");
	$Core = new zChat();
	$Core->connect($mysqlHostname, $mysqlUsername, $mysqlPassword, $mysqlDatabase);
	$Core->IsAuthorized();