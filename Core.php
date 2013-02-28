<?php
	if(!defined("zChat")) die("Hacking attempt...");
	session_start(); // We need a session right? //
class zChat {
	/* Mysql Connection */
    private $sqlConnection;
    public function connect($mysqlHostname, $mysqlUsername, $mysqlPassword, $mysqlDatabase){
        $this->sqlConnection= new mysqli($mysqlHostname, $mysqlUsername, $mysqlPassword, $mysqlDatabase);
        if ($this->sqlConnection->connect_error){ 
            die($this->sqlConnection->connect_error); 
			
		}
    }
	public function endConnection() {
	$this->sqlConnection->close();
	}
	public function AddUser($User,$Pass,$Mail,$Code) {
		if(!filter_var($Mail, FILTER_VALIDATE_EMAIL)) throw new Exception("E-Mail $Mail is not a valid email format!");
		// Now the fun part ^_^
		$IfExistUsername = $this->sqlConnection->query("SELECT * FROM `user` WHERE `Username` = '$User'");
		$IfExistMail = $this->sqlConnection->query("SELECT * FROM `user` WHERE `E-Mail` = '$Mail'");
		if($IfExistUsername->num_rows != 0)  throw new Exception("The Username is Allready taken!");
		if($IfExistMail->num_rows != 0)  throw new Exception("The E-Mail had allready been used for a account, Please use a different e-mail!");
			/* Generate a salty salt :P */
			$SaltLength = 7;
			$Salt = "";
			while ($SaltLength > 0) { $Salt .= dechex(mt_rand(0,15)); $SaltLength -= 1; }
			/* Generate a Activation code :P */
			$ActLength = 10;
			$Act = "";
			while ($ActLength > 0) { $Act .= dechex(mt_rand(0,15)); $ActLength -= 1; }
		/* Hash + Salt + Md5(SALT) = True */
		$SaltPass = md5($Salt.$Pass.md5($Salt));
		/* Prepare to insert all the goodies :( */
		$sUser = $this->sqlConnection->real_escape_string($User);
		$sPass = $this->sqlConnection->real_escape_string($SaltPass);
		$sSalt = $this->sqlConnection->real_escape_string($Salt);
		$sMail = $this->sqlConnection->real_escape_string($Mail);
		$sAct = $this->sqlConnection->real_escape_string($Act);
		$sIP = $this->sqlConnection->real_escape_string($_SERVER['REMOTE_ADDR']);
		/* Do the insert */
		$UserInsert = $this->sqlConnection->query("
			INSERT INTO `user` (`ID`,`Username`,`Password`,`Salt`,`E-Mail`,`IP`,`Activated`,`Code`)
			VALUES (NULL,'$sUser', '$sPass', '$sSalt', '$sMail', '$sIP', '0', '$sAct')");
		if(!isset($UserInsert) || empty($UserInsert))  throw new Exception("Could not add User to database");
	}
	public function ActivateUser($Codee) {
		$Code = $this->sqlConnection->real_escape_string($Codee);
		$IfValidCode = $this->sqlConnection->query("SELECT `Activated`, `Code` FROM `user` WHERE `Code` = '$Code'");
		
		if($IfValidCode->num_rows == 0) {	
			header("Location: /zchat/activate.php?msg=".urlencode("The activation code is not valid."));
			exit;
		}
		
		$Activated = $IfValidCode->fetch_array();
		
		if($Activated['Activated'] == "1") {
			header("Location: /zchat/login.php?msg=".urlencode("Your account is allready activated, You can login now."));
			exit;
		}
		
		$ActivateUser = $this->sqlConnection->query("UPDATE `user` SET `Activated` = '1' WHERE `Code` = '$Code'");
		
		if(!isset($ActivateUser) || empty($ActivateUser)) {
			header("Location: /zchat/activate.php?msg=".urlencode("Error, Could not activate your account...")); 
			exit;
		} else { 
			header("Location: /zchat/login.php?msg=".urlencode("Account activated! You can now login!")); 
		}
		
	}
	
	public function LoginUser($User,$Pass) {
		$sUser = $this->sqlConnection->real_escape_string($User);
		$sPass = $this->sqlConnection->real_escape_string($Pass);
		$GetSalt = $this->sqlConnection->query("SELECT `Salt` FROM `user` WHERE `Username` = '$sUser'")->fetch_array();
		$Salt = $GetSalt['Salt'];
		$SaltPass = md5($Salt.$Pass.md5($Salt));
		$sSaltPass = $this->sqlConnection->real_escape_string($SaltPass);
		$AuthPass = $this->sqlConnection->query("SELECT `Activated` FROM `user` WHERE `Username` = '$sUser' AND `Password` = '$sSaltPass'");
		if($AuthPass->num_rows == 0)  throw new Exception("Username Or Password incorrect!");
		$CheckActivated = $AuthPass->fetch_array();
		if($CheckActivated['Activated'] == "0") {
			header("Location: /zchat/login.php?msg=".urlencode("Your account Is not activated! Did you forget to activate it?"));
			exit;
		}
		/* Time to bake a cookie */
		$_SESSION['Username'] = $User;
		$_SESSION['Password'] = $SaltPass;
		header("Location: /zchat");
	}
	public function IsAuthorized() {
		$User = $_SESSION['Username'];
		$Pass = $_SESSION['Password'];
		if(!isset($_SESSION['Username']) || !isset($_SESSION['Password'])) {
		header("Location: /zchat/login.php?msg=".urlencode("You need to be logged in to access this page!"));
		exit;
		}
		$sUser = $this->sqlConnection->real_escape_string($User);
		$sPass = $this->sqlConnection->real_escape_string($Pass);
		$IsAuthorized = false;
		$CheckUser = $this->sqlConnection->query("SELECT * FROM `user` WHERE `Username` = '$sUser' AND `Password` = '$sPass'");
		if($CheckUser->num_rows == 0) { 
		header("Location: /zchat/login.php?msg=".urlencode("Could not authorize you, Please login again..."));
		exit;
		}
		$ActivatedCheck = $CheckUser->fetch_array();
		if($ActivatedCheck['Activated'] == "0")  
		{
		header("Location: /zchat/login.php?msg=".urlencode("Your account is NOT activated, possibly it got deactivated by a admin"));
		exit;
		}
		return true;
		
	}
}