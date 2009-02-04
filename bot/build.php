<?php

	function build_warehouse()
	{
		return build_gid(10);
	}

	function build_granary()
	{
		return build_gid(11);
	}
	
	function build_gid($gid)
	{
		global $server;
		$url = "http://$server/build.php?gid=$gid";

		echo $url . "\n";

		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		$result = curl_exec ($ch);
		curl_close ($ch);
		
		// <a href="dorf2.php?a=28&c=23d">
		$ret = preg_match('/<a href="(dorf2\.php\?a=[0-9]+&c=[0-9a-z]+)">/', $result, $matches);
		
		if(!$ret){
			echo "build gid $gid failed.\n";
			return false;
		}

		$url2 = "http://$server/" . $matches[1];
		echo $url2 . "\n";
		
		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL, $url2);
		curl_setopt($ch, CURLOPT_REFERER, $url);
		$result = curl_exec ($ch);
		curl_close ($ch);
		
		return true;
	}

	function switch_village($village)
	{
		global $server;

		if($village == 0) die("village == 0");

		$referer = "http://$server/dorf1.php";
		$url = "http://$server/dorf1.php?newdid=$village";

		// echo $referer . "\n";
		echo $url . "\n";

		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_REFERER, $referer);
		curl_setopt($ch, CURLOPT_URL,$url);
		$result = curl_exec ($ch);
		curl_close ($ch);

		// <a href="?newdid=205861" class="active_vl">
		$ret = preg_match('/<a href="\?newdid=([0-9]+)" class="active_vl">/', $result, $matches);
		
		if(!$ret) die("Failed to switch village.");
		
		return $result;

	}
	
	// $result is dorf1 page
	function get_resource_fields_level($result)
	{
		global $convert;
		
		$out = array();
		// <area href="build.php?id=1" coords="101,33,28" shape="circle" title="Ç´Ç±ÇË ÉåÉxÉã 4">
		// 				<area href="build.php?id=1"
		// coords="101,33,28" shape="circle"
		// title="Holzf&auml;ller Stufe 0" alt="" />

		if(preg_match_all('#<area href="build.php\?id=([0-9]+)"\s+coords="[0-9]+,[0-9]+,[0-9]+" shape="circle"\s+title="(\S+) [^0-9"]+?([0-9]+)"#', $result, $matches, PREG_SET_ORDER)){
			foreach($matches as $match){
				$type = $convert[$match[2]];
				$id = $match[1];
				$level = $match[3];
				
				$out[$type][$id] = $level;
			}
		}else{
			die("Can not get resource fields level.");
		}
		
		return $out;
	}
	
	function get_auto_build_resource_field_id($result)
	{
		global $race;
		global $account;
		global $village;
		
		$level_all = get_resource_fields_level($result);
		
		$res = get_res_info($result);

		$warehouse_capacity = $res[4];
		$granary_capacity = $res[7];
		
		// roman don't auto build farm
//		if($race == "roman"){
//			unset($res[3], $res[4], $res[5], $res[6], $res[7]);
//		}else{
			unset($res[4], $res[5], $res[6], $res[7]);
//		}
		
		asort($res);
		
		// print_r($res);
		
		foreach($res as $type => $num){
			$level_type = $level_all[$type];
			
			asort($level_type);
			//print_r($level_type);
			
			list($id, $level) = each($level_type);
			
			if($level >= 10) continue;
			
			if($granary_capacity < $warehouse_capacity){
				build_granary();
				return false;
			}
			
			$min_capacity = array(1200, 1700, 2300, 3100, 4000, 5000, 6300, 7800, 9600, 14000);
			
			if($min_capacity[$level] > $warehouse_capacity){
				if(build_warehouse()){
					$sql = "insert into `build`(account, village, id) values($account, $village, 26)";
					if(!mysql_query($sql)) die(mysql_error());
				}
				return false;
			}
			
			return $id;
		}
		
		echo "all resource fields level 10.\n";
		
		return false;
		
	}

	function build($village, $result, $newbie = 0)
	{
		global $server;
		global $account;
		global $race;

		// ^<div class="f10 b">.+<span id=timer1>
		if(preg_match('/<div class="f10 b">.+<span id=timer1>/', $result, $matches)){
			if($race == "roman"){
				if(preg_match('/<div class="f10 b">.+<span id=timer2>/', $result, $matches)){
					echo "Building timer2 (roman) found.\n";
					return;
				}
			}else{
				echo "Building timer1 found.\n";
				return;
			}
		}

		$orginal_result = $result;
		
		// Get a build task
		$sql = "select `seq`, `id`, `gid` from `build` where account = $account and `village` = $village order by `seq` limit 1";
		
		$res = mysql_query($sql);
		if(!$res) die(mysql_error());

		$row = mysql_fetch_row($res);
		if(!$row){
			echo "No build task more. Try to auto build.\n";
			
			$id = get_auto_build_resource_field_id($result);
			if(!$id) return;
			
			$seq = -1;
			$gid = 0;
		}else{
			$seq = $row[0];
			$id  = $row[1];
			$gid = $row[2];
		}
		
		$referer = "http://$server/dorf" . ( $id <= 18 ? "1" : "2" ) . ".php";
		$url = "http://$server/build.php?id=$id"; 

		echo $url . "\n";

		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_REFERER, $referer);
		curl_setopt($ch, CURLOPT_URL,$url);
		$result = curl_exec ($ch);
		curl_close ($ch);

		// <a href="dorf2.php?a=28&c=23d">
		$ret = preg_match('/<a href="(dorf[12]\.php\?a=[0-9]+&c=[0-9a-z]+)">/', $result, $matches);
		
		if(!$ret){
			// relay point
			// dorf2.php?a=16&id=39&c=2e0
			if($id == 39) $gid = 16;

			// wall
			// dorf2.php?a=31&id=40&c=061
			if($id == 40) {
				$ret = preg_match('/<a href="(dorf2\.php\?a=[0-9]+&id=' . $id . '&c=[0-9a-z]+)">/', $result, $matches);
			}else if($gid > 0){	
				// <a href="dorf2.php?a=23&id=33&c=0c6">
				$ret = preg_match('/<a href="(dorf[12]\.php\?a=' . $gid . '&id=' . $id . '&c=[0-9a-z]+)">/', $result, $matches);
			}
		}
		
		if(!$ret){
			echo "Busy or lacking resources or failed.\n";
			
			// roman
			if($race == "roman" && $id > 18){
				$id = get_auto_build_resource_field_id($orginal_result);
				
				if(!$id) return;
				
				$seq = -1;
				
				$referer = "http://$server/dorf" . ( $id <= 18 ? "1" : "2" ) . ".php";
				$url = "http://$server/build.php?id=$id"; 

				echo $url . "\n";

				$ch = my_curl_init();
				curl_setopt($ch, CURLOPT_REFERER, $referer);
				curl_setopt($ch, CURLOPT_URL,$url);
				$result = curl_exec ($ch);
				curl_close ($ch);
				
				// <a href="dorf2.php?a=28&c=23d">
				$ret = preg_match('/<a href="(dorf[12]\.php\?a=[0-9]+&c=[0-9a-z]+)">/', $result, $matches);

				if(!$ret) return;
				echo "roman auto building..\n";
			}else{
				return;
			}
		}
		
		$url2 = "http://$server/" . $matches[1];
		echo $url2 . "\n";
		
		if($seq >= 0){
			// Update the target
			$sql = "delete from `build` where account = $account and `seq` = $seq";
			if(!mysql_query($sql)) die(mysql_error());
		}
		
		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL, $url2);
		curl_setopt($ch, CURLOPT_REFERER, $url);
		$result = curl_exec ($ch);
		curl_close ($ch);
		
		//echo $result;
	}
	
	// destroy a building by position id of current village
	/*
		http://s3.travian.jp/build.php?gid=15
		<input class="f8" type="Submit" name="ok" value="éÊÇËâÛÇ∑"></p>
		<input type="hidden" name="a" value="70637">
		<option value="19">19. ëqå… 20</option>
		<option value="29">29. <ãÛ></option>

		POST /build.php HTTP/1.1
		Referer: http://s3.travian.jp/build.php?gid=15
		Content-Type: application/x-www-form-urlencoded
		Content-Length: 64
		gid=15&a=70637&abriss=23&ok=%E5%8F%96%E3%82%8A%E5%A3%8A%E3%81%99
	*/
	function destroy_building($village, $pos_id)
	{
		global $server;
		
		$url = "http://$server/build.php?gid=15";

		echo $url . "\n";

		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		$result = curl_exec ($ch);
		curl_close ($ch);
		
		if(!preg_match('#<input class="f8" type="Submit" name="ok" value="([^"]+)"></p>#', $result, $match)){
			echo "destroy_building: failed to find submit button, or busy.\n";
			return false;
		}
		
		$submit_value = $match[1];
		
		if(!preg_match('#<input type="hidden" name="a" value="([0-9]+)">#', $result, $match)){
			echo "destroy_building: failed to find hidden value `a` \n";
			return false;
		}
		
		$a = $match[1];
		
		if($a != $village){
			echo "destroy_building: village id ($village) dismatches hidden value a ($a), or village switched.\n";
			return false;
		}

		if(!preg_match('#<option value="' . $pos_id . '">' . $pos_id . '\. \D+\d+</option>#', $result)){
			echo "destroy_building: failed to find option value $pos_id, or empty postion. \n";
			return false;
		}
		
		$referer = $url;
		$url = "http://$server/build.php";

		$postfields = 'gid=15&a=' . $a . '&abriss=' . $pos_id . '&ok=' . urlencode($submit_value);

		echo $postfields . "\n";
		
		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_REFERER, $referer);

		$result = curl_exec ($ch);
		curl_close ($ch);

		return true;
	}
?>
