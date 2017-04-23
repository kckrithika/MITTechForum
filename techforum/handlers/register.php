<?php
	$name = $_POST["name"];
	$dob = $_POST["dob"];
	$reg = $_POST["register_no"];
	$script = "import UserDetailsConnector; print(UserDetailsConnector.isRegistered(\\\"".$reg."\\\"))";
	$cmd = "/usr/bin/python3.4 -c \"".$script."\"";
	if(exec($cmd, $returnVar)=="True")
	{
		echo "This register number is already registered!";
	}
	else
	{
		$insertcmd = "UserDetailsConnector.insert(\\\"".$name."\\\",\\\"".$reg."\\\",\\\"".$dob."\\\")";
		$script = "import UserDetailsConnector; ".$insertcmd;
		$cmd = "/usr/bin/python3.4 -c \"".$script."\"";
		exec($cmd, $returnVar);
		echo "Registered successfully!";
	}	
?>
