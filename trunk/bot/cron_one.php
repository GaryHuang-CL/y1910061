<?php
	function start_party($account, $village)
	{
		global $server;
		
		$url = "http://$server/build.php?gid=24";
		
		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		$result = curl_exec ($ch);
		curl_close ($ch);

		if(preg_match('#<td width="25%"><span id=timer1>([0-9]+):([0-9]+):([0-9]+)</span></td>#', $result, $match)){
			$hour = $match[1];
			$minute = $match[2];
			$second = $match[3];
			
			$due_time = time() + $hour * 3600 + $minute * 60 + $second;
			$sql = "update villages set party_due_time = $due_time where account = $account and id = $village";
			if(!mysql_query($sql)) die(mysql_error());
			
			echo $sql . "\n";
			echo "Party due time $hour:$minute:$second ...\n";
		}else if(preg_match('#<td width="28%"><a href="build.php\?id=([0-9]+)&a=1">[^<]+</a></td>#', $result, $match)){
			$id = $match[1];
			
			$referer = $url;
			$url = "http://$server/build.php?id=$id&a=1";
			$ch = my_curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			$result = curl_exec ($ch);
			curl_close ($ch);
			
			echo "Party ...\n";
		}

	}
	
	function shutdown($account)
	{
		$sql = "update accounts set busy = 0 where id = $account";
		mysql_query($sql);
	}
	
	function build_task_exist($account, $village)
	{
		$sql = "select count(*) from build where account = $account and village = $village";
		$res = mysql_query($sql);
		if(!$res) die(mysql_error());
		$row = mysql_fetch_row($res);
		
		return $row[0] > 0;

	}
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

	if(array_key_exists('a', $_GET)){
		$account = intval($_GET['a']);
	}else{
		if($argc < 2) die("Parameters");
		$account = $argv[1];
	}
	
	register_shutdown_function("shutdown", $account);

	// ----------------------------------------------------------------------------
	// Functions
	// ----------------------------------------------------------------------------
	require_once('utils.php');
	require_once('farm.php');
	require_once('attack.php');
	require_once('build.php');
	require_once('login.php');
	require_once('db.php');
	require_once('transfer.php');
	require_once('army.php');
	require_once('read-reports.php');
	
	$sql = "select server, user, password, race, main_village, last_report, beacon, message, busy, redundant_resource, farm_lo, farm_hi from accounts where id = $account";
	$res = mysql_query($sql);
	if(!$res) die(mysql_error());
	$row = mysql_fetch_row($res);
	if(!$row) die("Account not found. $account \n");
	
	$server       = $row[0];
	$user         = $row[1];
	$password     = $row[2];
	$race         = $row[3];
	$main_village = $row[4];
	$last_report  = $row[5];
	$beacon       = $row[6];
	$message      = $row[7];
	$busy         = $row[8];
	$redundant_resource = $row[9];
	$farm_lo = $row[10];
	$farm_hi = $row[11];
	
	if($busy == 1){
		echo "BUSY !!!!\n";
		exit();
	}

	$sql = "update accounts set busy = 1 where id = $account";
	mysql_query($sql);


	$sql = "select wood_name, brick_name, iron_name, crop_name from servers where addr = '$server'";
	$res = mysql_query($sql);
	if(!$res) die(mysql_error());
	$row = mysql_fetch_row($res);
	if(!$row) die("Server not found. $server \n");

	$convert = array($row[0] => 0, $row[1] => 1, $row[2] => 2, $row[3] => 3);
	
	$result = login();
	$hour = get_server_hour($result);
	
	$sql = "select id, auto_transfer, noraid, name, newbie, last_beg, crop, cart_capacity, defence, party, party_due_time from villages where account = $account order by rand()";
	$res = mysql_query($sql);
	if(!$res) die(mysql_error());
	
	while($row = mysql_fetch_row($res)){
		$village = $row[0];
		$auto_transfer = $row[1];
		$noraid = $row[2];
		$name = $row[3];
		$newbie = $row[4];
		$last_beg = $row[5];
		$buycrop = $row[6];
		$cart_capacity = $row[7];
		$defence = $row[8];
		$party = $row[9];
		$party_due_time = $row[10];

		if($cart_capacity == 0){
			if($race == "teuton") $cart_capacity = 1000;
			else if($race == "gaolic") $cart_capacity = 750;
			else $cart_capacity = 500;
		}
		
		echo "\n +++++++ $name +++++++\n";

		if($auto_transfer > 0){

			if(build_task_exist($account, $village)){
				$result = switch_village($village);
				build($village, $result, $newbie);
			}

			transfer_to_village($village, $auto_transfer);

		}else{
			if($village > 0)
				$result = switch_village($village);

			$attack_time_left = detect_attack($result);
			$oasis_attack_time_left = detect_oasis_attack($result);
			
			build($village, $result, $newbie);

			list($wood, $brick, $iron, $crop, $warehouse_capacity, , , $granary_capacity) = get_res_info($result);
			
			if($village == $main_village){

				detect_message($result, $account, $message, $user, $server);
				
				if($attack_time_left >= 0 && $beacon == 0){
					$sql = "update accounts set beacon = 1 where id = $account";
					if(!mysql_query($sql)) die(mysql_error());
					
					$title = "$server,$user,$attack_time_left";
					attack_mail($title);
					
				}else if($attack_time_left < 0 && $beacon == 1){
					$sql = "update accounts set beacon = 0 where id = $account";
					if(!mysql_query($sql)) die(mysql_error());
				}

				if($server == "speed.travian.tw" && $user == "3x3x3"){
					build_infantry(2, 7);
					reinforce(46, 101, array(2=>0));
					
					if($crop > 5000){
						sell(1500, 4, 1500, 3, 0, 3000);
					}
					
					if($brick > 5000){
						sell(1500, 2, 1500, 3, 0, 3000);
					}

					if($wood > 5000){
						sell(1500, 1, 1500, 3, 0, 3000);
					}
					
					
				}else if($server == "speed.travian.tw" && $user == "Kimon"){
					
					if($iron > $warehouse_capacity * 0.9 && $crop > 8000){
						$c1 = round($wood / 130);
						$c2 = round($brick / 120);
						$c3 = round($iron / 170);
						$c4 = round($crop / 70);
						$c = round(min($c1, $c2, $c3, $c4) * 9 / 10);
						build_infantry(3, $c);

					}else if($brick > $warehouse_capacity * 0.9 && $crop > 8000){
						$c1 = round($wood / 450);
						$c2 = round($brick / 515);
						$c3 = round($iron / 480);
						$c4 = round($crop / 80);
						$c = round(min($c1, $c2, $c3, $c4) * 9 / 10);
						build_cavalry(6, $c);

					}else if($wood > $warehouse_capacity * 0.9 && $crop > 8000){
						$c1 = round($wood / 95);
						$c2 = round($brick / 75);
						$c3 = round($iron / 40);
						$c4 = round($crop / 40);
						$c = round(min($c1, $c2, $c3, $c4) * 9 / 10);

						build_infantry(1, $c);
					}

					if($crop > $granary_capacity * 0.95){
						sell(3000, 4, 3000, 1, 0, 2000);
					}

					attack_and_farm_loop($village, $attack_time_left);
					read_self_attack_reports();

					if($attack_time_left >=0 && $attack_time_left < 1000){
						build_infantry(3, 0);
						avoid_attack_teutonic(49, 104);
					}

				}
			}else {
				
				if($attack_time_left >= 0 && $attack_time_left < 3600 && $defence == 0){
					transfer_to_village($village, $main_village);
				}else{
					transfer_to_village($village, $main_village, false, 75);
				}
			}

			// new village logic
			if($newbie > 0){
				
				if($attack_time_left < 0 || $attack_time_left > 3600){
					if(time() - $last_beg > 2000)
						transfer_to_newbie($main_village, $village, $result);
					
				}else if($attack_time_left < 3600 && $defence == 0){
					transfer_to_village($village, $main_village);
				}
			
			// sell resources for crops
			}
			
			if($buycrop > 0){
				sell_for_crop(1, 0, 4);
				
				if($village != $main_village)
					transfer_crop_to_village($village, $main_village, 5000);
			
			// villages in building
			}
			
			if($party > 0 && $party_due_time < time()){
				start_party($account, $village);
			}
		}
	}
	
	//delete_self_trade_reports();
	//read_ally_reports();
?>
