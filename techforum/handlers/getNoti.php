<?php
	function execute($script)
	{
		$cmd = "/usr/bin/python3.4 -c \"".$script."\"";
		return shell_exec($cmd);
	}
	session_start();
	$user = $_SESSION["username"];
	session_write_close();
	class notification
	{
		public $id="";
		public $thread="";
		public $question="";
		public $type="";
		public $seen="";
	}
	class notifications
	{
		public $notis = array();
	}
	$script = "import NotiConnector as nc; print(nc.getNotis(\\\"".$user."\\\"))";
	$output = execute($script);
	
	$json_object = json_decode($output);
	$count = $json_object->count;
	$notis = new notifications();
	for($i=0; $i<$count; $i+=1)
	{
		$row = $json_object->$i;
		$noti = new notification();
		$noti->id = $row->id;
		$noti->thread = $row->thread;
		$noti->question = rtrim(execute("import ThreadsConnector as tc; print(tc.getQuestion(\\\"".$noti->thread."\\\"))"));
		$noti->type = $row->type;
		$noti->seen = $row->seen;
		array_push($notis->notis, $noti);
	}
	echo json_encode($notis);
?>
