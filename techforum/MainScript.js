var lastReceivedID=0;
var isDoing = false;

var notificationButtonClicked = false;
var source = new EventSource("handlers/NewsFeed.php");
var loadfirst = true;
source.onmessage = function(event)
{
	var obj = JSON.parse(event.data);
	var division = document.getElementById("NewsFeed");
	var prev = division.scrollHeight;
	division.innerHTML=getHTMLfromJSON(obj)+"<br>"+division.innerHTML;
	var curr = division.scrollHeight;
	division.scrollTop = division.scrollTop+curr-prev;
};
var notisource = new EventSource("handlers/notifeed.php");
notisource.onmessage = function(event)
{
	var obj = JSON.parse(event.data);
	document.getElementById("notifications").style.visibility = "visible";
	var curr_count = document.getElementById("notibutton").innerHTML;
	curr_count = curr_count.substring(5, curr_count.length);
	curr_count = parseInt(curr_count)+1;
	document.getElementById("notibutton").innerHTML = "New: "+curr_count;
	
	if(obj.question.length > 20)
	{
		obj.question = obj.question.substring(0,20)+"...";
	}
	var notiDIV = document.createElement('div');
	notiDIV.id = "noti"+obj.id;
	notiDIV.className = "unseen";
	
	var span = document.createElement('span');
	span.className = "close";
	span.onclick = deleteNoti.bind(span, obj.id, 0);
	span.innerHTML = "x";
	
	var anchor = document.createElement('a');
	anchor.addEventListener("click", setseen.bind(anchor, obj.id, 0));
	anchor.addEventListener("click", displayThread.bind(anchor, obj.thread));
	anchor.className = "link";
	anchor.innerHTML = "Your question "+obj.question+" has received a new "+obj.type+". ";
	
	notiDIV.appendChild(span);
	notiDIV.appendChild(anchor);
	
	/*var preparedHTML = "<div id=\"noti"+obj.id+"\" class=\"unseen\"><span class=\"close\" onclick=\"deleteNoti("+obj.id+", 0);\">x</span><a onclick=\"setseen("+obj.id+", 0); displayThread("+obj.thread+");\" class=\"link\">Your question \""+obj.question+"\" has received a new "+obj.type+".</a></div>";*/
	//var notis = document.getElementById("notifications").innerHTML = preparedHTML+document.getElementById("notifications").innerHTML;
	
	var notis = document.getElementById("notifications");
	notis.insertBefore(notiDIV, notis.firstChild);
}
function loadSuggestions()
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
				var obj = JSON.parse(xmlhttp.responseText.trim());
				for(i in obj.suggestions)
				{

					var sugg = obj.suggestions[i];
					var suggestionDIV = document.createElement('div');
					suggestionDIV.className = "suggestion";
					suggestionDIV.onclick = displayThread.bind(suggestionDIV, sugg.thread);
					suggestionDIV.innerHTML += sugg.question+"<br>"+sugg.asker+"<br>"+sugg.topic+"<hr>";
					document.getElementById("Suggestions").appendChild(suggestionDIV);
				}
       		}
        }
	}
	xmlhttp.open("POST", "handlers/suggestion.php", false);
    xmlhttp.send();
    return false;
}
function loadNoti()
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
				var count=0;
				var response = xmlhttp.responseText.trim();
				var obj = JSON.parse(response);
				var notisDIV = document.createElement('div');
				notisDIV.id="notifications";
				notisDIV.className = "popup";
				for(i in obj.notis)
				{
					var notification_object = obj.notis[i];
					var notiID = notification_object.id;
					var thread = notification_object.thread;
					var question = notification_object.question;
					var type = notification_object.type;
					var seen = notification_object.seen;
					var classofhtml = "seen";
					if(seen==0)
					{
						count+=1;
						classofhtml = "unseen";
					}
					if(question.length > 20)
					{
						question = question.substring(0,20)+"...";
					}
					var notiDIV = document.createElement('div');
					notiDIV.className = classofhtml;
					notiDIV.id = "noti"+notiID;
					var close = document.createElement('span');
					close.className = "close";
					close.onclick = deleteNoti.bind(close, notiID, seen);
					close.innerHTML = "x";
					var anchor = document.createElement('a');
					anchor.className = "link";
					anchor.addEventListener("click", setseen.bind(anchor, notiID, seen));
					anchor.addEventListener("click", displayThread.bind(anchor, thread));
					anchor.innerHTML = "Your question "+question+" has received a new "+type+" .";
					notiDIV.appendChild(close);
					notiDIV.appendChild(anchor);
					notisDIV.appendChild(notiDIV);
				}
				document.getElementById("body").appendChild(notisDIV);
				document.getElementById("notibutton").innerHTML = "New: "+count;
       		}
        }
	}
	xmlhttp.open("POST", "handlers/getNoti.php", true);
    xmlhttp.send();
    return false;	
}
function getHTMLfromJSON(obj)
{
	var answers = obj.answers;
	var commentsresult="";
	for(i in obj.comments)
	{
		var comment = obj.comments[i];
		commentsresult+="<font class=\"Meta\"><span class=\"profileSpan\" onclick=\"openModal(this)\">"+comment.author+"</span> commented</font><br>"+comment.comment+"<br><font class=\"Meta\"> at "+comment.timeOfComment+"</font><br><hr>";
	}
	
	var commentbox = "<form><textarea class=\"renderedbox\" required></textarea><input type=\"submit\" class=\"answerbutton \" value=\"comment\" onclick=\"postComment(this, "+obj.id+"); return false;\"></input></form>";
	alert(commentbox);
	
	var answersresult = "";
	var flag = true;
	
	for(i in answers)
	{
		var answer = answers[i];
		var edit = false;
		if(answer.author=="You")
		{
			flag = false;
			if(obj.closed=="0")
			{
				edit = "true";
			}
		}
		var bestbutton = "";
		if(obj.closed=="0"&&obj.asker=="You")
		{
			bestbutton = "<button onclick=\"selectbest(this, "+answer.id+")\" class=\"bestbutton\">Select as best Answer</button>";
		}
		var tick = "";
		if(answer.isBest=="1")
		{
			tick = "<img src=\"images/tick1.png\" style=\"float:left;\"/>";
		}
		if(edit==false)
		{
			answersresult = answersresult+"<div id=\"answer"+answer.id+"\">"+bestbutton+tick+"<font class=\"Meta\"><span class=\"profileSpan\" onclick=\"openModal(this)\">"+answer.author+"</span> answered</font><br><p id=\"answer\">"+answer.answer+"</p><font class=\"Meta\"> at "+answer.timeOfAnswer+"</font></div><hr>";
		}
		else
		{
			var textwidthclass = "renderedbox";
			if(bestbutton!="")
			{
				textwidthclass = "smallerrenderedbox";
			}
			answersresult = answersresult+"<div id=\"answer"+answer.id+"\">"+bestbutton+tick+"<form><textarea class=\""+textwidthclass+"\" required>"+answer.answer+"</textarea><input type=\"submit\" class=\"answerbutton \" value=\"Edit Answer\" onclick=\"postAnswer(this,  "+obj.id+"); return false;\"></input><hr></form></div>";
		}
	}
					
	var answerbox = "";
	if(flag==true&&obj.closed=="0")
	{
		answerbox = "<form><textarea class=\"renderedbox\" required></textarea><input type=\"submit\" class=\"answerbutton \" value=\"Answer\" onclick=\"postAnswer(this, "+obj.id+"); return false;\"></input></form>";
	}
	var closedresult = "";
	if(obj.closed=="1")
	{
		closedresult="closed";
	}
	var preparedHTML = "<div id=\""+obj.id+"\"class=\"post\"><div class=\"thingreenbackground\"><span class=\"profileSpan\" onclick=\"openModal(this);\">"+obj.asker+"</span> asked</div><div class=\"renderedtext\">"+obj.question+"<font class=\"Meta\"> at "+obj.timeOfPost+" in the topic "+obj.topic+"</font></div><br><div class=\"thingreenbackground\">Comments</div><div class=\"renderedtext\">"+commentsresult+"<br>"+commentbox+"</div><div class=\"thingreenbackground\">Answers</div><div class=\"renderedtext\">"+answersresult+"<br>"+answerbox+"</div><div class=\"thingreenbackground\">"+closedresult+"</div></div><br>";
	return preparedHTML;
}
function loadData()
{
	var xmlhttp;
	if(lastReceivedID!=1)
	{
		var query=0;
		if(lastReceivedID!=0)
		{
			query = lastReceivedID-1;
		}
		var message = "ID="+query;
		if (window.XMLHttpRequest) 
		{
			xmlhttp = new XMLHttpRequest();
		} 
		else 
	   	{
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
		var loading = false;
		xmlhttp.onreadystatechange = function() 
		{
			if (xmlhttp.readyState == XMLHttpRequest.DONE ) 
			{
				loading = false;
				if(xmlhttp.status == 200)
				{
					var string = xmlhttp.responseText;
					var loadingDisplay = document.getElementById("loadIndicator");
					loadingDisplay.parentNode.removeChild(loadingDisplay);
					var obj = JSON.parse(string);
					if(obj.id != "")
					{
						lastReceivedID=obj.id;
						var preparedHTML = getHTMLfromJSON(obj);
						document.getElementById("NewsFeed").innerHTML+=preparedHTML;
						if(loadfirst==true)
						{
							loadfirst = false;
							loadData();
						}
					}
		   		}
		   		isDoing = false;
			}
			else
		   	{
		   		if(loading == false)
		   		{
		   			loading = true;
		   			var loadIndicator = "<center><p id=\"loadIndicator\" class=\"loading\">Loading<span>.</span><span>.</span><span>.</span></p></center>";
		   			document.getElementById("NewsFeed").innerHTML+=loadIndicator;
		   		}
		   	}
		}
		xmlhttp.open("POST", "handlers/new_loadData.php", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", message.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.send(message);
	}
}
function loadTopics()
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
				var obj = JSON.parse(xmlhttp.responseText.trim());
				var select = document.getElementById("topics");
				var count = obj.count;
				for (i=0; i<count; i++)
				{
					index = i.toString();
					var id = obj[index].id;
					var topic = obj[index].topic;
					var opt = document.createElement('option');
					opt.value = id;
					opt.innerHTML = topic;
					select.appendChild(opt);
				}
				var opt = document.createElement('option');
				opt.value = "new";
				opt.innerHTML = "Add a new Topic";
				select.appendChild(opt);
       		}
        }
	}
	xmlhttp.open("POST", "handlers/topicsFetch.php", false);
    xmlhttp.send();
}
function onchangeTopic()
{
	var selectBox = document.getElementById("topics");
	var value = selectBox.options[selectBox.selectedIndex].value;
	if(value=="new")
	{
		raiseAddTopicPopup();
	}
	return;
}
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
				var response = xmlhttp.responseText.trim();
				if(response=="YES")
				{
					loadData();
					loadNoti();
					loadSuggestions();
					loadTopics();
				}
				else
				{
					location.replace("NotLoggedInPage.html");
				}
	   		}
	    }
	}
	xmlhttp.open("POST", "handlers/isLoggedin.php", true);
	xmlhttp.send();
}
function scrollCheck(element)
{
	sh = element.scrollHeight;
	st = element.scrollTop;
	ch = element.clientHeight;
	if(element.scrollHeight - element.scrollTop - element.clientHeight < 5)
	{
		if(isDoing==false)
		{
			isDoing = true;		
			loadData();
		}
	}
}

