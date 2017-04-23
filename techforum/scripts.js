window.onload=function()
{
	var xmlhttp;
	if (window.XMLHttpRequest) 
	{
    	xmlhttp = new XMLHttpRequest();
    } 
    else 
   	{
	    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function() 
	{
        if (xmlhttp.readyState == XMLHttpRequest.DONE ) 
		{
		    if(xmlhttp.status == 200)
			{
        		if(xmlhttp.responseText.trim()=="YES")
        		{
        			location.replace("main.html");
        			return true;
        		}
       		}
        }
	}
	xmlhttp.open("POST", "handlers/isLoggedin.php", true);
	xmlhttp.send();
    return false;
}
function validateRegister()
{
	password = document.getElementById("registerPassword").value;
	verify = document.getElementById("passwordVerify").value;
	if((password.localeCompare(verify))!=0)
	{
		document.getElementById("registerError").innerHTML = "Passwords do not match!";
		return false;
	}
	else
	{
		username=document.getElementById("registerUsername").value;
		name = document.getElementById("registerName").value;
		reg = document.getElementById("registerNo").value;
		email = document.getElementById("email").value;
		message = "username="+username+"&password="+password+"&name="+name+"&registerNo="+reg+"&email="+email;
		var xmlhttp;
		if (window.XMLHttpRequest) 
		{
			xmlhttp = new XMLHttpRequest();
		} 
		else 
	   	{
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange = function() 
		{
		    if (xmlhttp.readyState == XMLHttpRequest.DONE ) 
			{
				if(xmlhttp.status == 200)
				{
		    		document.getElementById("registerError").innerHTML = xmlhttp.responseText;
		    		return false;
		   		}
		    }
		}
		xmlhttp.open("POST", "handlers/register.php", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", message.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.send(message);
		return false;
	}
}

function login()
{
	var xmlhttp;
	username = document.getElementById("username").value;
	password = document.getElementById("password").value;
	message = "username="+username+"&password="+password;
	if (window.XMLHttpRequest) 
	{
    	xmlhttp = new XMLHttpRequest();
    } 
    else 
   	{
	    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function() 
	{
        if (xmlhttp.readyState == XMLHttpRequest.DONE ) 
		{
		    if(xmlhttp.status == 200)
			{
        		if(xmlhttp.responseText.trim()=="OK")
        		{
        			location.href="main.html";
        			return true;
        		}
        		else
        		{
        			document.getElementById("loginError").innerHTML = xmlhttp.responseText;
        			return false;
        		}
       		}
        }
	}
	xmlhttp.open("POST", "handlers/login.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", message.length);
	xmlhttp.setRequestHeader("Connection", "close");
    xmlhttp.send(message);
    return false;
}
