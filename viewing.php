<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Loading Page... - Soap Boks</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    
    <!-- The script controlling the heights of the countdown and iframe-->
    <script>
        "use strict";
        
        //Set the countdown at 5 minutes
        var minutesLeft = 5;
        var secondsLeft = 0;
        
        //The set interval id
        var intId = 0;
        
        //If 60 seconds have passed, subtract a minute
        function subtractSeconds() {
            if (secondsLeft == 0) {
                subtractMinutes();
                secondsLeft = 59;
            } else {
                secondsLeft -= 1;
            }
        }
        
        //Get the link for the site through php
        var link = "<?php
	    //Make sure errors are displayed
            error_reporting(E_ALL);
            ini_set('display_errors', TRUE);
            ini_set('display_startup_errors', TRUE);
            
            function incrementChar($s, $i) {
                $lastChar = $s[$i];
                
                $beginChar = '0';
                $endChar = 'z';
                
                //If the character limit for the current character has been reached, reset the current character and change the next character, if there is one
                if ($lastChar == $endChar) {
                    $s[$i] = $beginChar;
                    if ($i != 0) {
                        return incrementChar($s, $i - 1);
                    } else {
                        return $s . $beginChar;
                    }
                } else {
                    $s[$i] = chr(ord($s[$i]) + 1);
                }
                
                return $s;
            }
        
            //$s: string to be changed
            function increased($s) {
                return incrementChar($s, strlen($s) - 1);
            }
            
            function published($con, $url) {
            	$query = mysql_query("SELECT `shortened` FROM `sharedLinks` WHERE `url`='" . $url . "';");
            	if ($query) {
            	    return mysql_fetch_array($query);
            	} else {
                    error_log(mysql_error());
                    echo $url;
                    return;
                }
            }
            
            //Connect to the database
            $con = mysql_connect("localhost", "standardUser", "1102 orP kooBcaM");
            
            $url = mysql_real_escape_string($_GET["url"]);

            //If the connection failed, just return the url
            if (!$con) {
                error_log(mysql_error());
                echo $url;
                return;
            }

            //If the user can't use the database, return the url again
            if (!mysql_select_db("PlayApps", $con)) {
                error_log(mysql_error());
                echo $url;
                return;
            }
            
            $lastFileName = published($con, $url);
            
            if ($lastFileName[0] == "") {
                //Gets the text file for the last used file name.  If the program fails to open it, it will give the url distributed.
                $lastFileNameFile = fopen(dirname(__FILE__) . "/lastFileName.txt", "r+") or fopen(dirname(__FILE__) . "/lastFileName.txt", "x+") or exit($url);
        
                //The html for the redirect page
                $html = "<!DOCTYPE html><html><head><title>Redirecting...</title></head><body><script type='text/javascript'>window.location='" . $url . "';</script></body></html>";
            
                //Get a new file name from the latest one
                $fileName = increased(file_get_contents("lastFileName.txt"));
            
                //Set it in the text file
                fwrite($lastFileNameFile, $fileName);
                fclose($lastFileNameFile);
            
                //Write the html file
                $htmlFile = fopen(dirname(__FILE__) . "/l/" . $fileName . ".html", "x");
                fwrite($htmlFile, $html);
                fclose($htmlFile);
                
                $query = "INSERT INTO `sharedLinks` (`url`, `shortened`) VALUES ('" . $url . "', " . $fileName . ")";
                if (!mysql_query($query)) {
                    error_log(mysql_error());
                    echo $url;
                    return;
                }
                
                //Finally return the link
                echo "http://soapboks.co/l/" . $fileName . ".html";
            } else {
                //Return the gotten link
                echo "http://soapboks.co/l/" . $lastFileName[0] . ".html";
            }
            
            mysql_close($con);
	    ?>";
        
        
        //Same for minutes as seconds
        function subtractMinutes() {
            if (minutesLeft == 0) {
                //Stop the timer and replace it with a link
                clearInterval(intId);
                $("#timer").replaceWith("<h3>Here's your new link to share with your friends:</h3><br>" + link);
            } else {
                minutesLeft -= 1;
            }
        }
        
        function startTimer() {
            //Every second, subtract seconds and update the timer
            intId = setInterval(function() {
                subtractSeconds();
                $("#timer").text(minutesLeft + ":" + secondsLeft);
            }, 1000);
        }
        
        //The function controlling the heights of the countdown and iframe
        function adjustHeights() {
            //Get the window height
            var windowHeight = window.innerHeight;
            
            //Set the countdown to 10% of the window height and the frame to 90% of the window height
            $(".container").height(windowHeight * 0.1);
            //$(".frame").height(windowHeight * 0.8);
        }
        
        //Set the page title to a modified version of the iframe title
        function setTitle(frame) {
            document.title = frame.contentWindow.document.title + " - Soap Boks";
        }
        
        function onLoadFrame(frame) {
            adjustHeights();
            setTitle(frame);
            startTimer();
        }
    </script>
    
    <!-- Countdown Header-->
    <div class="container" style="text-align: center;">
        <h1 id="timer">Loading...</h1>
    </div>
    
    <!-- The actual site -->
    <div class="container">
    	<div class="embed-responsive embed-responsive-16by9">
    		<iframe class="embed-responsive-item frame" src="<?php echo "/loadPage.php?url=" . $_GET["url"];?>" onload="onLoadFrame(this)"></iframe>
    	</div>
    </div>
    </body>
</html>