function postQuestion()
{
	var questionbox = document.getElementById("Question");
	var topicbox = document.getElementById("topics");
	var message = "Question="+questionbox.value+"&Topic="+topicbox.value;
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
				questionbox.value="";
				set=1;
       		}
        }
	}
	xmlhttp.open("POST", "handlers/QuestionHandler.php", false);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", message.length);
	xmlhttp.setRequestHeader("Connection", "close");
    xmlhttp.send(message);
    return false;	
}

function postComment(element, id)
{
	var grandparent = element.parentNode.parentNode;
	var textbox = grandparent.firstChild.firstChild;
	var comment = textbox.value;
	var message = "Comment="+comment+"&id="+id;
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
				textbox.value="";
				set=1;
       		}
        }
	}
	xmlhttp.open("POST", "handlers/CommentHandler.php", false);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", message.length);
	xmlhttp.setRequestHeader("Connection", "close");
    xmlhttp.send(message);
    return false;
}

function postAnswer(element, id, type)
{
	var answerbox = element.previousElementSibling;
	var answer = answerbox.value;
	var message = "Answer="+answer+"&id="+id+"&type="+type;
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
				answerbox.parentElement.removeChild(answerbox);
				element.parentElement.removeChild(element);
       		}
        }
	}
	xmlhttp.open("POST", "handlers/AnswerHandler.php", false);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", message.length);
	xmlhttp.setRequestHeader("Connection", "close");
    xmlhttp.send(message);
    return false;
}

