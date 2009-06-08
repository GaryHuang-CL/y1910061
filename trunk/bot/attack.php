<?php

	// Attack by mission
	// Return true if there is mission and no enough troops, and need stop to farm
	// Return false if there is no mission, or some error
	
	function attack($result, $village)
	{
		global $server;
		$url = "http://$server/a2b.php";
		
		$server_hour = get_server_hour($result);
		
		// echo "Current hour : " . $server_hour . "\n";
		
		$start = ($server_hour == 0 ? 23 : $server_hour - 1);
		$stop  = ($server_hour == 23 ? 0 : $server_hour + 1);

		$sql = "select x, y, min_clubs, `recursive`, `seq`, `ram` from mission where village = $village and TO_DAYS(now()) > `recursive` and `hour` in (" . $start . "," . $server_hour . "," . $stop . ") order by `recursive`";
		$res = mysql_query($sql);
		if(!$res) die(mysql_error());

		$row = mysql_fetch_row($res);
		if(!$row){
			// echo "No mission.\n";
			return false;
		}

		$target_x = $row[0];
		$target_y = $row[1];
		$min_clubs = $row[2];
		$recursive = $row[3];
		$seq = $row[4];
		$with_ram = $row[5];
		
		echo "Mission : (" . $row[0] . " , " . $row[1] . ") with " . $min_clubs . " clubs\n";

//		if(being_attacked()){
//			echo "Cancel mission.\n";
//			return false;
//		}
		
		// How many clubs there ?
		// onClick="document.snd.t1.value=1; return false;
		if(!preg_match('/on[cC]lick="document\.snd\.t1\.value=([0-9]+); return false;/', $result, $matches)){
			echo "no club for mission .\n";
			return true;
		}

		$curr_clubs = $matches[1];
		echo $curr_clubs . " clubs.\n";
		
		if($curr_clubs < $min_clubs){
			echo("no enough clubs for mission.\n");
			return true;
		}

		$axes = '';
		$tks = '';
		$hero = '';
		$rams = '';
		
		if($recursive > 0){
			// update the mission
			$sql = "update mission set `recursive` = TO_DAYS(now()) where `seq` = " . $seq;
			if(!mysql_query($sql)) die(mysql_error());
			
			// daily farm
			$clubs = $min_clubs;
			
		}else{
			// uniq mission, need all clubs, axes, tks and hero
			
			// onClick="document.snd.t11.value=1; return false;"
			if(preg_match('/on[cC]lick="document\.snd\.t11\.value=1; return false;/', $result, $matches)){
				$hero = 1;
			}else{
				echo "Hero does NOT in home.\n";
				return true;
			}

			// onClick="document.snd.t3.value=1; return false;
			if(!preg_match('/on[cC]lick="document\.snd\.t3\.value=([0-9]+); return false;/', $result, $matches)){
				$axes = 0;
			}else{
				$axes = $matches[1];
			}

			// onClick="document.snd.t6.value=1; return false;
			if(!preg_match('/on[cC]lick="document\.snd\.t6\.value=([0-9]+); return false;/', $result, $matches)){
				$tks = 0;
			}else{
				$tks = $matches[1];
			}

			// ram
			if($with_ram == 1){
				// onClick="document.snd.t7.value=1; return false;
				if(preg_match('/on[cC]lick="document\.snd\.t7\.value=([0-9]+); return false;/', $result, $matches)){
					$rams = $matches[1];
				}
			}
			
			
			// Delete the mission
			$sql = "delete from mission where `seq` = " . $seq;
			if(!mysql_query($sql)) die(mysql_error());
			
			$clubs = $curr_clubs;
		}

		attack_func($target_x, $target_y, $clubs, $axes, $tks, $rams, '', $hero, $result);

		return true;
	}
	
	function attack_and_farm_loop($village, $incoming_attack_remain_seconds)
	{
		$result = get_a2b_page();

		// not being attacked, we need cumulate clubs for attack
		if($incoming_attack_remain_seconds > 1800 || $incoming_attack_remain_seconds < 0){
			$need_more_clubs = attack($result, $village);
			
			if($need_more_clubs) return;
		}
		
		for($i = 0; $i < 20; $i++){
			if(!farm($result, $village)) return;
			
			// refresh a2b page
			$result = get_a2b_page();
		}
	}
?>