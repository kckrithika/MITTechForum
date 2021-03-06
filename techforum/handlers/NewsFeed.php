<?php
	session_start();
	$currentuser = $_SESSION["username"];
	session_write_close();
	function printData($class)
	{
		header("Content-Type: text/event-stream");
		header("Cache-Control: no-cache");
		echo "data: ";
		echo json_encode($class);
		echo "\n\n";
		ob_flush();
		flush();
	}
	function execute($script)
	{
		$cmd = "/usr/bin/python3.4 -c \"".$script."\"";
		return shell_exec($cmd);
	}
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
	
	function fetch($id)
	{
		$rawthread="not initialized";
		session_start();
		$user = $_SESSION["username"];
		session_write_close();
		if($id!=0)
		{
			$rawthread = rtrim(execute("import ThreadsConnector as tc; print(tc.getThread(\\\"".$id."\\\"))"));
		}
		else
		{
			$rawthread = rtrim(execute("import ThreadsConnector as tc; print(tc.getLatest())"));
		}
		$obj = json_decode($rawthread);
		$post = new post();
		$post->id = $obj->id;
		$post->asker = $obj->asker;
		if($post->asker==$user)
		{
			$post->asker="You";
			
		}
		$post->question = $obj->question;
		$post->timeOfPost = $obj->timeOfPost;
		$topicJSON = rtrim(execute("import TopicsConnector as tc; print(tc.get(\\\"".$obj->topic."\\\"))"));
		$topicOBJ = json_decode($topicJSON);
		$post->topic = $topicOBJ->topic;
		$post->closed = $obj->closed;
		
		$rawcomments = rtrim(execute("import CommentsConnector as cc; print(cc.getComments(\\\"".$post->id."\\\"))"));
		$obj = json_decode($rawcomments);
		$count = $obj->count;
		for($i=0; $i<$count; $i+=1)
		{
			$cmnt = $obj->$i;
			$comment = new comment();
			$comment->author = $cmnt->author;
			if($comment->author==$user)
			{
				$comment->author="You";
			}
			$comment->comment = $cmnt->comment;
			$comment->timeOfComment = $cmnt->timeOfComment;
			array_push($post->comments, $comment);
		}
		
		$rawanswers = rtrim(execute("import AnswersConnector as ac; print(ac.getAnswers(\\\"".$post->id."\\\"))"));
		$obj = json_decode($rawanswers);
		$count = $obj->count;
		for($i=0; $i<$count; $i+=1)
		{
			$ans = $obj->$i;
			$answer = new answer();
			$answer->id = $ans->id;
			$answer->isBest = $ans->bestAnswer;
			$answer->answer = $ans->answer;
			$answer->author = $ans->answerer;
			if($answer->author==$user)
			{
				$answer->author="You";
			}
			$answer->timeOfAnswer = $ans->timeOfAnswer;
			array_push($post->answers, $answer);
		}
		return $post;		
	}
	
	date_default_timezone_set("Asia/Calcutta");
	$lastthreadtime = $lastcommenttime = $lastanswertime = date("Y-m-d")." ".date("H:i:s");
	while(true)
	{
		$postedthread = rtrim(execute("import ThreadsConnector; print(ThreadsConnector.getPostAbove(\\\"".$lastthreadtime."\\\"))"));
		
		/* Check if posted thread exists */
		if($postedthread!="None")
		{
			$lastthreadtime = rtrim(execute("import ThreadsConnector; print(ThreadsConnector.getTimeOfPost(\\\"".$postedthread."\\\"))"));
			printData(fetch($postedthread));
		}
		
		$postedanswer = rtrim(execute("import AnswersConnector; print(AnswersConnector.getAnswerAbove(\\\"".$lastanswertime."\\\"))"));
		
		/* Check if posted answer exists */
		if($postedanswer!="None")
		{
			$lastanswertime = rtrim(execute("import AnswersConnector; print(AnswersConnector.getTimeOfAnswer(\\\"".$postedanswer."\\\"))"));
			$thread = rtrim(execute("import AnswersConnector; print(AnswersConnector.getThread(\\\"".$postedanswer."\\\"))"));
			printData(fetch($thread));
		}
		
		$postedcomment = rtrim(execute("import CommentsConnector; print(CommentsConnector.getCommentAbove(\\\"".$lastcommenttime."\\\"))"));
		if($postedcomment!="None")
		{
			$lastcommenttime = rtrim(execute("import CommentsConnector; print(CommentsConnector.getTimeOfComment(\\\"".$postedcomment."\\\"))"));
			$thread = rtrim(execute("import CommentsConnector; print(CommentsConnector.getThread(\\\"".$postedcomment."\\\"))"));
			printData(fetch($thread));
		}
	}
?>