function selectbest(element, id)
{
	var message = "answerid="+id;
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
				var outerdiv = element.parentNode.parentNode;
				var parent = element.parentNode;
				var children = outerdiv.childNodes;
				for (i in children)
				{
					var child = children[i];
					var id = child.id;
					if(id.substring(0,6)=="answer")
					{
						child.removeChild(child.firstChild);
					}
				}
				var img = document.createElement('img');
				img.src = "images/tick1.png";
				parent.insertBefore(img, parent.firstChild);
       		}
        }
	}
	xmlhttp.open("POST", "handlers/selectbest.php", false);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", message.length);
	xmlhttp.setRequestHeader("Connection", "close");
    xmlhttp.send(message);
    return false;
}

function openModal(element)
{
	var message = "user="+element.innerHTML;
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
				var response = xmlhttp.responseText.trim();
				var obj = JSON.parse(response);
				var topics = obj.interested;
				var topicslist = "";
				for (i in topics)
				{
					topicslist+=topics[i]+", ";
				}
				if(topicslist=="")
				{
					topicslist = "User has not participated enough to predict topics of interest!";
				}
				var modalcontent = "<fieldset><legend>"+obj.formalname+"</legend><font class=\"profile\"><br>Topics of interest:<br>"+topicslist+"<br></font></fieldset>";
				var preparedHTML = "<div id=\"modal\" class=\"modal\"><div class=\"modal-content\"><span class=\"close\" onclick = \"closespan(this);\">x</span>"+modalcontent+"</div></div>";
				document.getElementById("NewsFeed").innerHTML+=preparedHTML;
       		}
        }
	}
	xmlhttp.open("POST", "handlers/getInfo.php", false);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", message.length);
	xmlhttp.setRequestHeader("Connection", "close");
    xmlhttp.send(message);
    return false;
}
function closespan(element)
{
	var ToBeRemoved = element.parentElement.parentElement;
	ToBeRemoved.parentElement.removeChild(ToBeRemoved);
}

