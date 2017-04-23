<?php
	$comment = $_POST["Comment"];
	$thread = $_POST["id"];
	session_start();
	$author = $_SESSION["username"];
	session_write_close();
	date_default_timezone_set("Asia/Calcutta");
	$time = date("Y-m-d")." ".date("H:i:s");
	$script = "import CommentsConnector; CommentsConnector.insert(\\\"".$thread."\\\", \\\"".$author."\\\", \\\"".$comment."\\\", \\\"".$time."\\\")";
	$cmd = "/usr/bin/python3.4 -c \"".$script."\"";
	$output = shell_exec($cmd);
	
	$script = "import ThreadsConnector as tc; print(tc.getAsker(\\\"".$thread."\\\"))";
	$cmd = "/usr/bin/python3.4 -c \"".$script."\"";
	$user = rtrim(shell_exec($cmd));
	
	
	$script = "import NotiConnector; NotiConnector.insert(\\\"".$user."\\\", \\\"".$thread."\\\", \\\"comment\\\")";
	$cmd = "/usr/bin/python3.4 -c \"".$script."\"";
	$output = shell_exec($cmd);
?>
