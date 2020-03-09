<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>ClassChat</title>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
    <link rel="stylesheet" href="style.css" type="text/css" />
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/seedrandom/3.0.5/seedrandom.min.js"></script>
    <script type="text/javascript" src="chat.js"></script>

    <script type="text/javascript">

        var chatroom = localStorage.getItem('room');
        var lcsname = localStorage.getItem('name');
        var name = lcsname;
        
        if (!chatroom || chatroom === 'null') {
            alert("ERROR: No chat room found");
            window.location.href = "index.php";
        }

        document.cookie = "chatroom=" + chatroom;

        function signOut() {
            localStorage.removeItem('name');
            localStorage.removeItem('room');
            sessionStorage.removeItem('pwdTemp');
            window.location.href = "index.php";
        }

        function generateLink() {
            $('#name-area a').attr('href', 'javascript:copyToClipboard("https://classchat.calclover2514.repl.co/room.php?room=' + chatroom + '")')
            $('#name-area a').html('https://classchat.calclover2514.repl.co/room.php?room=' + chatroom)
        }

        function copyToClipboard(text) {
            var dummy = document.createElement("textarea");
            document.body.appendChild(dummy);
            dummy.value = text;
            dummy.select();
            document.execCommand("copy");
            document.body.removeChild(dummy);
            alert('Link copied to clipboard');
        }

        function insertAtCaret(areaId, text) {
            var txtarea = document.getElementById(areaId);
            var scrollPos = txtarea.scrollTop;
            var caretPos = txtarea.selectionStart;

            var front = (txtarea.value).substring(0, caretPos);
            var back = (txtarea.value).substring(txtarea.selectionEnd, txtarea.value.length);
            txtarea.value = front + text + back;
            caretPos = caretPos + text.length;
            txtarea.selectionStart = caretPos;
            txtarea.selectionEnd = caretPos;
            txtarea.focus();
            txtarea.scrollTop = scrollPos;
            $("#sendie").focus();
        }

        function insertAtCursor(myField, myValueBefore, myValueAfter) {
            if (document.selection) {
                myField.focus();
                document.selection.createRange().text = myValueBefore + document.selection.createRange().text + myValueAfter;
            } else if (myField.selectionStart || myField.selectionStart == '0') {
                var startPos = myField.selectionStart;
                var endPos = myField.selectionEnd;
                myField.value = myField.value.substring(0, startPos)+ myValueBefore+ myField.value.substring(startPos, endPos)+ myValueAfter+ myField.value.substring(endPos, myField.value.length);
            } 
            $("#sendie").focus();
        }

        function showHide() {
            document.getElementById("myDropdown").classList.toggle("show");
        }
        function showHideA() {
            document.getElementById("myDropdownA").classList.toggle("show-a");
        }

        window.onclick = function(event) {
            if (!event.target.matches('.dropbtn')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                var i;
                for (i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
            if (!event.target.matches('.dropbtn-a')) {
                var dropdowns = document.getElementsByClassName("dropdown-content-a");
                var i;
                for (i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show-a')) {
                        openDropdown.classList.remove('show-a');
                    }
                }
            }
        }

        try {
            var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            var recognition = new SpeechRecognition();
        }
        catch(e) {
            alert("ERROR: Speech Recognition not supported\n" + e);
        }

        recognition.onstart = function() { 
            $("#instructions").html('Voice recognition activated. Try speaking into the microphone.');
            var snd = new Audio("sounds/start.wav");
            snd.play();
            $("#stop-rec").focus();
        };
        recognition.onend = function() {
            $("#instructions").html('Voice recognition complete.');
            var snd = new Audio("sounds/stop.wav");
            snd.play();
        };
        recognition.onspeechend = function() {
            $("#instructions").html('Voice recognition complete.');
            var snd = new Audio("sounds/stop.wav");
            snd.play();
        };
        recognition.onerror = function(event) {
            if (event.error == 'no-speech') {
                $("#instructions").html('No speech was detected. Try again.');  
            }
        };

        recognition.onresult = function(event) {
            var current = event.resultIndex;
            var transcript = event.results[current][0].transcript + " ";
            if ($('#sendie').val()) {
                var noteContent = $('#sendie').val();
            } else {
                var noteContent = '';
            }
            noteContent += transcript;
            $('#sendie').val(noteContent);
            $("#sendie").focus();
        }

        var chat =  new Chat();

        $(document).ready(function() {
            if (name === 'admin' || name === 'Admin') {
                $("#name-area").html("Chat room: <span>" + filterString(atob(chatroom)) + "</span><a href=''></a><br>You are: <span>" + filterString(name) + "</span><button type='button' onclick='signOut()' style='margin: 0px 0px 0px 0px;'>Sign Out</button> <form action='chat.php' method='post'><input type='submit' name='clearChat' value='Clear Chat'/></form> <form action='chat.php' method='post'><input type='submit' name='deleteLastPost' value='Remove Last Post'/></form> <button type='button' onclick='deleteRoom()' style='margin: 0px 4px 0px 4px;'>Delete Room</button>");
            } else {
                $("#name-area").html("Chat room: <span>" + filterString(atob(chatroom)) + "</span><a href=''></a><br>You are: <span>" + filterString(name) + "</span><button type='button' onclick='signOut()'>Sign Out</button>");
            }
            if (localStorage.getItem('signin') === 'true') {
                localStorage.removeItem('signin');
                chat.update();
    			chat.send(name + ' entered the room.', 'Admin');
            }
        });

    	$(function() {
    		chat.getState(); 
            $("#sendie").keydown(function(event) {  
                var key = event.which;  
                if (key >= 33) {
                    var maxLength = $(this).attr("maxlength");  
                    var length = this.value.length;  
                    if (length >= maxLength) {  
                        event.preventDefault();  
                    }  
                }  
    		});
    		$('#sendie').keyup(function(e) {
    			if (e.keyCode == 13) { 
                    var text = $(this).val();
    				var maxLength = $(this).attr("maxlength");  
                    var length = text.length;  
                    if (length <= maxLength + 1) { 
    			        chat.send(text, name);	
    			        $(this).val("");
                    } else {
    					$(this).val(text.substring(0, maxLength));
    				}
    			}
            });
    	});
    </script>
</head>

<body onload="setInterval('chat.update()', 1000)">
    <div id="page-wrap">
        <h2>ClassChat<a href="javascript:generateLink()">Generate room link</a></h2>
        <div id="name-area"></div>
        <div id="chat-wrap">
            <h1>Loading <img style="opacity: 0.4; position: absolute; -ms-transform: translate(-50%, 50%); transform: translate(-50%, 50%);" src="images/loading.gif" alt="..." width="auto" height="10px"/></h1>
            <div id="chat-area"></div>
        </div>
        <p id="msg">Type a message: </p>
        <br>
        <form id="send-message-area">
            <button type='button' style='font-weight: bold;' onclick="insertAtCursor(document.getElementById('sendie'), '<b>', '</b>')">B</button>
            <button type='button' style='font-style: italic;' onclick="insertAtCursor(document.getElementById('sendie'), '<i>', '</i>')">&nbsp;i&nbsp;</button>
            <button type='button' style='text-decoration: underline;' onclick="insertAtCursor(document.getElementById('sendie'), '<u>', '</u>')">U</button>
            <button type='button' onclick="insertAtCursor(document.getElementById('sendie'), '<q>', '</q>')">Quote</button>
            <button type='button' onclick="insertAtCursor(document.getElementById('sendie'), '<code>', '</code>')">Code</button>
            <button type='button' onclick="insertAtCursor(document.getElementById('sendie'), '<img src=&quot;', '&quot; alt=&quot;This image no longer exists&quot; height=&quot;auto&quot; width=&quot;auto&quot;>')">Image</button>
            <button type='button' style='text-decoration: underline;' onclick="insertAtCursor(document.getElementById('sendie'), '<a target=&quot;_blank&quot; href=&quot;', '&quot;>Website</a>')">URL</button>
            Color:
            <div class="dropdown">
                <button type='button' onclick="showHide()" class="dropbtn">Default ▼</button>
                <div id="myDropdown" class="dropdown-content">
                    <a href="javascript:insertAtCursor(document.getElementById('sendie'), '', '')">Default</a>
                    <a style="color: darkred;" href="javascript:insertAtCursor(document.getElementById('sendie'), '<p style=&quot;color: darkred;&quot;>', '</p>')">Dark Red</a>
                    <a style="color: red;" href="javascript:insertAtCursor(document.getElementById('sendie'), '<p style=&quot;color: red;&quot;>', '</p>')">Red</a>
                    <a style="color: orange;" href="javascript:insertAtCursor(document.getElementById('sendie'), '<p style=&quot;color: orange;&quot;>', '</p>')">Orange</a>
                    <a style="color: brown;" href="javascript:insertAtCursor(document.getElementById('sendie'), '<p style=&quot;color: brown;&quot;>', '</p>')">Brown</a>
                    <a style="color: yellow;" href="javascript:insertAtCursor(document.getElementById('sendie'), '<p style=&quot;color: yellow;&quot;>', '</p>')">Yellow</a>
                    <a style="color: green;" href="javascript:insertAtCursor(document.getElementById('sendie'), '<p style=&quot;color: green;&quot;>', '</p>')">Green</a>
                    <a style="color: olive;" href="javascript:insertAtCursor(document.getElementById('sendie'), '<p style=&quot;color: olive;&quot;>', '</p>')">Olive</a>
                    <a style="color: cyan;" href="javascript:insertAtCursor(document.getElementById('sendie'), '<p style=&quot;color: cyan;&quot;>', '</p>')">Cyan</a>
                    <a style="color: blue;" href="javascript:insertAtCursor(document.getElementById('sendie'), '<p style=&quot;color: blue;&quot;>', '</p>')">Blue</a>
                    <a style="color: darkblue;" href="javascript:insertAtCursor(document.getElementById('sendie'), '<p style=&quot;color: darkblue;&quot;>', '</p>')">Dark Blue</a>
                    <a style="color: indigo;" href="javascript:insertAtCursor(document.getElementById('sendie'), '<p style=&quot;color: indigo;&quot;>', '</p>')">Indigo</a>
                    <a style="color: violet;" href="javascript:insertAtCursor(document.getElementById('sendie'), '<p style=&quot;color: violet;&quot;>', '</p>')">Violet</a>
                    <a style="color: white;" href="javascript:insertAtCursor(document.getElementById('sendie'), '<p style=&quot;color: white;&quot;>', '</p>')">White</a>
                    <a style="color: black;" href="javascript:insertAtCursor(document.getElementById('sendie'), '<p style=&quot;color: black;&quot;>', '</p>')">Black</a>
                </div>
            </div>
            Size:
            <div class="dropdown-a">
                <button type='button' onclick="showHideA()" class="dropbtn-a">Normal ▼</button>
                <div id="myDropdownA" class="dropdown-content-a">
                    <a href="javascript:insertAtCursor(document.getElementById('sendie'), '<p style=&quot;font-size: 7px;&quot;>', '</p>')">Tiny</a>
                    <a href="javascript:insertAtCursor(document.getElementById('sendie'), '<p style=&quot;font-size: 9px;&quot;>', '</p>')">Small</a>
                    <a href="javascript:insertAtCursor(document.getElementById('sendie'), '', '')">Normal</a>
                    <a href="javascript:insertAtCursor(document.getElementById('sendie'), '<p style=&quot;font-size: 18px;&quot;>', '</p>')">Large</a>
                    <a href="javascript:insertAtCursor(document.getElementById('sendie'), '<p style=&quot;font-size: 24px;&quot;>', '</p>')">Huge</a>
                </div>
            </div>
            <div id="sendie-wrap">
                <textarea id="sendie" maxlength="2000" autofocus></textarea>
            </div>
            <div id="instructions">Voice Recognition:</div>
            <button type='button' onclick='recognition.start()'>Start</button>
            <button id="stop-rec" type='button' onclick='recognition.stop()'>Stop</button>
            <button type='button' onclick='$("#sendie").val("").focus()'>Clear</button>
        </form>
    </div>
</body>

</html>

<?php
    if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['clearChat'])) {
        clear();
    }
    if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['deleteLastPost'])) {
        delete();
    }
    function clear() {
        $pwdtemp = fgets(fopen('chat/' . $_COOKIE['chatroom'] . '.txt', 'r'));
        file_put_contents('chat/' . $_COOKIE['chatroom'] . '.txt', $pwdtemp);
        header('Location: chat.php'); 
    }
    function delete() {
        $lines = file('chat/' . $_COOKIE['chatroom'] . '.txt'); 
        $last = sizeof($lines) - 1 ; 
        unset($lines[$last]); 
        $fp = fopen('chat/' . $_COOKIE['chatroom'] . '.txt', 'w'); 
        fwrite($fp, implode('', $lines)); 
        fclose($fp);
        header('Location: chat.php'); 
    }
?>