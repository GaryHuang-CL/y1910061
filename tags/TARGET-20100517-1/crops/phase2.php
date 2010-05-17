<?php

	function parse($result, $x, $y)
	{
		$pos = strpos($result, '<div class="village_map');
		
		if($pos === false){
			echo("failed to strpos.\n");
			return false;
		}
		

		// f6 is 15 crops, f1 is 9 crops
		// f7, f8, f9 is 7 crops
		
		// w3 w6 w9 w10 is 25%
		// w12 is 50%
		if(!preg_match('#<img src="img/x\.gif" id="([a-z])([0-9]{1,2})"#', $result, $matches, 0, $pos)){
			echo("failed to parse.\n");
			return false;
		}
		
		if($matches[1] == 'f'){

			if($matches[2] == '6'){
				$crops = 15;
			} else if($matches[2] == '1'){
				$crops = 9;
			} else if($matches[2] == '7'){
				$crops = 7;
			} else if($matches[2] == '8'){
				$crops = 7;
			} else if($matches[2] == '9'){
				$crops = 7;
			} else {
				$crops = 6;
			}
			
			$sql = "update crop_crawler set crops = $crops where x = $x and y = $y";
			
		}else if($matches[1] == 'w'){
			$oasis = $matches[1] . $matches[2];
			
			$sql = "update crop_crawler set oasis = '$oasis' where x = $x and y = $y";
		}else {
			echo("expect f or w.\n");
			return false;
		}
		
		
		
		if(!mysql_query($sql)) die(mysql_error());
		
		return true;

	}

	// ----------------------------------------------------------------------------
	// Main
	// ----------------------------------------------------------------------------
	// don't need phase2
	exit();
	require_once('common.php');
	require_once('db.php');
	require_once('login.php');
	
	login();
	
	if($argc < 2){
		die("x needed.");
	}
	
	$x_range = intval($argv[1]);
	//sleep(($x_range + 400) / 2);
	
	$sql = "select x, y, d, c from crop_crawler where x >= $x_range and x < $x_range + 100 and crops is null and oasis is null";
	$res = mysql_query($sql);
	if(!$res) die(mysql_error());

	while($row = mysql_fetch_row($res)){
		$x = $row[0];
		$y = $row[1];
		$d = $row[2];
		$c = $row[3];
		
		$url = "http://$server/karte.php?d=$d&c=$c";

		while(true){
			$ch = my_curl_init(true);
			curl_setopt($ch, CURLOPT_URL,$url);
			$result = curl_exec ($ch);
			curl_close ($ch);
			
			echo "($x, $y)\n";
			
			// echo $result;
			if(parse($result, $x, $y)) break;
			
			sleep(5);
			login();

		}
		
		//exit();
	}
	

?>
