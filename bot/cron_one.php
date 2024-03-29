<?php
	function start_party($account, $village)
	{
		global $server;
		
		$url = "http://$server/build.php?gid=24";
		
		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		$result = curl_exec ($ch);
		curl_close ($ch);

		if(preg_match('#<span id=timer1>([0-9]+):([0-9]+):([0-9]+)</span>#', $result, $match)){
			$hour = $match[1];
			$minute = $match[2];
			$second = $match[3];
			
			$due_time = time() + $hour * 3600 + $minute * 60 + $second;
			$sql = "update villages set party_due_time = $due_time where account = $account and id = $village";
			if(!mysql_query($sql)) die(mysql_error());
			
			echo $sql . "\n";
			echo "Party due time $hour:$minute:$second ...\n";
		}else if(preg_match('#<a href="build.php\?id=([0-9]+)&a=1">[^<]+</a>#', $result, $match)){
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
		global $proxy;
		$sql = "update accounts set busy = 0 where id = $account";
		mysql_query($sql);
		
		if($proxy == 1){
			echo "Shutdown proxy server.\n";
			$cmd = "pkill -u y1910061 python";
			exec($cmd . " > /dev/null &");
		}
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

	$next_check_time = 3600;

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
	
	$sql = "select server, user, password, race, main_village, last_report, beacon, message, busy, redundant_resource, farm_lo, farm_hi, proxy from accounts where id = $account";
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
	$farm_hi = $row[11];
	$proxy = $row[12];
	
	if($proxy == 1){
		echo "Start proxy server.\n";
		$cmd = "./ppp/ppp.py";
		exec($cmd . " > /dev/null &");
		sleep(2);
	}
	
	if($busy == 1){
		echo "BUSY !!!!\n";
		exit();
	}

	$sql = "update accounts set busy = 1 where id = $account";
	mysql_query($sql);


	$sql = "select wood_name, brick_name, iron_name, crop_name, report_str from servers where addr = '$server'";
	$res = mysql_query($sql);
	if(!$res) die(mysql_error());
	$row = mysql_fetch_row($res);
	if(!$row) die("Server not found. $server \n");

	$convert = array($row[0] => 0, $row[1] => 1, $row[2] => 2, $row[3] => 3);
	$report_str = explode(',', $row[4]);
	
	$result = login();
	$hour = get_server_hour($result);
	
	$sql = "select id, auto_transfer, noraid, name, newbie, last_beg, crop, cart_capacity, defence, party, party_due_time, x, y from villages where account = $account order by rand()";
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
		$village_x = $row[11];
		$village_y = $row[12];

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
			if($attack_time_left > 900){
				$next_check_time = min($next_check_time, $attack_time_left - 900);
			}
				
			$oasis_attack_time_left = detect_oasis_attack($result);
			
			update_check_time($result);
			
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

				if($server == "s4.travian.com" && $user == "flock"){
					
					if(max($wood, $brick, $iron) > $warehouse_capacity * 0.9 && min($wood, $brick, $iron) > $warehouse_capacity * 0.5 && $crop > 75000){
						$reserve = round(min($wood / 100, $brick / 130, $iron / 55, $crop / 30) / 2) ;
						build_infantry(1, $reserve);
						
						if($iron > $brick * 2){
							sell(3000, 3, 3000, 2, 8, 9000);
						}
					}

					if($attack_time_left >= 0){
						build_infantry(1, 0);
					}
					

				}else if($server == "s4.travian.com" && $user == "futaba"){
/*
					if($wood <= $brick && $wood <= $iron)
						sell(500, 4, 500, 1, 4, 3000);
					else if($brick <= $wood && $brick <= $iron)
						sell(500, 4, 500, 2, 4, 3000);
					else
						sell(500, 4, 500, 3, 4, 3000);
*/					
					if(max($wood, $brick, $iron) > $warehouse_capacity * 0.9 && min($wood, $brick, $iron) > $warehouse_capacity * 0.5 && $crop > 75000){
						if($wood >= $iron){
							$reserve = round(min($wood / 550, $brick / 440, $iron / 320, $crop / 100) / 2) ;
							build_cavalry(5, $reserve);
						}else{
							$reserve = round(min($wood / 150, $brick / 160, $iron / 210, $crop / 80) / 2) ;
							build_infantry(3, $reserve);
						}
					}

					//if($attack_time_left >=0){
					//	build_infantry(3, 0);
					//}

				}else if($server == "s4.travian.com" && $user == "SeaMonkey"){
					if(max($wood, $brick, $iron) > $warehouse_capacity * 0.9 && min($wood, $brick, $iron) > $warehouse_capacity * 0.5 && $crop > 75000){
						$reserve = round(min($wood / 100, $brick / 130, $iron / 160, $crop / 70)  / 2) ;
						build_infantry(2, $reserve);
					}

					if($attack_time_left >= 0){
						build_infantry(2, 0);
					}
					
					
				}else if($server == "s4.travian.com" && $user == "Kimon"){
					if(max(/*$wood, */ $brick, $iron) > $warehouse_capacity * 0.9 && min($wood, $brick, $iron) > $warehouse_capacity * 0.6 && $crop > 75000){
						if($brick > $iron /*&& $brick > $wood*/){
							$reserve = round(min($wood / 450, $brick / 515, $iron / 480, $crop / 80) / 2) ;
							build_cavalry(6, $reserve);
							
						}else if(/*$iron >= $wood && */ $iron >= $brick){
							$reserve = round(min($wood / 130, $brick / 120, $iron / 170, $crop / 70) / 2) ;
							build_infantry(3, $reserve);
						/*
						}else {
							$reserve = round(min($wood / 95, $brick / 75, $iron / 40, $crop / 40) * 3 / 4) ;
							build_infantry(1, $reserve);
						*/
						}
					}
					
					attack_and_farm_loop($village, $attack_time_left);
					read_self_attack_reports();

					if($attack_time_left >= 0 && $attack_time_left < 800){
						avoid_attack_teutonic(249, 56);
						reinforce(232, 57, array(10=>0));
						build_infantry(3, 0);
					}

				}else if($server == "s4.travian.com" && $user == "ceto"){
					if(max(/*$wood, */ $brick, $iron) > $warehouse_capacity * 0.9 && min($wood, $brick, $iron) > $warehouse_capacity * 0.6 && $crop > 75000 ){
						
						if($brick > $iron /*&& $brick > $wood*/){
							$reserve = round(min($wood / 450, $brick / 515, $iron / 480, $crop / 80) / 2) ;
							build_cavalry(6, $reserve);
							
						}else if(/*$iron >= $wood && */ $iron >= $brick){
							$reserve = round(min($wood / 130, $brick / 120, $iron / 170, $crop / 70) / 2) ;
							build_infantry(3, $reserve);
						/*
						}else if($wood >= $iron && $wood >= $brick){
							$reserve = round(min($wood / 95, $brick / 75, $iron / 40, $crop / 40) / 2) ;
							build_infantry(1, $reserve);
						*/
						}
					}


					// attack_and_farm_loop($village, $attack_time_left);
					if($attack_time_left >=0 && $attack_time_left < 800){
						// avoid_attack_teutonic(184, 47);
						build_infantry(3, 0);
					}
/*
					if($crop > $granary_capacity * 0.9){
						if($wood <= $brick && $wood <= $iron)
							sell(1000, 4, 1000, 1, 10, 0);
						else if($brick <= $wood && $brick <= $iron)
							sell(1000, 4, 1000, 2, 10, 0);
						else
							sell(1000, 4, 1000, 3, 10, 0);
						
					}
*/
					// read_self_attack_reports();

				}
				
			}else {
				
				if($attack_time_left >= 0 && $attack_time_left < 3600 && $defence == 0){
					transfer_to_village($village, $main_village);
				}else{
					transfer_to_village($village, $main_village, false, 75);
				}
			}
			
			// C01
			if($server == "s4.travian.com" && $user == "ceto" && $village == 3681){
				if(max($wood, $brick, $iron) > $warehouse_capacity * 0.95 && min($wood, $brick, $iron) > $warehouse_capacity * 0.2 && $crop > min(3000, $granary_capacity * 0.3)){
					
					if($iron >= $wood){
						$reserve = round(min($wood / 130, $brick / 120, $iron / 170, $crop / 70) * 3 / 4) ;
						build_infantry(3, $reserve);

					}if($wood >= $iron){
						$reserve = round(min($wood / 145, $brick / 70, $iron / 85, $crop / 40) * 3 / 4) ;
						build_infantry(2, $reserve);
					}
				}

				// attack_and_farm_loop($village, $attack_time_left);

				if($attack_time_left >=0 && $attack_time_left < 1000){
					build_infantry(2, 0);
					//avoid_attack_teutonic(52, 10);
					reinforce(49, 11, array(1=>0, 2=>0,3=>0));
				}
				
				reinforce(250, 58, array(2=>0), "15:30:01");
				transfer_to_village($village, 160873);
			
			// C03
			}else if($server == "s4.travian.com" && $user == "ceto" && $village == 143603){
/*
				if(max($wood, $brick, $iron) > $warehouse_capacity * 0.96 && min($wood, $brick, $iron) > $warehouse_capacity * 0.6 && $crop > min(3000, $granary_capacity * 0.3)){
					$reserve = round(min($wood / 95, $brick / 75, $iron / 40, $crop / 40) * 4 / 5) ;
					build_infantry(1, $reserve);
				}

				attack_and_farm_loop($village, $attack_time_left);

				if($attack_time_left >= 0 && $attack_time_left < 1000){
					avoid_attack_teutonic(233, 71);
					build_infantry(1, 0);
				}
				
				if($crop > $granary_capacity * 0.74)
					transfer_to_village($village, 178771);
				else
					transfer_to_village($village, 178771, true);
*/
				//if($hour < 9)
				//	transfer_resource_to_xy($village, 250, 58, 0, 0, 0, 2200, 0);

			// C04
			}else if($server == "s4.travian.com" && $user == "ceto" && $village == 155356){
				
				// transfer_to_village($village, $main_village, true);
				//sell(1000, 4, 1000, rand(1, 3), 4, 0);

			// C05
			}else if($server == "s4.travian.com" && $user == "ceto" && $village == 160873){
				
				// transfer_to_village($village, $main_village, true);
				//sell(1000, 4, 1000, rand(1, 3), 7, 0);
				
			// C06
			}else if($server == "s4.travian.com" && $user == "ceto" && $village == 168817){
				
				if($attack_time_left >= 0 && $attack_time_left < 1000){
					avoid_attack_teutonic(233, 71);
					build_infantry(1, 0);
				}

			}else if($server == "s4.travian.com" && $user == "flock" && $village == 103391){
				//transfer_to_village($village, $main_village);
				//if($attack_time_left >= 0){
					//reinforce(18, 128, array(1=>0));
				//	build_infantry(1, 0);
				//}
				continue;
			}else if($server == "s4.travian.com" && $user == "futaba" && $village == 97907){
				transfer_to_village($village, $main_village);
				if($attack_time_left >= 0){
					build_infantry(2, 0);
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

	echo "$next_check_time ....\n";
	$sql = "update accounts set next_check_time = FROM_UNIXTIME(unix_timestamp(now()) + $next_check_time) where id = $account";
	echo $sql . "\n";
	if(!mysql_query($sql)) die(mysql_error());

	//delete_self_trade_reports();
	//read_ally_reports();
?>
