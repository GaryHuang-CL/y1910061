<?php
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
	
	$sql = "select server, user, password, race, main_village, last_report, beacon, message, busy from accounts where id = $account";
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
	
	$sql = "select id, auto_transfer, noraid, name, newbie, last_beg, crop, cart_capacity, defence from villages where account = $account order by rand()";
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

				if($server == "s3.travian.jp" && $user == "3x3x3"){
					
					if(max($wood, $brick, $iron) > $warehouse_capacity * 0.8 && min($wood, $brick, $iron) > $warehouse_capacity * 0.2){
						$c1 = round($wood / 100);
						$c2 = round($brick / 130);
						$c3 = round($iron / 55);
						$c4 = round($crop / 30);
						$c = round(min($c1, $c2, $c3, $c4) * 9 / 10);
						
						if($c > 0) build_infantry(1, $c);

					}
					
					if($crop > $granary_capacity * 0.9){
						sell(750, 4, 750, 2, 4, 9000);
					}


				}else if($server == "s3.travian.jp" && $user == "inblackhole"){
					
					if(max($wood, $brick, $iron) > $warehouse_capacity * 0.8 && min($wood, $brick, $iron) > $warehouse_capacity * 0.2){
						$c1 = round($wood / 100);
						$c2 = round($brick / 130);
						$c3 = round($iron / 55);
						$c4 = round($crop / 30);
						$c = round(min($c1, $c2, $c3, $c4) * 9 / 10);
						
						if($c > 0) build_infantry(1, $c);

					}
					
					if($crop > $granary_capacity * 0.9){
						sell(750, 4, 750, 2, 4, 9000);
					}
					
				}else if($server == "s3.travian.jp" && $user == "papurica731"){
					
					if(max($wood, $brick, $iron) > $warehouse_capacity * 0.8 && min($wood, $brick, $iron) > $warehouse_capacity * 0.2){
						$c1 = round($wood / 100);
						$c2 = round($brick / 130);
						$c3 = round($iron / 160);
						$c4 = round($crop / 70);
						$c = round(min($c1, $c2, $c3, $c4) * 9 / 10);
						
						if($c > 0) build_infantry(2, $c);

					}
					
					if($crop > $granary_capacity * 0.9){
						sell(500, 4, 500, 3, 5, 9000);
					}

					// トマト@k9999	(89|19)
					//if($hour > 2 && $hour < 10){
					//	transfer_resouce_to_xy($village, 89, 19, 0, 0, 0, 1000, 0);
					//}

				}else if($server == "s3.travian.jp" && $user == "docomo2"){
					
					if(max($wood, $brick, $iron) > $warehouse_capacity * 0.8 && min($wood, $brick, $iron) > $warehouse_capacity * 0.2){
						$c1 = round($wood / 100);
						$c2 = round($brick / 130);
						$c3 = round($iron / 160);
						$c4 = round($crop / 70);
						$c = round(min($c1, $c2, $c3, $c4) * 9 / 10);
						
						if($c > 0) build_infantry(2, $c);

					}
					
					if($crop > $granary_capacity * 0.9){
						sell(500, 4, 500, 2, 5, 9000);
					}

				}else if($server == "s3.travian.jp" && $user == "Kimon"){
					
					if(max($wood, $brick, $iron) > $warehouse_capacity * 0.8 && min($wood, $brick, $iron) > $warehouse_capacity * 0.2){
						$c1 = round($wood / 100);
						$c2 = round($brick / 130);
						$c3 = round($iron / 160);
						$c4 = round($crop / 70);
						$c = round(min($c1, $c2, $c3, $c4) * 9 / 10);
						
						if($c > 0) build_infantry(2, $c);

					}
					
					if($crop > $granary_capacity * 0.9){
						sell(500, 4, 500, 2, 8, 9000);
					}
					
				}else if($server == "s3.travian.jp" && $user == "Hömeless"){

					if($wood > $warehouse_capacity * 0.8){
						$c1 = round($wood / 95);
						$c2 = round($brick / 75);
						$c3 = round($iron / 40);
						$c4 = round($crop / 40);
						$c = round(min($c1, $c2, $c3, $c4) * 9 / 10);

						build_infantry(1, $c);
					}

					if($brick > $warehouse_capacity * 0.8){
						$c1 = round($wood / 450);
						$c2 = round($brick / 515);
						$c3 = round($iron / 480);
						$c4 = round($crop / 80);
						$c = round(min($c1, $c2, $c3, $c4) * 9 / 10);
						build_cavalry(6, $c);
					}
					
					if($iron > $warehouse_capacity * 0.8){
						$c1 = round($wood / 130);
						$c2 = round($brick / 120);
						$c3 = round($iron / 170);
						$c4 = round($crop / 70);
						$c = round(min($c1, $c2, $c3, $c4) * 9 / 10);
						build_infantry(3, $c);
					}

					if($crop > $granary_capacity * 0.9){
						sell(1000, 4, 1000, rand(1, 2), 4, 1000);
					}

					attack_and_farm_loop($village, $attack_time_left);

					if($attack_time_left >=0 && $attack_time_left < 420){
						avoid_attack_teutonic(95, 29);
					}

					read_self_attack_reports();
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
				
				if($attack_time_left < 0){
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
		}
	}
	
	delete_self_trade_reports();
	read_ally_reports();
?>
