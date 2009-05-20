<?php

	function parse($result, $x, $y)
	{
		// [38,100,2,0,"d=240739&c=b9","t4"],
		// [38,101,3,0,"d=239938&c=e4","b04","\u5a1c\u5a1c\u8389","\u5a1c\u5a1c\u8389","54",""],
		// [-6,-6,10,0,"d=325601&c=f2","t0"],
		// [-6,-5,3,0,"d=324800&c=db","b04","SUN-\u03b5\u0457\u0437","cccufo","57",""],
		// [-6,-4,3,0,"d=323999&c=04","b04","alexander","alexander","47",""],

		//if(!preg_match_all('/karte\.php\?d=([0-9]+)&c=([a-z0-9]{2})/', $result, $matches, PREG_SET_ORDER)) die("Can not parse karte page.");
		if(!preg_match_all('/\[([-0-9]+),([-0-9]+),([0-9]+),([0-9]+),"d=([0-9]+)&c=([a-z0-9]{2})","[a-z][0-9]{1,2}"/', $result, $matches, PREG_SET_ORDER)) die("Can not parse karte page.");
		if(count($matches) != 169) die("Bad count.");

		$imgs = array(); 
		foreach ($matches as $val) {
			$x = $val[1];
			$y = $val[2];
			$crop_type = $val[3];
			$oasis = $val[4];
			$d = $val[5];
			$c = $val[6];
			
			if($crop_type > 0){
				// Note: crop_type 3 is player occupied place
				if($crop_type == '6'){
					$crops = 15;
				} else if($crop_type == '1'){
					$crops = 9;
				} else if($crop_type == '7'){
					$crops = 7;
				} else if($crop_type == '8'){
					$crops = 7;
				} else if($crop_type == '9'){
					$crops = 7;
				} else {
					$crops = 6;
				}
				
			    $sql = "replace crop_crawler(x, y, d, c, crops) values ($x, $y, $d, '$c', $crops)";

			}else{
				$sql = "replace crop_crawler(x, y, d, c, oasis) values ($x, $y, $d, '$c', '$oasis')";
			}
		    
		    if(!mysql_query($sql)) die(mysql_error());
		    
		}
		echo "\n";
	}

	// ----------------------------------------------------------------------------
	// Main
	// ----------------------------------------------------------------------------

	require_once('common.php');
	require_once('db.php');
	require_once('login.php');
	
	// http://speed.travian.tw/ajax.php?f=k7&x=-6&y=-6&xx=6&yy=6
	
	login();

	$referer = "http://$server/karte.php";
	$url = "http://$server/ajax.php?f=k7&";
	mysql_query("LOCK TABLES crop_crawler WRITE");
	
	
	for($x = -400; $x < 400; $x = $x + 13){
		for($y = -400; $y < 400; $y = $y + 13){

			$x1 = $x - 6;
			$y1 = $y - 6;
			
			$x2 = $x + 6;
			$y2 = $y + 6;
			
			$sql = "select count(1) from crop_crawler where x = $x and y = $y";
			$res = mysql_query($sql);
			if(!$res) die(mysql_error());
			$row = mysql_fetch_row($res);
			
			if($row[0] > 0){
				continue;
			}
			
			$ch = my_curl_init(true);
			curl_setopt($ch, CURLOPT_URL, $url . "x=$x1&y=$y1&xx=$x2&yy=$y2");
			curl_setopt($ch, CURLOPT_REFERER, $referer);

			$result = curl_exec ($ch);
			curl_close ($ch);
			
			echo "($x, $y)";
			parse($result, $x, $y);
		}
	}

?>
