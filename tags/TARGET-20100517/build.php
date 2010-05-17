<?php
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
		// <area href="build.php?id=1" coords="101,33,28" shape="circle" title="‚«‚±‚è ƒŒƒxƒ‹ 4">
		if(preg_match_all('#<area href="build.php\?id=([0-9]+)" coords="[0-9]+,[0-9]+,[0-9]+" shape="circle" title="(\S+) \S+ ([0-9]+)">#', $result, $matches, PREG_SET_ORDER)){
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
		$level_all = get_resource_fields_level($result);
		
		$res = get_res_info($result);
		unset($res[4], $res[5], $res[6], $res[7]);
		asort($res);
		
		// print_r($res);
		
		foreach($res as $type => $num){
			$level_type = $level_all[$type];
			
			asort($level_type);
			//print_r($level_type);
			
			list($id, $level) = each($level_type);
			
			if($level >= 10) continue;
			
			return $id;
		}
		
		echo "all resource fields level 10.\n";
		
		return false;
		
	}

	function build($village, $result)
	{
		global $server;
		
		// Get a build task
		$sql = "select `seq`, `id` from `build` where `village` = $village order by `seq` limit 1";
		
		$res = mysql_query($sql);
		if(!$res) die(mysql_error());

		$row = mysql_fetch_row($res);
		if(!$row){
			echo "No build task more. Try to auto build.\n";
			
			$id = get_auto_build_resource_field_id($result);
			$seq = -1;
			
		}else{
			$seq = $row[0];
			$id = $row[1];
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
			echo "Busy or lacking resources or failed.\n";
			return;
		}
		
		$url2 = "http://$server/" . $matches[1];
		echo $url2 . "\n";
		
		if($seq >= 0){
			// Update the target
			$sql = "delete from `build` where `seq` = $seq";
			if(!mysql_query($sql)) die(mysql_error());
		}
		
		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL, $url2);
		curl_setopt($ch, CURLOPT_REFERER, $url);
		$result = curl_exec ($ch);
		curl_close ($ch);
		
		//echo $result;
	}
?>