<?php
	define("SAWMILL",      		5);
	define("BRICKYARD",    		6);
	define("IRON_FOUNDRY", 		7);
	define("GRAIN_MILL", 		8);
	define("BAKERY", 			9);
	define("WAREHOUSE",      	10);
	define("GRANARY",        	11);
	define("MAIN_BUILDING", 	15);
	define("MARKETPLACE",	 	17);
	define("RESIDENCE", 		25);

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
		$ret = preg_match('/href="(dorf2\.php\?a=[0-9]+&amp;c=[0-9a-z]+)">/', $result, $matches);
		
		if(!$ret){
			echo "build gid $gid failed.\n";
			return false;
		}

		$url2 = "http://$server/" . html_entity_decode($matches[1]);
		echo $url2 . "\n";
		
		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL, $url2);
		curl_setopt($ch, CURLOPT_REFERER, $url);
		$result = curl_exec ($ch);
		curl_close ($ch);
		
		update_check_time($result);
		return $result;
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

		// <td class="dot hl">&#x25CF;</td>
		// <td class="link"><a href="?newdid=226563">
		$ret = preg_match('/<td class="dot hl">&#x25CF;<\/td>\s*<td class="link"><a href="\?newdid=([0-9]+)/', $result, $matches);
		
		if(!$ret) die("Failed to switch village.");
		if($matches[1] != $village) die("Failed to switch village " . $matches[1] . "\n");
		
		return $result;

	}
	
	// $result is dorf1 page
	function get_resource_fields_level($result)
	{
		global $convert;
		
		$out = array();
		// <area href="build.php?id=1" coords="101,33,28" shape="circle" title="きこり レベル 4">
		// 				<area href="build.php?id=1"
		// coords="101,33,28" shape="circle"
		// title="Holzf&auml;ller Stufe 0" alt="" />
		// <area href="build.php?id=4" coords="46,63,28" shape="circle" title="Iron Mine Level 0"/>
		if(preg_match_all('#<area href="build.php\?id=([0-9]+)"\s+coords="[0-9]+,[0-9]+,[0-9]+" shape="circle"\s+title="([^0-9"]+?) ([0-9]+)"#', $result, $matches, PREG_SET_ORDER)){
			foreach($matches as $match){
				$tmp = explode(' ', $match[2]);
				array_pop ($tmp);
				$name = implode(' ', $tmp);
				
				$type = $convert[$name];
				$id = $match[1];
				$level = $match[3];
				
				$out[$type][$id] = $level;
			}
		}else{
			die("Can not get resource fields level.");
		}
		
		return $out;
	}

	function get_lowest_resource($result, $type)
	{
		$ret = get_resource_fields_level($result);
		
		$levels = $ret[$type];
		
		asort($levels);
			
		list($id, $level) = each($levels);
		
		return array($id, $level);
	}
	
	function get_dorf2_page()
	{
		global $server;
		$url = "http://$server/dorf2.php";

		echo $url . "\n";

		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		$result = curl_exec ($ch);
		curl_close ($ch);

		if(!strstr($result, 'id="map2"')){
			echo "get_dorf2_page failed: can not find map2.\n";
			return false;
		}
		
		return $result;
	}
	
	// main_building, warehouse, granary
	function get_dorf2_building_level($result)
	{
		$ret = array();
		
		// <img src="img/x.gif" class="building d1 g10b" alt="倉庫 レベル 0" />
		if(preg_match('#<img src="img/x.gif" class="building d[0-9]+ g10b?" alt="[^0-9]+([0-9]+)" />#', $result, $match)){
			$ret['warehouse'] = $match[1];
		}	
		
		if(preg_match('#<img src="img/x.gif" class="building d[0-9]+ g11b?" alt="[^0-9]+([0-9]+)" />#', $result, $match)){
			$ret['granary'] = $match[1];
		}	

		if(preg_match('#<img src="img/x.gif" class="building d[0-9]+ g15b?" alt="[^0-9]+([0-9]+)" />#', $result, $match)){
			$ret['main_building'] = $match[1];
		}	
		
		return $ret;
	}

	function get_building_level($result, $gid)
	{
		if(preg_match('#<img src="img/x.gif" class="building d[0-9]+ g' . $gid . 'b?" alt="[^0-9]+([0-9]+)" />#', $result, $match)){
			return $match[1];
		}
		
		return false;
	}
	
	function get_free_space($result)
	{
		// <img src="img/x.gif" class="building d4 iso" alt="建築用地" />
		if(preg_match('#<img src="img/x.gif" class="building d([0-9]+) iso" alt="#', $result, $match)){
			return $match[1];
		}
		
		echo "no free space.\n";
		
		return false;
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
			$min_capacity = array(800, 800, 1200, 1200, 1700, 5000, 6300, 7800, 9600, 14000);
			
			if($min_capacity[$level] > $granary_capacity){
				if($granary_capacity > 800)
					build_granary();
				else
					create_granary();
				return false;
			}
			
			if($min_capacity[$level] > $warehouse_capacity){
				if($warehouse_capacity == 800){
					create_warehouse();
					return false;
				}
				
				if($dorf2_html = build_warehouse()){

					if(!strstr($dorf2_html, 'id="map2"')){
						echo "get_auto_build_resource_field_id failed: can not find map2.\n";
						return false;
					}
					
					$dorf2_buildings = get_dorf2_building_level($dorf2_html);
					
					// no main building, try to build one
					if(!isset($dorf2_buildings['main_building'])){
						$id = get_free_space($dorf2_html);
						
						if(!$id) return false;
						
						$id = $id + 18;

						$sql = "insert into `build`(account, village, id, gid) values($account, $village, $id, 15)";
						if(!mysql_query($sql)) die(mysql_error());
					}else if($dorf2_buildings['main_building'] < $level + 3){
						$sql = "insert into `build`(account, village, id) values($account, $village, 26)";
						if(!mysql_query($sql)) die(mysql_error());
					}
					return false;
				}
			}
			
			return $id;
		}
		
		echo "all resource fields level 10.\n";
		build_when_all_resource_done();
		
		return false;
		
	}

	function build($village, $result, $newbie = 0)
	{
		global $server;
		global $account;
		global $race;

		// ^<div class="f10 b">.+<span id=timer1>
		// <span id="timer1">1:51:21</span>
		if(preg_match('/<td><span id="timer1">[0-9]+:[0-9]+:[0-9]+<\/span>/', $result, $matches)){
			if($race == "roman"){
				if(preg_match('/<td><span id="timer2">[0-9]+:[0-9]+:[0-9]+<\/span>/', $result, $matches)){
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

			list(,,,$crop_production) = get_production($result);
			
			if($crop_production < 5){
				echo "Low crop...\n";
				build_on_low_crop($result);
				return;
			}
			
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
		$ret = preg_match('/href="(dorf[12]\.php\?a=[0-9]+&amp;c=[0-9a-z]+)">/', $result, $matches);
		
		if(!$ret){
			// relay point
			// dorf2.php?a=16&id=39&c=2e0
			if($id == 39) $gid = 16;

			// wall
			// dorf2.php?a=31&id=40&c=061
			if($id == 40) {
				$ret = preg_match('/href="(dorf2\.php\?a=[0-9]+&amp;id=' . $id . '&amp;c=[0-9a-z]+)">/', $result, $matches);
			}else if($gid > 0){	
				// <a href="dorf2.php?a=23&id=33&c=0c6">
				$ret = preg_match('/href="(dorf[12]\.php\?a=' . $gid . '&amp;id=' . $id . '&amp;c=[0-9a-z]+)">/', $result, $matches);
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
				$ret = preg_match('/href="(dorf[12]\.php\?a=[0-9]+&amp;c=[0-9a-z]+)">/', $result, $matches);

				if(!$ret) return;
				echo "roman auto building..\n";
			}else{
				return;
			}
		}
		
		$url2 = "http://$server/" . html_entity_decode($matches[1]);
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
		
		update_check_time($result);
		//echo $result;
	}
	
	// destroy a building by position id of current village
	/*
		http://s3.travian.jp/build.php?gid=15
		<input class="f8" type="Submit" name="ok" value="取り壊す"></p>
		<input type="hidden" name="a" value="70637">
		<option value="19">19. 倉庫 20</option>
		<option value="29">29. <空></option>

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
	
	function create_building($gid, $result)
	{
		if(!$result){
			$result = get_dorf2_page();
			if(!$result) return false;
		}
		
		$id = get_free_space($result);
		
		if(!$id) return false;
		
		$id = $id + 18;
		
		return build_or_upgrade($id, $gid);
	}
	
	function create_main_building($result = '')
	{
		return create_building(15, $result);
	}
	
	function create_warehouse($result = '')
	{
		return create_building(10, $result);
	}

	function create_granary($result = '')
	{
		return create_building(11, $result);
	}
	
	function build_or_upgrade($id, $gid = 0)
	{
		global $server;

		$referer = "http://$server/dorf" . ( $id <= 18 ? "1" : "2" ) . ".php";
		$url = "http://$server/build.php?id=$id"; 

		echo $url . "\n";

		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_REFERER, $referer);
		curl_setopt($ch, CURLOPT_URL,$url);
		$result = curl_exec ($ch);
		curl_close ($ch);

		// <a href="dorf2.php?a=28&c=23d">
		$ret = preg_match('/href="(dorf[12]\.php\?a=[0-9]+&amp;c=[0-9a-z]+)">/', $result, $matches);
		
		if(!$ret){
			// relay point
			// dorf2.php?a=16&id=39&c=2e0
			if($id == 39) $gid = 16;

			// wall
			// dorf2.php?a=31&id=40&c=061
			if($id == 40) {
				$ret = preg_match('/href="(dorf2\.php\?a=[0-9]+&amp;id=' . $id . '&amp;c=[0-9a-z]+)">/', $result, $matches);
			}else if($gid > 0){	
				// <a href="dorf2.php?a=23&id=33&c=0c6">
				$ret = preg_match('/href="(dorf[12]\.php\?a=' . $gid . '&amp;id=' . $id . '&amp;c=[0-9a-z]+)">/', $result, $matches);
			}
		}

		if(!$ret){
			echo "Busy or lacking resources or failed .. $gid $id\n";
			return false;
		}

		$url2 = "http://$server/" . html_entity_decode($matches[1]);
		echo $url2 . "\n";
		
		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL, $url2);
		curl_setopt($ch, CURLOPT_REFERER, $url);
		$result = curl_exec ($ch);
		curl_close ($ch);
		update_check_time($result);
		return true;
	}
	
	function build_on_low_crop($dorf1_html)
	{
		$result = get_dorf2_page();
		if(!$result) return;
		
		$dorf2_buildings = get_dorf2_building_level($result);
		
		// no main building, try to build one
		if(!isset($dorf2_buildings['main_building'])){
			if(create_main_building($result)) return;
		}
		
		// all crops level >= 5 but no warehouse, try to build one
		list($id, $level) = get_lowest_resource($dorf1_html, 3);
		
		if($level >= 5 && !isset($dorf2_buildings['warehouse'])){
			create_warehouse($result);
			return;
		}
		
		// all crops level > 6 and warehouse level < 2, upgrade
		if($level >= 6 && $dorf2_buildings['warehouse'] < 2){
			build_warehouse();
			return;
		}
		
		if($level >= 7){
			if($dorf2_buildings['warehouse'] < 5){
				build_warehouse();
				return;
			}
			
			if(!isset($dorf2_buildings['granary'])){
				create_granary();
				return;
			}

			if($dorf2_buildings['granary'] < 2){
				build_granary();
				return;
			}
		}
		
		build_or_upgrade($id);
	}
	
	function get_production($result)
	{
		$ret = array();
		// <td id="l4" title="5">1200/1200</td>
		// <td id="l1" title="-1162">1133/1200</td>
		if(preg_match_all('#<td id="l[0-9]" title="([0-9-]+)">[0-9-]+/[0-9]+</td>#', $result, $matches, PREG_SET_ORDER)){
			foreach($matches as $match){
				array_push($ret, $match[1]);
			}
			
			if(count($ret) != 4) die("Bad count get production.\n");
			
			return $ret;
		}
		
		echo $result;
		die("failed to get production.\n");

	}
	
	function update_check_time($result)
	{
		global $next_check_time;
		
		// <span id="timer1">1:51:21</span>
		$ret = preg_match_all('#<span id="timer[0-9]">([0-9]+):([0-9]+):([0-9]+)</span>#', $result, $matches, PREG_SET_ORDER);
		
		if(!$ret) return;
		
		foreach($matches as $match){
			$hour = $match[1];
			$minute = $match[2];
			$second = $match[3];
			echo $match[0] . "\n";
				$next_check_time = min($next_check_time, $hour * 3600 + $minute * 60 + $second);
		}
	}
	
	function create_or_upgrade($result, $gid, $max_level = 10)
	{
		$level = get_building_level($result, $gid);
		
		if($level === false){
			create_building($gid, $result);
			return true;

		}else if($level < $max_level){
			build_gid($gid);
			return true;
		}
		
		return false;
	}
	
	// build when all resource level 10
	function build_when_all_resource_done()
	{
		global $village, $main_village, $newbie;
		
		if($village == $main_village) return;
		
		if(!$newbie) return;
		
		$result = get_dorf2_page();
		if(!$result) return;
		
		if(create_or_upgrade($result, MAIN_BUILDING)){
			echo "build MAIN_BUILDING.\n";
			return;
		}

		// RESIDENCE to level 10
		if(create_or_upgrade($result, RESIDENCE)){
			echo "build RESIDENCE.\n";
			return;
		}
		
		// GRANARY to level 12
		if(create_or_upgrade($result, GRANARY, 12)){
			echo "build GRANARY level 12.\n";
			return;
		}

		$targets = array(MARKETPLACE, SAWMILL, BRICKYARD, IRON_FOUNDRY, GRAIN_MILL);
		$levels= array();
		
		foreach($targets as $target){
			$level = get_building_level($result, $target);
			
			if(!$level){
				echo "Create $target\n ..\n";
				create_building($target, $result);
				return;
			}
			
			$levels[$target] = $level;
		}
		
		asort($levels);
		
		print_r($levels);
		
		list($target, $level) = each($levels);
		
		if($level < 5){
			build_gid($target);
			return;
		}
		
		// WAREHOUSE to level 14
		if(create_or_upgrade($result, WAREHOUSE, 14)){
			echo "build WAREHOUSE.\n";
			return;
		}

		// GRANARY to level 14
		if(create_or_upgrade($result, GRANARY, 14)){
			echo "build GRANARY.\n";
			return;
		}

		// BAKERY to level 5
		if(create_or_upgrade($result, BAKERY, 5)){
			echo "build BAKERY.\n";
			return;
		}
		// MARKETPLACE to level 15
		if(create_or_upgrade($result, MARKETPLACE, 15)){
			echo "build MARKETPLACE.\n";
			return;
		}

		echo "Nothing to build..\n";
	}
?>
