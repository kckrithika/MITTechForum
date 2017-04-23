<?php
	function FullForm($string)
	{
		switch($string)
		{
			case "os":  return "Operating Systems";
						
			case "dbms": return "Database management systems";
			
			case "ds": return "Data structures";
			
			case "web": return "Web designing";
			
			case "java": return "Java";
			
			case "python": return "Python";
			
			case "server": return "Server scripting";
			
			case "ai": return "Artificial Intelligence";
			
			case "c": return "c programming";
			
			case "compilers": return "Compilers";
			
			case "cpp": return "C plus plus";
			
			case "networks": return "Networks";
			
			case "algo": return "Algorithms";
			
			case "sql": return "SQL";
			
			case "cp": return "Competitive programming";
		}
		return "invalid";
	}
	function getArray($string)
	{
		$string = str_replace("[[", "[", $string);
		$string = str_replace("]]", "]", $string);
		$array = explode("],", $string);
		return $array;
	}
	function execute($script)
	{
		$cmd = "/usr/bin/python3.4 -c \"".$script."\"";
		return shell_exec($cmd);
	}
	function getName($user)
	{
		return rtrim(execute("import UserDetailsConnector as ud; print(ud.getName(\\\"".$user."\\\"))"));
	}
	function getRegNo($user)
	{
		return rtrim(execute("import UserDetailsConnector as ud; print(ud.getRegNo(\\\"".$user."\\\"))"));
	}
	function getEmail($user)
	{
		return rtrim(execute("import UserDetailsConnector as ud; print(ud.getEmail(\\\"".$user."\\\"))"));
	}
	function getTopic($user)
	{
		return rtrim(execute("import RankingsConnector as rc; print(rc.getInterestedTopic(\\\"".$user."\\\"))"));
	}
	$user = $_GET["user"];
	if($user=="You")
	{
		session_start();
		$user = $_SESSION["username"];
		session_write_close();
	}
	$name = getName($user);
	$regno = getRegNo($user);
	$email = getEmail($user);
	$interestArray = getArray(getTopic($user));
	$Topics = array();
	foreach($interestArray as $element)
	{
		$element = str_replace("[", "", $element);
		$element = str_replace("]", "", $element);
		$subArray = explode(",", $element);
		if($subArray[1]!=25.000 and $subArray[2]!=8.333)
		{
			$subArray[0] = str_replace('"', '', $subArray[0]);
			array_push($Topics, FullForm($subArray[0]));
		}
	}
?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="../styles/style.css">
		<link rel="shortcut icon" href="../images/icon.ico">	
	</head>
	<body>
		<div class="heading">
			<img src="../images/logo.ico" name="logo"/>
			<h1> Clear your doubts! </h1>
			<hr>
		</div>
		<fieldset class="centerFieldset">
			<legend> <?php echo $name ?> </legend>
			<font class="profile">
				Register number: <br>
				<?php echo $regno ?>
				<br><br>
				e-mail: <br>
				<?php echo $email ?>
				<br><br>
				<?php
					if(sizeof($Topics)==0)
					{
						echo "User has not participated enough to predict interest!";
					} 
					else
					{
						foreach($Topics as $topic)
						{
							echo $topic.", ";
						}
					}
				?>
				<br><br>
			</font>
		</fieldset>
	</body>
</html>