function notipopup(element)
{
	if(notificationButtonClicked == false)
	{
		notificationButtonClicked = true;
		document.getElementById("notifications").style.visibility = "visible";
	}
	else
	{
		notificationButtonClicked = false;
		document.getElementById("notifications").style.visibility = "hidden";		
	}
}
function deleteNoti(id, seen)
{
	var element = document.getElementById("noti"+id);
	element.parentElement.removeChild(element);
	var message = "id="+id;
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
				if(seen==1)
				{
					var curr_count = document.getElementById("notibutton").innerHTML;
					curr_count = curr_count.substring(5, curr_count.length);
					curr_count = curr_count-1;
					document.getElementById("notibutton").innerHTML = "New: "+curr_count;
		   		}
       		}
        }
	}
	xmlhttp.open("POST", "handlers/deleteNoti.php", false);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", message.length);
	xmlhttp.setRequestHeader("Connection", "close");
    xmlhttp.send(message);
    return false;
}
function setseen(id, seen)
{
	if(seen==0)
	{
		var message = "id="+id;
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
					document.getElementById("noti"+id).className = "seen";
					var curr_count = document.getElementById("notibutton").innerHTML;
					curr_count = curr_count.substring(5, curr_count.length);
					curr_count = curr_count-1;
					document.getElementById("notibutton").innerHTML = "New: "+curr_count;
		   		}
		    }
		}
		xmlhttp.open("POST", "handlers/setseen.php", false);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", message.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.send(message);
	}
}
function displayThread(thread)
{
	var message = "ID="+thread;
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
				var response = xmlhttp.responseText.trim();
				var obj = JSON.parse(response);
				var preparedHTML = getHTMLfromJSON(obj);
				var GoBackButton = "<button class= \"buttonType3\" onclick = \"goBackFromNoti();\">Go Back to Home</button><br>";
				document.getElementById("coverNewsFeed").innerHTML = GoBackButton+preparedHTML;
	   		}
	    }
	}
	xmlhttp.open("POST", "handlers/new_loadData.php", false);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", message.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.send(message);
}
function raiseAddTopicPopup()
{
	var preparedContent = "<fieldset class=\"modal-content\"><legend>Add topic</legend><span class=\"close\" onclick = \"closespan(this);\">x</span><input type=\"text\"/><button class=\"TopicButton\" onclick=\"addTopic(this);\">Add</button></fieldset>";
	var div = document.createElement('div');
	div.id="addNewTopic";
	div.className = "modal";
	div.innerHTML=preparedContent;
	document.getElementById("NewsFeed").appendChild(div);
}
function addTopic(element)
{
	var topic = element.previousElementSibling.value;
	if(topic=="")
	{
		alert("Please enter a valid topic!");
	}
	else
	{
		var message = "topic="+topic;
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
					var popup = document.getElementById("addNewTopic");
					popup.parentNode.removeChild(popup);
					var select = document.getElementById("topics");
					while (select.hasChildNodes()) 
					{
    					select.removeChild(select.lastChild);
					}
					loadTopics();
					var length = select.childNodes.length;
					select.selectedIndex = length-2;
		   		}
			}
		}
		xmlhttp.open("POST", "handlers/addTopic.php", false);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", message.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.send(message);
	}
}
function goBackFromNoti()
{
	document.getElementById("coverNewsFeed").innerHTML = "<div id=\"NewsFeed\" onscroll=\"scrollCheck(this)\"></div>";
	location.reload(true);
}
