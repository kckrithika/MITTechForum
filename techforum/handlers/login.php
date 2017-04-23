<?php
	$username = $_POST["username"];
	$password = $_POST["password"];
	$script = "import UserDetailsConnector; print(UserDetailsConnector.check(\\\"".$username."\\\", \\\"".$password."\\\"));";
	$cmd = "/usr/bin/python3.4 -c \"".$script."\"";
	$output = exec($cmd, $resultVar);
	if($output=="True")
	{
		session_start();
		$_SESSION["username"] = $username;
		echo "OK";
	}
	else
	{
		echo "Username or password incorrect";
	}
?>
