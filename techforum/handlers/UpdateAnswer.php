<?php
	function execute($string)
	{
		$cmd = "/usr/bin/python3.4 -c \"".$script."\"";
		$output = shell_exec($cmd);
	}
	$answer = $_POST["answer"];
	$id = $_POST["id"];
	$script = "import AnswersConnector; AnswersConnector.updateAnswer(\\\"".$id."\\\", \\\"".$answer."\\\")";
	execute($script);
?>
