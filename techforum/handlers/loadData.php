<?php
	class comment
	{
		public $author = "";
		public $comment = "";
		public $timeOfComment = "";
	}
	class answer
	{
		public $id = "";
		public $author = "";
		public $answer = "";
		public $timeOfAnswer = "";
		public $isBest = "";
	}
	class post
	{
		public $id="";
		public $asker="";
		public $question="";
		public $timeOfPost="";
		public $topic="";
		public $comments = array();
		public $answers = array();
		public $closed = "";
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
	
	function getClass($id)
	{
		/* Fetch all the data from the database first */
		session_start();
		$currentuser = $_SESSION["username"];
		session_write_close();
		$post = new post();
		$post->id = $id;
		
		/* Get the asker */
		$asker = rtrim(execute("import ThreadsConnector; print(ThreadsConnector.getAsker(\\\"".$id."\\\"))"));
		$post->asker = $asker;
		if($asker==$currentuser)
		{
			$post->asker = "You";
		}
		
		/* Get the question */
		$question = rtrim(execute("import ThreadsConnector; print(ThreadsConnector.getQuestion(\\\"".$id."\\\"))"));
		$post->question = $question;
		
		$timeOfPost = rtrim(execute("import ThreadsConnector; print(ThreadsConnector.getTimeOfPost(\\\"".$id."\\\"))"));
		$post->timeOfPost = $timeOfPost;
		
		$topic = rtrim(execute("import ThreadsConnector; print(ThreadsConnector.getTopic(\\\"".$id."\\\"))"));
		$post->topic = $topic;
		
		/* Get comment id s for the current thread id */
		$commentids = rtrim(execute("import CommentsConnector; print(CommentsConnector.getCommentIDs(\\\"".$id."\\\"))"));
		
		/* convert the comment ids into array */
		$array1 = getArray($commentids);
		
		/* Form an array of comments */
		$commentArray=array();
		foreach($array1 as $elementid)
		{
			if($elementid!="")
			{
				$author = rtrim(execute("import CommentsConnector; print(CommentsConnector.getAuthor(\\\"".$elementid."\\\"))"));
				if($author==$currentuser)
				{
					$author=="You";
				}
				$comment = rtrim(execute("import CommentsConnector; print(CommentsConnector.getComment(\\\"".$elementid."\\\"))"));
				$time = rtrim(execute("import CommentsConnector; print(CommentsConnector.getTimeOfComment(\\\"".$elementid."\\\"))"));
				$cmnt = new comment();
				$cmnt->author = $author;
				$cmnt->comment = $comment;
				$cmnt->timeOfComment = $time;
				array_push($commentArray, $cmnt);
			}
		}
		
		$post->comments = $commentArray;
		
		/*Get answer id 's for the current thread id*/
		$answerids = rtrim(execute("import AnswersConnector; print(AnswersConnector.getAnswerIDs(\\\"".$id."\\\"))"));
		
		/* convert the answerids into array */
		$array2 = getArray($answerids);
		
		/* Check if the thread is closed */
		
		$isclosed = formatString(execute("import ThreadsConnector; print(ThreadsConnector.isClosed(\\\"".$id."\\\"))"));
		$post->closed = $isclosed;
		
		$answerArray = array();
		foreach($array2 as $elementid)
		{
			if($elementid!="")
			{
				$answerer = formatString(execute("import AnswersConnector; print(AnswersConnector.getAnswerer(\\\"".$elementid."\\\"))"));
				if($answerer == $currentuser)
				{
					$answerer = "You";
				}
				$answer = formatString(execute("import AnswersConnector; print(AnswersConnector.getAnswer(\\\"".$elementid."\\\"))"));
				$time = rtrim(execute("import AnswersConnector; print(AnswersConnector.getTimeOfAnswer(\\\"".$elementid."\\\"))"));
				$isbest = rtrim(execute("import AnswersConnector; print(AnswersConnector.isBest(\\\"".$elementid."\\\"))"));
				$ans = new answer();
				$ans->id = $elementid;
				$ans->author = $answerer;
				$ans->answer = $answer;
				$ans->timeOfAnswer = $time;
				$ans->isBest = $isbest;
				array_push($answerArray, $ans);
			}
		}
		$post->answers = $answerArray;
		return $post;		
	}
	
	$id = $_POST["ID"];
	$result = "";
	if($id=="0")
	{
		$id = formatString(execute("import ThreadsConnector; print(ThreadsConnector.getLastID())"));
		if($id=="None")
		{
			$result = "Nothing to fetch";
		}
		else
		{
			$result = json_encode(getClass($id));
		}
		
	}	
	else if($id>1)
	{
		$result = json_encode(getClass((string)($id-1)));	
	}
	else
	{
		$result = "!";
	}
	echo $result;
?>
