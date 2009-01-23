<?php
	function code2utf($num){
	  if($num<128)return chr($num);
	  if($num<2048)return chr(($num>>6)+192).chr(($num&63)+128);
	  if($num<65536)return chr(($num>>12)+224).chr((($num>>6)&63)+128).chr(($num&63)+128);
	  if($num<2097152)return chr(($num>>18)+240).chr((($num>>12)&63)+128).chr((($num>>6)&63)+128) .chr(($num&63)+128);
	  return '';
	}

	function decode_javascript_unicode($str){
		return preg_replace('~\\\u([0-9a-f]{4})~ei', 'code2utf(hexdec("\\1"))', $str);

	}

	function load_my_villages()
	{
		global $account;
		
		$sql = "select id, x, y from villages where noraid = 0 and account = $account";
		$res = mysql_query($sql);
		if(!$res) die(mysql_error());
		
		$my_villages = array();
		
	    while($row = mysql_fetch_row($res)){
	    	$id = $row[0];
	    	$x = $row[1];
	    	$y = $row[2];
	    	
	    	$my_villages[$id] = array($x, $y);
	    }
	    
	    return $my_villages;
	}

	function get_nearest_village_and_distance($my_villages, $target_x, $target_y)
	{
		$min_distance = 9999;
		$min_id = 0;
		
		foreach($my_villages as $id => $value){
			$x = $value[0];
			$y = $value[1];
			
		    $distance  = round(sqrt(pow(abs($x - $target_x), 2) + pow(abs($y - $target_y), 2)), 2);
		    
		    if($distance < $min_distance){
		    	$min_distance = $distance;
		    	$min_id = $id;
		    }
		}
		
		return array($min_id, $min_distance);
	}

	function parse($result, $my_villages)
	{
		// <area href="karte.php?d=358325&amp;c=26" onmouseover="map('”ò—´äˆ&#21345;“I‘ºäµ','”ò—´äˆ&#21345;','28','','-123','-47')"
		// {"x":"-9","y":"-54","src":"http:\/\/img.travian.com\/hki\/\/img\/un\/m\/d04.gif","ew":"39","name":"ROYAL","dname":"ROYAL\u7684\u6751\u838a","ally":"","href":"karte.php?d=364046&c=b3"},
		// {"x":"-7","y":"-53","src":"http:\/\/img.travian.com\/hki\/\/img\/un\/m\/o8.gif","ew":null,"name":null,"dname":null,"ally":null,"href":"karte.php?d=363247&c=36"}],
		// [{"x":"-6","y":"-59","src":"http:\/\/img.travian.com\/hki\/\/img\/un\/m\/d04.gif","ew":"42","name":"\u5929\u4e0b","dname":"\u5929\u4e0b","ally":"","href":"karte.php?d=368054&c=6e"},
		// {"x":"75","y":"24","src":"img\/un\/m\/t1.gif","ew":null,"name":null,"dname":null,"ally":null,"href":"karte.php?d=301652&c=78","title":""}
		if(!preg_match_all('/\{"x":"([-0-9]+)","y":"([-0-9]+)","src":"[^"]+?","ew":"([0-9]+)","name":"(.+?)","dname":"(.+?)","ally":"(.*?)","href":"karte\.php\?d=([0-9]+)&c=(.+?)"/', $result, $matches, PREG_SET_ORDER)){
			echo ("Can not parse karte page.\n");
			return false;
		}

		foreach ($matches as $val) {
		    $x            = $val[1];
		    $y            = $val[2];
		    $population   = $val[3];
		    $player_name  = mysql_real_escape_string(decode_javascript_unicode($val[4]));
		    $village_name = mysql_real_escape_string(decode_javascript_unicode($val[5]));
		    $ally         = mysql_real_escape_string(decode_javascript_unicode($val[6]));
		    $d            = $val[7];
		    $c            = $val[8];
		    
		    list($nearest_village, $distance) = get_nearest_village_and_distance($my_villages, $x, $y);
		    
		    $sql = "replace into populations(daystamp, x, y, player_name, village_name, population, ally_name, d, c, distance, nearest_village) values(to_days(now()), $x, $y, '$player_name', '$village_name', $population, '$ally', $d, '$c', $distance, $nearest_village)";
		    
		    if(!mysql_query($sql)) die(mysql_error());
		}
	}

	function get_map($basic_x, $basic_y, $my_villages)
	{
		global $server;
		
		$url = "http://$server/karte.php";

		// 21x21 35x35 49x49 63x63 77x77 91x91
		define("RANGE", 49);

		$range = (RANGE - 7) / 2;

		for($x = $basic_x - $range; $x <= $basic_x + $range; $x = $x + 7){
			for($y = $basic_y - $range; $y <= $basic_y + $range; $y = $y + 7){
			
				// xp=-124&yp=-54&s1.x=&s1.y=&s1=ok
				$postfields = "xp=$x&yp=$y&s1.x=&s1.y=&s1=ok";
				echo $postfields . "\n";
				
				$ch = my_curl_init();
				curl_setopt($ch, CURLOPT_URL,$url);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
				curl_setopt($ch, CURLOPT_REFERER, $url);

				$result = curl_exec ($ch);
				curl_close ($ch);
				
				parse($result, $my_villages);
			}
		}
	}
	
	// ----------------------------------------------------------------------------
	// Main
	// ----------------------------------------------------------------------------
	require_once('common.php');
	require_once('db.php');
	require_once('attack_ac.php');

	// load my villages
	$my_villages = load_my_villages();

	get_map(-15, -101, $my_villages);

	$sql = "delete from populations where `daystamp` < to_days(now()) - 5";
	if(!mysql_query($sql)) die(mysql_error());

?>
