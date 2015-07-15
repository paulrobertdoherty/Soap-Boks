<?php
	//Connect to the database
	$con = mysql_connect("localhost", "contentAdmin", "1102 orP kooBcaM");

	//If the connection failed, quit the program and make an error message
	if (!$con) {
		exit ("Can't use the database.  Error " . mysql_connect_error());
	}

	if (!mysql_select_db("PlayApps", $con)) {
		exit ("Can't use the database.  Error " . mysql_connect_error());
	}

	//POST the function and category from the url
	$function = mysql_real_escape_string($_GET["function"]);
	$category = mysql_real_escape_string($_GET["category"]);

	//Do different things for the POST and set function
	switch ($function) {
		case "get":
			//POST a random distribution count in the database with the selected category
			$sql = "SELECT `distCount` FROM `apps` WHERE `category` LIKE '%" . $category . "%';";
			$query = mysql_query($sql, $con) or die(mysql_errno());
			$results = mysql_fetch_array($query);
			error_log($results);
            		$resultSize = sizeof($results);
            
            		//If nothing came up for the category
            		if ($resultSize == 0) {
                		die("No sites are in the " . $category . " category.");
            		}
            
            		$resultIndex = rand(0, $resultSize - 1);
            		$result = $results[$resultIndex];
			
			//POST the package name from the distribution count as finalResult
			$sql = "SELECT `name` FROM `apps` WHERE distCount=" . $result . ";";
			$query = mysql_query($sql, $con) or die(mysql_errno());
			$finalResult = mysql_fetch_array($query)[0];
			
			//The difference between the distribution count of the app and the user's distributions.
			$dif = $result - 1;
			
			//Update the distribution count in the database, deleting the row if necessary
			if ($dif > 0) {
				$sql = "UPDATE `apps` SET distCount=" . $dif . " WHERE name='" . $finalResult . "';";
				mysql_query($sql, $con) or die(mysql_error());
			} else if ($dif == 0) {
				$sql = "DELETE from `apps` WHERE name='" . $finalResult . "';";
				mysql_query($sql, $con) or die(mysql_error());
			} else {
				$sql = "DELETE from `apps` WHERE name='" . $finalResult . "';";
				mysql_query($sql, $con) or die(mysql_error());
			}
			
			//Finally return the package name and new distribution count
			echo $finalResult;
			break;

		case "set":
			//POST the name and distribution count from url
			$name = mysql_real_escape_string($_GET["name"]);
			$distCount = mysql_real_escape_string($_GET["distCount"]);

			//Check if the user has already submitted their app.
			//If they did, override the category and add to the distribution count.
			//If they did not, submit their app.
			$sql = "SELECT `name` FROM `apps` where name='" . $name . "';";
			$results = mysql_fetch_array(mysql_query($sql, $con));

			//If the user did not submit their app yet
			if (empty($results)) {
				//Submit it
				$sql = "INSERT INTO `apps` (`name`, `category`, `distCount`) VALUES ('" . $name . "', '" . $category . "', " . $distCount . ");";
				if (mysql_query($sql, $con)) {
					echo "App distributed successfully!";
				} else {
					//If it doesn't work, make an error message
					echo "App failed to distribute.  Error " . mysql_errno();
				}
			} else {
				//POST the old distribution count and add it to the new count
				$distCount += (int) (mysql_fetch_array(mysql_query("SELECT `distCount` FROM `apps` where name='" . $name . "';", $con))[0]);

				//Update the row with the new information
				$sql = "UPDATE `apps` SET category='" . $category . "', distCount=" . $distCount . " WHERE name='" . $name . "';";
				if (mysql_query($sql, $con)) {
					echo "App distributed successfully!";
				} else {
					//If it doesn't work, make an error message
					echo "App failed to distribute.  Error " . mysql_errno();
				}
			}
			break;
	}

	//Close the connection
	mysql_close($con);
?>