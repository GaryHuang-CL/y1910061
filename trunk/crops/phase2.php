<?php

	function parse($result, $x, $y)
	{
		// <div id="f6"></div>
		// f6 is 15 crops, f1 is 9 crops
		if(!preg_match('/<div id="f([0-9])"><\/div>/', $result, $matches)){
			echo("failed to parse.\n");
			return false;
		}
		
		$crops = 6;
		if($matches[1] == '6'){
			$crops = 15;
		} else if($matches[1] == '1'){
			$crops = 9;
		}

		$sql = "update crop_crawler set crops = $crops where x = $x and y = $y";
		
		if(!mysql_query($sql)) die(mysql_error());
		
		return true;

	}

	// ----------------------------------------------------------------------------
	// Main
	// ----------------------------------------------------------------------------

	require_once('common.php');
	require_once('db.php');
	require_once('login.php');
	
	login();
	
	if($argc < 2){
		die("x needed.");
	}
	
	$x_range = intval($argv[1]);
	
	$sql = "select x, y, d, c from crop_crawler where x >= $x_range and x < $x_range + 100 and crops is null and trim(oasis) = ''";
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
