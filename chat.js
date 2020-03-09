var instanse = false;
var state;
var mes;
var file;

function getPwd() {
    $.ajax({
		type: "POST",
		url: "process.php",
		data: {  
		   	'function': 'getpwd'
		},
		dataType: "json",
		success: function(data){
			sessionStorage.setItem('pwdTemp', data.pwd);
		},
        async: false
	});
}

function deleteRoom () {
    instanse = true;
    if (confirm("Are you sure?\nThis action cannot be undone.")) {
        $.ajax({
	    	type: "POST",
	    	url: "process.php",
	    	data: {  
	    	   	'function': 'deleteroom'
	    	},
	    	dataType: "json",
	    	success: function(data){
                signOut();
	    	},
            async: false
	    });
    } else {
        setTimeout(updateChat, 1500);
        return false;
    }
}

function filterString(str) {
	$.ajax({
		type: "POST",
		url: "process.php",
		data: {  
		   	'function': 'getList'
		},
		dataType: "json",
		success: function(data){
			var listEnc = data.list;
            var listRaw = atob(listEnc);
            var listCSV = listRaw.split(/,/);
            var i = 0;
            var star = "*";
            for (i in listCSV) {
                var pattern = new RegExp("\\b" + listCSV[i] + "\\b", 'gi');
                str = str.replace(pattern, star.repeat(listCSV[i].length));
            }
		},
        async: false
	});
    return str;
}

function lightOrDark(colorspl) {
    var ra, ga, ba, hspa;

    colorspl = [colorspl.slice(0, 2), colorspl.slice(2, 4), colorspl.slice(4)];
    ra = parseInt(colorspl[0], 16);
    ga = parseInt(colorspl[1], 16);
    ba = parseInt(colorspl[2], 16);

    hspa = Math.sqrt(
    0.299 * (ra * ra) +
    0.587 * (ga * ga) +
    0.114 * (ba * ba)
    );

    if (hspa > 127.5) {
        colorspl = '#000000';
    } else {
        colorspl = '#ffffff';
    }
    return colorspl;
}

function Chat() {
    this.update = updateChat;
    this.send = sendChat;
	this.getState = getStateOfChat;
}

function getStateOfChat() {
	if(!instanse) {
		instanse = true;
		$.ajax({
			type: "POST",
			url: "process.php",
			data: {  
			   	'function': 'getState'
			},
			dataType: "json",
			success: function(data){
				state = data.state;
				instanse = false;
			},
		});
	}	 
}

function updateChat() {
	if(!instanse){
		instanse = true;
	    $.ajax({
			type: "POST",
			url: "process.php",
			data: {  
			   	'function': 'update',
				'state': state,
				'file': file
			},
			dataType: "json",
			success: function(data){
				if(data.text){
					for (var i = 0; i < data.text.length; i++) {
                        $('#chat-area').append($("<div>"+ filterString(data.text[i]) +"</div>"));
                    }								  
                    document.getElementById('chat-area').scrollTop = document.getElementById('chat-area').scrollHeight;
				}
				instanse = false;
				state = data.state;
                if (!data.exists || data.exists === 'false') {
                    var newpwd = null;
                    do {
                        newpwd = prompt("New Room Admin Password:", "");
                    } while (newpwd === null || newpwd === 'null' || newpwd === '')
                    newpwd = btoa(newpwd);
                    setPwd(newpwd);
                    alert("You can now use this password to sign in as the account 'Admin'");
                    updateChat();
                }
                $('#chat-wrap h1').css('display', 'none');
			},
            async: false
		});
	}
	else {
		setTimeout(updateChat, 1500);
	}
}

function sendChat(message, nickname)
{       
    updateChat();
    var numRand = new Math.seedrandom(nickname);
    var bgColor = Math.floor(numRand()*16777215).toString(16);
    var textcol = lightOrDark(bgColor);
    $.ajax({
		type: "POST",
		url: "process.php",
		data: {  
		   	'function': 'send',
			'message': message,
			'nickname': nickname,
			'file': file,
            'color': bgColor,
            'textcol': textcol
		},
		dataType: "json",
		success: function(data){
			updateChat();
		},
	});
}

function setPwd(pwd)
{       
    updateChat();
    $.ajax({
		type: "POST",
		url: "process.php",
		data: {  
		   	'function': 'setpwd',
			'pwd': pwd
		},
		dataType: "json",
		success: function(data){
			updateChat();
		},
	});
}
