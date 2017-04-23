<?
	function execute($script)
	{
		$cmd = "/usr/bin/python3.4 -c \"".$script."\"";
		return shell_exec($cmd);
	}
	echo rtrim(execute("import TopicsConnector as tc; print(tc.getAll());"));
?>
