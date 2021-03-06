<?php
	function execute($script)
	{
		$cmd = "/usr/bin/python3.4 -c \"".$script."\"";
		return shell_exec($cmd);
	}

	class suggestion
	{
		public $thread="";
		public $question="";
		public $asker="";
		public $topic="";
	}
	class suggestions
	{
		public $suggestions = array();
	}
	
	class topic
	{
		public $id="";
		public $name="";
	}
	session_start();
	$user = $_SESSION["username"];
	session_write_close();
	$topics=array();
	$rawTopics = rtrim(execute("import TopicsConnector as tc; print(tc.getAll())"));
	$obj = json_decode($rawTopics);
	$count = $obj->count;
	for($i=0; $i<$count; $i=$i+1)
	{
		$temp = new topic();
		$temp->id = $obj->$i->id;
		$temp->name = $obj->$i->topic;
		array_push($topics, $temp);
	}
	$suggs = new suggestions();
	foreach($topics as $topic)
	{
		$skilluncertainty = rtrim(execute("import RankingsConnector as rc; print(rc.getRank(\\\"".$user."\\\", \\\"".$topic->id."\\\"))"));
		$pair = rtrim($skilluncertainty, ')');
		$pair = trim($pair, '(');
		$pair = rtrim($pair, ',');
		$pair = explode(',', $pair);
		$skill = $pair[0];
		if ($pair[1]!=8.333)
		{
			$threadraw = rtrim(execute("import ThreadsConnector as tc; print(tc.getBelow(\\\"".$skill."\\\", \\\"".$topic->id."\\\"))"));
			$jsonobject = json_decode($threadraw);
			$count = $jsonobject->count;
			for($i=0; $i<$count; $i+=1)
			{
				$sugg = new suggestion();
				$object = $jsonobject->$i;
				$sugg->thread = $object->thread;
				$sugg->question = $object->question;
				$sugg->asker = $object->asker;
				$sugg->topic = $topic->name;
				array_push($suggs->suggestions, $sugg);
			}
		}
	}
	
	echo json_encode($suggs);
?>
