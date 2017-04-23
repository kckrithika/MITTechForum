<?php
	session_start();
	$answerer = $_SESSION["username"];
	session_write_close();
	$answer = $_POST["Answer"];
	$thread = $_POST["id"];
	$type = $_POST["type"];
	date_default_timezone_set("Asia/Calcutta");
	$time = date("Y-m-d")." ".date("H:i:s");
	
	$script = "import AnswersConnector; AnswersConnector.insert(\\\"".$thread."\\\", \\\"".$answerer."\\\", \\\"".$answer."\\\", \\\"".$time."\\\")";
	$cmd = "/usr/bin/python3.4 -c \"".$script."\"";
	$output = shell_exec($cmd);
	
	$script = "import ThreadsConnector as tc; print(tc.getAsker(\\\"".$thread."\\\"))";
	$cmd = "/usr/bin/python3.4 -c \"".$script."\"";
	$user = rtrim(shell_exec($cmd));
	
	
	$script = "import NotiConnector; NotiConnector.insert(\\\"".$user."\\\", \\\"".$thread."\\\", \\\"answer\\\")";
	$cmd = "/usr/bin/python3.4 -c \"".$script."\"";
	$output = shell_exec($cmd);
?>
