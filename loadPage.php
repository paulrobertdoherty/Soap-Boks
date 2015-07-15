<?php
    //Below creates a page from a url that can be displayed in an iframe
    
    //Make sure errors are displayed
    error_reporting(E_ALL);
    ini_set('display_errors', TRUE);
    ini_set('display_startup_errors', TRUE);
    
    function startsWith($haystack, $needle) {
    	// search backwards starting from haystack length characters from the end
    	return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
    }
    
    function replace($s, $do, $dontStart, $dontStart2, $toAdd) {
        $array = explode($do, $s);
        $final = $array[0];
        
        for ($i = 1; $i < sizeof($array); $i++) {
            if (startsWith($array[$i], $dontStart) || (strlen($dontStart2) !== 0 && startsWith($array[$i], $dontStart2))) {
                $final = $final . $do . $array[$i];
                continue;
            }
            
            $final = $final . $toAdd . $array[$i];
        }
        
        return $final;
    }
    
    $url = $_GET["url"];
    
    //Get the html content from the url
    $parse = parse_url($url);
    $domain = "//soapboks.co/loadPage.php?url=" . $parse['scheme'] . '://' . $parse['host'];
    $content = file_get_contents($url);
    
    $splitURL = explode(".", $url);
    
    //The extension of the url
    $ex = $splitURL[sizeof($splitURL) - 1];
    
    //Set header
    switch($ex) {
    	case "css":
    		header('Content-type: text/css');
    		break;
    	case "js":
    		header('Content-type: text/js');
    		break;
    	default:
    		header('Content-type: text/html');
    		break;
    }
    
    if ($ex == "css") {
    	$content = replace($content, "url('", "http", "", "url('" . $domain);
    } else {
   	$content = replace($content, 'src="/', '/', "http", 'src="' . $parse['scheme'] . '://'. $parse['host'] . '/');
   	$content = replace($content, 'src="', '//', "http", 'src="' . $parse['scheme'] . '://'. $parse['host'] . '/');
   	$content = str_replace('href="//', 'href="//soapboks.co/loadPage.php?url=//', $content);
    	$content = str_replace('href="http', 'href="//soapboks.co/loadPage.php?url=http', $content);
    	$content = replace($content, 'href="/', '/', "", 'href="' . $domain . "/");
   	$content = replace($content, 'href="', '//', "http", 'href="' . $domain);
    }

    //Return the page
    echo $content;
?>