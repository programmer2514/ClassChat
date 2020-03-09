<?php
    $function = $_POST['function'];
    date_default_timezone_set("America/New_York");
    
    $log = array();
    
    switch ($function) {
        case ('getpwd'):
            $pwd = trim(fgets(fopen('chat/' . $_COOKIE['chatroom'] . '.txt', 'r')));
            $log['pwd'] = $pwd;
        break;
        case ('setpwd'):
            $pwd = $_POST['pwd'];
            file_put_contents('chat/' . $_COOKIE['chatroom'] . '.txt', $pwd . "\n");
        break;

    	case ('getList'):
            $list = file_get_contents("filter.b64");
            $log['list'] = $list; 
        break;

    	case ('getState'):
        	 if (file_exists('chat/' . $_COOKIE['chatroom'] . '.txt')){
                 $lines = file('chat/' . $_COOKIE['chatroom'] . '.txt');
        	 }
            $log['state'] = count($lines); 
        break;	
    	
    	case ('update'):
        	$state = $_POST['state'];
            $exists = true;
        	if (!file_exists('chat/' . $_COOKIE['chatroom'] . '.txt')) {
                fopen('chat/' . $_COOKIE['chatroom'] . '.txt', 'w');
                $exists = false;
        	}
        	$lines = file('chat/' . $_COOKIE['chatroom'] . '.txt');
        	$count =  count($lines);
        	if ($state == $count){
        		$log['state'] = $state;
        		$log['text'] = false; 
        		$log['exists'] = $exists;
        	} else {
        		$text = array();
                $log['state'] = $state + count($lines) - $state;
        		foreach ($lines as $line_num => $line) {
        			if ($line_num >= $state) {
                        $line = str_replace("\n", "", $line);
                        if (substr($line, 0, 5) == "<span") {
                            $text[] = $line;
                        }
        			}
                }
        		$log['text'] = $text;
        		$log['exists'] = $exists; 
        	}  
        break;
    	 
    	case('send'):
		    $nickname = htmlentities(strip_tags($_POST['nickname']));
			// $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
			$message = $_POST['message'];
            $color = $_POST['color'];
            $textcol = $_POST['textcol'];
		    if (($message) != "\n") {
			    // if(preg_match($reg_exUrl, $message, $url)) {
       			//     $message = preg_replace($reg_exUrl, '<a href="'.$url[0].'" target="_blank">'.$url[0].'</a>', $message);
				// } 
        	    fwrite(fopen('chat/' . $_COOKIE['chatroom'] . '.txt', 'a'), "<span style='background: #" . $color . "; color: " . $textcol . ";'>". $nickname . "</span>" . $message = str_replace("\n", " ", $message) . "<span id='date'>" . date("h:i m/d/Y") . "</span>\n"); 
		    }
        break;
    }
    echo json_encode($log);
?>