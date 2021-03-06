<?php
	session_start();
	$currentuser = $_SESSION["username"];
	session_write_close();
	function execute($script)
	{
		$cmd = "/usr/bin/python3.4 -c \"".$script."\"";
		return shell_exec($cmd);
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
	
	function fetch($id, $currentuser)
	{
		/* Fetching information from Threads table */
		$asker = rtrim(execute("import ThreadsConnector; print(ThreadsConnector.getAsker(\\\"".$id."\\\"))"));
		$bestbuttonenable = false;
		$question = rtrim(execute("import ThreadsConnector; print(ThreadsConnector.getQuestion(\\\"".$id."\\\"))"));
		$timeOfPost = rtrim(execute("import ThreadsConnector; print(ThreadsConnector.getTimeOfPost(\\\"".$id."\\\"))"));
		$closed = rtrim(execute("import ThreadsConnector; print(ThreadsConnector.isClosed(\\\"".$id."\\\"))"));
		$closedresult=" ";
		$topic = rtrim(execute("import ThreadsConnector; print(ThreadsConnector.getTopic(\\\"".$id."\\\"))"));
		if($asker==$currentuser)
		{
			if($closed==0)
			{
				$bestbuttonenable = true;
			}
			$asker = "You";
		}
		
		/* Fetching all comments belonging to this thread */
		$commentsforidstring = rtrim(execute("import CommentsConnector; print(CommentsConnector.getCommentIDs(\\\"".$id."\\\"))"));
		$commentids = getArray($commentsforidstring);
		
		$commentsresult="";
		foreach($commentids as $elementid)
		{
			if($elementid!="")
			{
				$author = rtrim(execute("import CommentsConnector; print(CommentsConnector.getAuthor(\\\"".$elementid."\\\"))"));
				$comment = rtrim(execute("import CommentsConnector; print(CommentsConnector.getComment(\\\"".$elementid."\\\"))"));
				$time = rtrim(execute("import CommentsConnector; print(CommentsConnector.getTimeOfComment(\\\"".$elementid."\\\"))"));
				if($author==$currentuser)
				{
					$author = "You";
				}
				
				/* Add the fetched information to the result */
				$commentsresult = $commentsresult."<font class=\\\"Meta\\\">".$author." commented</font><br>".$comment."<br><font class=\\\"Meta\\\"> at ".$time."</font><br><hr>";
			}
		}
		
		/* Initially answerbox is enabled */
		$answerboxenabled = true;
		
		/* Fetching all answers belonging to this thread */
		$answersforidstring = rtrim(execute("import AnswersConnector; print(AnswersConnector.getAnswerIDs(\\\"".$id."\\\"))"));
		$answerids = getArray($answersforidstring);
	
		$answersresult = "";
		foreach($answerids as $elementid)
		{
			if($elementid!="")
			{
				/* Fetching information from Answers table */
				$answerer = rtrim(execute("import AnswersConnector; print(AnswersConnector.getAnswerer(\\\"".$elementid."\\\"))"));
				$answer = rtrim(execute("import AnswersConnector; print(AnswersConnector.getAnswer(\\\"".$elementid."\\\"))"));
				$time = rtrim(execute("import AnswersConnector; print(AnswersConnector.getTimeOfAnswer(\\\"".$elementid."\\\"))"));

				/* Disable the answerbox if current user has already answered */
				$editbuttonenabled=false;
				if($answerer==$currentuser)
				{
					$answerboxenabled=false;
					$answerer = "You";
					$editbuttonenabled=true;
				}
				
				$bestbutton="";
				if($bestbuttonenable==true)
				{
					$bestbutton = "<button onclick=\\\"selectbest(".$elementid.")\\\" class=\\\"bestbutton\\\">Select as best Answer</button>";
				}
				
				$editbutton = "";
				if($editbuttonenabled==true)
				{
					$editbutton = "<button onclick=\\\"editAnswer(".$elementid.")\\\" class=\\\"editbutton\\\">Edit</button>";
				}
				
				/* Add the fetched information to the result */
				$answersresult = $answersresult."<div id=\\\"answer".$elementid."\\\">".$bestbutton.$editbutton."<font class=\\\"Meta\\\">".$answerer." answered</font><br><p id=\\\"answer\\\">".$answer."</p><font class=\\\"Meta\\\"> at ".$time."</font></div><hr>";
			}
		}
		
		/* Check if the thread is closed */
		if($closed==1)
		{
			/* Disable answerbox */
			$answerboxenabled = false;
			$closedresult = "closed";
		}
		/* Preparing the answer and comment text boxes with buttons */
		
		$answerbox = "";
		if($answerboxenabled==true)
		{
			$answerbox = "<form><textarea class=\\\"renderedbox\\\" required></textarea><input type=\\\"submit\\\" class=\\\"answerbutton \\\" value=\\\"Give Answer\\\" onclick=\\\"postAnswer(this, ".$id."); return false;\\\"></input></form>";
		}
		
		$commentbox = "<form ><textarea class=\\\"renderedbox\\\" required></textarea><input type=\\\"submit\\\" onclick=\\\"postComment(this, ".$id."); return false;\\\" class=\\\"commentbutton\\\" value=\\\"Comment\\\"></input></form>";
		
		/* Preparing the result */
		$result ="<div id=\\\"".$id."\\\"class=\\\"post\\\"><div class=\\\"thingreenbackground\\\">".$asker." asked</div><div class=\\\"renderedtext\\\">".$question."<font class=\\\"Meta\\\"> at ".$timeOfPost." in the topic ".$topic."</font></div><br><div class=\\\"thingreenbackground\\\">Comments</div><div class=\\\"renderedtext\\\">".$commentsresult."<br>".$commentbox."</div><div class=\\\"thingreenbackground\\\">Answers</div><div class=\\\"renderedtext\\\">".$answersresult."<br>".$answerbox."</div><div class=\\\"thingreenbackground\\\">".$closedresult."</div></div>";
		return $result;
	}
	$id = $_POST["ID"];
	$dispid = "";
	$response = "";
	if($id=="")
	{
		$id = formatString(execute("import ThreadsConnector; print(ThreadsConnector.getLastID())"));
		if($id=="None")
		{
			$dispid = 0;
		}
		else if($id==1)
		{
			$dispid = $id;
			$response = fetch($id, $currentuser);
		}
		else
		{
			$dispid = $id-1;
			$response = fetch($id, $currentuser)."<br>".fetch($id-1, $currentuser);
		}
	}	
	else if($id>2)
	{
		$dispid = $id-2;
		$response = fetch($id-1, $currentuser)."<br>".fetch($id-2, $currentuser);
	}
	else if($id>1)
	{
		$dispid = ($id-1);
		$response = fetch($id-1, $currentuser);
	}
	else
	{
		$dispid = 0;
	}
	echo '{"id":"'.$dispid.'", "response":"'.$response.'"}';
?>
