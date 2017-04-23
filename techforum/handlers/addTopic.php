<?php
	function execute($script)
	{
		$cmd = "/usr/bin/python3.4 -c \"".$script."\"";
		return shell_exec($cmd);
	}
	$topic = $_POST["topic"];
	$output = rtrim(execute("import TopicsConnector as tc; tc.insert(\\\"".$topic."\\\")"));
?>
