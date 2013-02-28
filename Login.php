<?php
	ini_set("display_errors",1);
	define("zChat",1);
	require("Mysql.php");
	require("Core.php");
	$Core = new zChat();
	
	if( 
		!empty($_POST['Username']) and !empty($_POST['Password'])
	){
		$Core->connect($mysqlHostname, $mysqlUsername, $mysqlPassword, $mysqlDatabase);
		$Core->LoginUser($_POST['Username'],$_POST['Password']);
		$Core->endConnection();
	}
		
?>
<html>
<style>p{margin:0;}</style>
<p><?php echo $_GET['msg'] ?></p>
<form action="Login.php" method="post"> 
<p>Username:<input name="Username" type="text" /></p>
<p>Password:<input name="Password" type="password" /></p>
<input type="submit" value="Login"/>
</form>
</html>