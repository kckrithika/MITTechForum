<?php
	$question = $_POST["Question"];
	session_start();
	$username = $_SESSION["username"];
	session_write_close();
	$topic = $_POST["Topic"];
	date_default_timezone_set("Asia/Calcutta");
	$datetime = date("Y-m-d")." ".date("H:i:s");
	$script = "import ThreadsConnector; ThreadsConnector.insert(\\\"".$question."\\\",\\\"".$username."\\\",\\\"".$topic."\\\", \\\"".$datetime."\\\");";
	$cmd = "/usr/bin/python3.4 -c \"".$script."\" &";
	$output = shell_exec($cmd);
	echo $cmd." executed";
?>
