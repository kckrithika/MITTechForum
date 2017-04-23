<?php
	function execute($script)
	{
		$cmd = "/usr/bin/python3.4 -c \"".$script."\"";
		return shell_exec($cmd);
	}
	$id = $_POST["id"];
	$output = execute("import NotiConnector as nc; nc.deleteNoti(\\\"".$id."\\\")");
?>
