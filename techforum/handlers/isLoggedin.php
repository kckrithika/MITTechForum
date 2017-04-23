<?php
	session_start();
	if(isset($_SESSION["username"]))
	{
		echo "YES";
	}
	else
	{
		echo "NO";
	}
?>
