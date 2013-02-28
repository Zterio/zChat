<?php
	ini_set("display_errors",1);
	define("zChat",1);
	require("Mysql.php");
	require("Core.php");
	$Core = new zChat();
	
	if( 
		!empty($_POST['Username']) and
		!empty($_POST['Password']) and
		!empty($_POST['Email']) and
		!empty($_POST['Code'])
	){
		$Core->connect($mysqlHostname, $mysqlUsername, $mysqlPassword, $mysqlDatabase);
		$Core->AddUser($_POST['Username'],$_POST['Password'],$_POST['Email'],$_POST['Code']);
		$Core->endConnection();
	}
		
?>
<html>
<style>p{margin:0;}</style>
<p><?php echo $_GET['msg'] ?></p>
<form action="Register.php" method="post"> 
<p>Username: <input name="Username" type="text" /></p>
<p>Password: <input name="Password" type="Password" /></p>
<p>E-Mail:<input name="Email" type="text" /></p>
<p>Code:<input name="Code" type="text" /></p>
<input type="submit" />
</form>
</html>
