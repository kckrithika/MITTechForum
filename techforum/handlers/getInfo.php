<?php

	class info
	{
		public $formalname = "";
		public $interested = array();
	}
	function execute($script)
	{
		$cmd = "/usr/bin/python3.4 -c \"".$script."\"";
		return shell_exec($cmd);
	}
	
	$info = new info();
	$user = $_POST["user"];
	if($user=="Profile")
	{
		session_start();
		$user = $_SESSION["username"];
		session_write_close();
	}
	$info->formalname = rtrim(execute("import UserDetailsConnector as ud; print(ud.getName(\\\"".$user."\\\"))"));
	$interestedOBJ = json_decode(rtrim(execute("import RankingsConnector as rc; print(rc.getInterestedTopic(\\\"".$user."\\\"))")));
	$count = $interestedOBJ->count;
	for($i=0; $i<$count; $i=$i+1)
	{
		if($interestedOBJ->$i->uncertainty!=8.333)
		{
			$topic = json_decode(rtrim(execute("import TopicsConnector as tc; print(tc.get(\\\"".$interestedOBJ->$i->topic."\\\"))")));
			array_push($info->interested, $topic->topic);
		}
	}
	
	echo json_encode($info);
?>
