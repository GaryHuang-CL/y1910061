<?php

	function parse($result, $x, $y)
	{
		//<img style="position:absolute; left:432px; top:-20px" src="http://img.travian.com/hki/img/un/m/o1.gif">
		//<img style="position:absolute; left:469px; top:0px" src="http://img.travian.com/hki/img/un/m/t0.gif">
		if(!preg_match_all('/<img style="position:absolute; left:[-0-9]+px; top:[-0-9]+px" src=.+?([a-z][0-9]{1,2})\.gif">/', $result, $matches, PREG_SET_ORDER)) die("Can not parse karte page.");
		if(count($matches) != 169) die("Bad parse 1.");

		$imgs = array(); 
		foreach ($matches as $val) {
		    array_push($imgs, $val[1]);
		}


		// <area href="#" onclick='opener.location.href="karte.php?d=202236&c=16", self.close()' coords="380,473,416,453,453,473,416,493" shape="poly" onmouseover="x_y('-17','148')" onmouseout="x_y('-21','154')"/>
		$ds = array();
		$cs = array();
		
		if(!preg_match_all('/<area href="#" onclick=\'opener\.location\.href="karte.php\?d=([0-9]+)&c=([a-z0-9][a-z0-9])", self\.close\(\)\'/', $result, $matches, PREG_SET_ORDER)) die("Can not parse karte page 2.");
		if(count($matches) != 169) die("Bad parse 2.");

		foreach ($matches as $val) {
		    array_push($ds, $val[1]);
		    array_push($cs, $val[2]);
		}

		
		$idx = 0;
		$sql = "replace into crop_crawler(x, y, d, c, oasis) values";
		for($i = $y + 12; $i >= $y; $i--){
			for($j = $x; $j <= $x + 12;  $j++){

				$oasis = '';
				if(substr($imgs[$idx], 0, 1) == 'o'){
					$oasis = $imgs[$idx];
				}
				
				$d = $ds[$idx];
				$c = $cs[$idx];
				
				if($idx < 168){
					$sql = $sql . "($j, $i, $d, '$c', '$oasis'),";
				}else{
					$sql = $sql . "($j, $i, $d, '$c', '$oasis')";
				}
				
				$idx++;
			}
		}
		
		if(!mysql_query($sql)) die(mysql_error());

	}

	// ----------------------------------------------------------------------------
	// Main
	// ----------------------------------------------------------------------------

	require_once('common.php');
	require_once('db.php');
	require_once('login.php');
	
	login();
	$url = "http://$server/karte2.php";
	
	for($x = -400; $x < 400; $x = $x + 13){
		for($y = -400; $y < 400; $y = $y + 13){

			$ox = $x + 6;
			$oy = $y + 6;
			
			$sql = "select count(1) from crop_crawler where x = $ox and y = $oy";
			$res = mysql_query($sql);
			if(!$res) die(mysql_error());
			$row = mysql_fetch_row($res);
			
			if($row[0] > 0){
				continue;
			}
			
			// xp=35&yp=-25&s1.x=28&s1.y=15&s1=ok
			$postfields = "xp=$ox&yp=$oy&s1.x=28&s1.y=15&s1=ok";
			echo $postfields . "\n";
			
			$ch = my_curl_init(true);
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_REFERER, $url);

			$result = curl_exec ($ch);
			curl_close ($ch);
			
			parse($result, $x, $y);
		 	//echo $result;
		}
	}
	
	mysql_query("delete from crop_crawler where x>400 or y>400");

?>
