<?php
	ini_set("display_errors",1);
	define("zChat",1);
	require("Mysql.php");
	require("Core.php");
	$Core = new zChat();
	
	if( 
		!empty($_POST['Code']) || isset($_GET['code'])
	){
		$Core->connect($mysqlHostname, $mysqlUsername, $mysqlPassword, $mysqlDatabase);
		$Core->ActivateUser($_POST['Code']);
		$Core->endConnection();
	}
		
?>
<html>
<style>p{margin:0;}</style>
<p><?php echo $_GET['msg'] ?></p>
<form action="Activate.php" method="post"> 
<p>Code:<input name="Code" type="text" /></p>
<input type="submit" value="Activate user"/>
</form>
</html>
