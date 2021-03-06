<?php
	class notification
	{
		public $id="";
		public $thread="";
		public $question="";
		public $type="";
		public $seen="";
	}
	function execute($script)
	{
		$cmd = "/usr/bin/python3.4 -c \"".$script."\"";
		return shell_exec($cmd);
	}
	function printData($output)
	{
		header("Content-Type: text/event-stream");
		header("Cache-Control: no-cache");
		echo "data: ";
		echo $output;
		echo "\n\n";
		ob_flush();
		flush();
	}
	date_default_timezone_set("Asia/Calcutta");
	session_start();
	$user = $_SESSION["username"];
	session_write_close();
	$date = date("Y-m-d")." ".date("H:i:s");
	while(true)
	{
		$output = rtrim(execute("import NotiConnector as nc; print(nc.getNotiAbove(\\\"".$date."\\\", \\\"".$user."\\\"))"));
		if($output!="{}")
		{
			$noti = new notification();
			$row = json_decode($output);
			$noti->id = $row->id;
			$noti->thread = $row->thread;
			$noti->question = rtrim(execute("import ThreadsConnector as tc; print(tc.getQuestion(\\\"".$noti->thread."\\\"))"));
			$noti->type = $row->type;
			$noti->seen = $row->seen;
			printData(json_encode($noti));
			$date = $row->time;
		}
	}
?>
