<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>ClassChat - Sign in</title>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
    <link rel="stylesheet" href="sistyle.css" type="text/css" />
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script type="text/javascript" src="chat.js"></script>
    <script type="text/javascript">
        localStorage.setItem('signin', 'true');
        
        if (localStorage.getItem('room') && localStorage.getItem('name')) {
            sessionStorage.removeItem('pwdTemp');
            window.location.href = "chat.php";
        }

        $(document).ready(function() {
            if (localStorage.getItem('room')) {
                $('#rname').val(atob(localStorage.getItem('room')));
                $('#uname').focus();
            }
            if (localStorage.getItem('name')) {
                $('#uname').val(localStorage.getItem('name'));
                $('#rname').focus();
            }
        });

        function signIn() {
            var name = $('#uname').val();
            var room = $('#rname').val();
            document.cookie = "chatroom=" + btoa(room);
            if (!name || !room) {
                $('#page-wrap p').html('Incorrect username or room name');
                return false;
            } else {
                $('#page-wrap p').html('');
            }
            if (name === 'admin' || name === 'Admin') {
                getPwd();
                if (sessionStorage.getItem('pwdTemp') !== 'null' && sessionStorage.getItem('pwdTemp') !== null && sessionStorage.getItem('pwdTemp') !== '') {
                    var pwdchk = prompt("Admin Password:", "");
                    if (pwdchk !== atob(sessionStorage.getItem('pwdTemp'))) {
                        alert('Incorrect Password.\nAccess Denied!');
                        return false;
                    }
                }
            }
            localStorage.setItem('room', btoa(room));
            localStorage.setItem('name', name);
            sessionStorage.removeItem('pwdTemp');
            window.location.href = "chat.php";
            return false;
        }
    </script>
</head>

<body>
    <div id="page-wrap">
        <h2>ClassChat</h2>
        <form onsubmit="return signIn();">
            <label for="rname">Room Name:</label><br>
            <input type="text" id="rname" name="rname" value=""><br>
            <label for="uname">Username:</label><br>
            <input type="text" id="uname" name="uname" value=""><br>
            <p></p><br>
            <input type="submit" id="submit" value="Sign In">
        </form>
    </div>
    <div id="footer">
        <h3>ClassChat v0.4.9b</h3>
        <p>&copy; 2020 Benjamin J. Pryor</p>
        <span><a href="">Project GitHub</a> &bull; <a href="https://repl.it/@calclover2514">My Repls</a> &bull; <a href="https://zg-studios.github.io/projects/">My Website</a> &bull; <a href="mailto:programmer2514@gmail.com">Email Me</a></span>
    </div>
</body>

</html>