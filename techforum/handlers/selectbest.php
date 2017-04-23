<?php
	session_start();
	$currentuser = $_SESSION["username"];
	session_write_close();
	function formatString($string)
	{
		$string = rtrim($string);
		$string = str_replace("(", "", $string);
		$string = str_replace(")", "", $string);
		$string = str_replace(", ", "", $string);
		$string = rtrim($string, ",,");
		$string = rtrim($string, ",");
		return $string;
	}
	
	function getArray($string)
	{
		$string = str_replace("(", "", $string);
		$string = str_replace(")", "", $string);
		$string = rtrim($string, ",,");
		$string = rtrim($string, ",");
		$array = explode(',', $string);
		return $array;
	}
	function execute($script)
	{
		$cmd = "/usr/bin/python3.4 -c \"".$script."\"";
		return shell_exec($cmd);
	}
	$id = $_POST["answerid"];
	
	/* in Answers table mark as best answer */
	execute("import AnswersConnector; AnswersConnector.setBest(\\\"".$id."\\\")");
	
	/* get the answerer's name from Answers table */
	$answerer = rtrim(execute("import AnswersConnector; print(AnswersConnector.getAnswerer(\\\"".$id."\\\"))"));
	$thread = rtrim(execute("import AnswersConnector; print(AnswersConnector.getThread(\\\"".$id."\\\"))"));
	
	/* get the thread and mark the thread as closed */
	execute("import ThreadsConnector; ThreadsConnector.setclosed(\\\"".$thread."\\\")");
	
	/* insert the answerer name in thread */
	execute("import ThreadsConnector; ThreadsConnector.setAnswerer(\\\"".$thread."\\\", \\\"".$answerer."\\\")");
	
	/* get topic from threads */
	$topic = rtrim(execute("import ThreadsConnector; print(ThreadsConnector.getTopic(\\\"".$thread."\\\"))"));
	
	/* get the ranks of the answerer in the topic and put it in the thread */
	$levels = rtrim(execute("import RankingsConnector; print(RankingsConnector.getRank(\\\"".$answerer."\\\", \\\"".$topic."\\\"))"));
	$first = true;
	$skill = "";
	$uncertainty = "";
	$levels = getArray($levels);
	foreach($levels as $element)
	{
		if($first==true)
		{
			$skill = $element;
			$first = false;
		}
		else
		{
			$uncertainty = $element;
		}
	}
	execute("import ThreadsConnector; ThreadsConnector.setAnswererLevels(\\\"".$thread."\\\", \\\"".$skill."\\\", \\\"".$uncertainty."\\\")");
	if($currentuser!=$answerer)
	{
		/* get the ranks of asker also in the same topic */
		$askerlevels = rtrim(execute("import RankingsConnector; print(RankingsConnector.getRank(\\\"".$currentuser."\\\", \\\"".$topic."\\\"))"));
		$first = true;
		$askerSkill = "";
		$askerUncertainty = "";
		$levels = getArray($askerlevels);
		foreach($levels as $element)
		{
			if($first==true)
			{
				$askerSkill = $element;
				$first = false;
			}
			else
			{
				$askerUncertainty = $element;
			}
		}

		/* perform match between asker and answerer */
		$result = rtrim(execute("import rankCalculator; print(rankCalculator.updateRank(".$skill.", ".$uncertainty.", ".$askerSkill.",".$askerUncertainty."))"));
		$count=0;
		$array = getArray($result);
		foreach($array as $element)
		{
			if($count==0)
			{
				$skill = $element;
				$count = $count+1;
			}
			else if($count==1)
			{
				$uncertainty = $element;
				$count = $count+1;
			}
			else if($count==2)
			{
				$askerSkill = $element;
				$count = $count+1;		
			}
			else if($count==3)
			{
				$askerUncertainty = $element;
				$count = $count+1;		
			}
		}
		/* update the new levels in the Rankings table */
		execute("import RankingsConnector; RankingsConnector.setLevel(\\\"".$answerer."\\\", \\\"".$topic."\\\", \\\"".$skill."\\\", \\\"".$uncertainty."\\\")");
	
		execute("import RankingsConnector; RankingsConnector.setLevel(\\\"".$currentuser."\\\", \\\"".$topic."\\\", \\\"".$askerSkill."\\\", \\\"".$askerUncertainty."\\\")");
	}
?>
