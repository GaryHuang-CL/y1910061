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
	
	$sql = "select id, auto_transfer, noraid, name, newbie, last_beg from villages where account = $account order by rand()";
	$res = mysql_query($sql);
	if(!$res) die(mysql_error());
	
	while($row = mysql_fetch_row($res)){
		$village = $row[0];
		$auto_transfer = $row[1];
		$noraid = $row[2];
		$name = $row[3];
		$newbie = $row[4];
		$last_beg = $row[5];
		
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
					
					if(max($wood, $brick, $iron) > $warehouse_capacity * 0.9 && min($wood, $brick, $iron) > $warehouse_capacity * 0.5){
						$c1 = round($wood / 100);
						$c2 = round($brick / 130);
						$c3 = round($iron / 55);
						$c4 = round($crop / 30);
						$c = min($c1, $c2, $c3, $c4) - 10;
						
						if($c > 0) build_infantry(1, $c);

					}
					
					if($crop > $granary_capacity * 0.9){
						sell(750, 4, 750, 2, 4, 9000);
					}

				}else if($server == "speed.travian.hk"){
					build_infantry(1, 70);
					attack_and_farm_loop($village, $attack_time_left);

					if($attack_time_left >=0 && $attack_time_left < 375){
						sleep($attack_time_left - 50);
						build_infantry(1, 0);
						attack_and_farm_loop($village, $attack_time_left);
					}

					if($crop > 7000){
						sell(3000, 4, 3000, 1, 0, 5000);
					}

					if($iron > 7000){
						sell(3000, 3, 3000, 1, 0, 5000);
					}

					if($brick > 7000){
						sell(3000, 2, 3000, 1, 0, 5000);
					}

					read_self_attack_reports();

				}else if($server == "s3.travian.jp" && $user == "inblackhole"){
					
					if(max($wood, $brick, $iron) > $warehouse_capacity * 0.9 && min($wood, $brick, $iron) > $warehouse_capacity * 0.5){
						$c1 = round($wood / 100);
						$c2 = round($brick / 130);
						$c3 = round($iron / 55);
						$c4 = round($crop / 30);
						$c = min($c1, $c2, $c3, $c4) - 10;
						
						if($c > 0) build_infantry(1, $c);

					}
					
					if($crop > $granary_capacity * 0.9){
						sell(750, 4, 750, 2, 4, 9000);
					}
					
				}else if($server == "s3.travian.jp" && $user == "papurica731"){
					
					if(max($wood, $brick, $iron) > $warehouse_capacity * 0.9 && min($wood, $brick, $iron) > $warehouse_capacity * 0.5){
						$c1 = round($wood / 100);
						$c2 = round($brick / 130);
						$c3 = round($iron / 160);
						$c4 = round($crop / 70);
						$c = min($c1, $c2, $c3, $c4) - 10;
						
						if($c > 0) build_infantry(2, $c);

					}
					
					if($crop > $granary_capacity * 0.9){
						sell(500, 4, 500, 3, 5, 9000);
					}

				}else if($server == "s3.travian.jp" && $user == "docomo2"){
					
					if(max($wood, $brick, $iron) > $warehouse_capacity * 0.9 && min($wood, $brick, $iron) > $warehouse_capacity * 0.5){
						$c1 = round($wood / 100);
						$c2 = round($brick / 130);
						$c3 = round($iron / 160);
						$c4 = round($crop / 70);
						$c = min($c1, $c2, $c3, $c4) - 10;
						
						if($c > 0) build_infantry(2, $c);

					}
					
					if($crop > $granary_capacity * 0.9){
						sell(500, 4, 500, 3, 5, 9000);
					}

				}else if($server == "s3.travian.jp" && $user == "Kimon"){
					
					if(max($wood, $brick, $iron) > $warehouse_capacity * 0.9 && min($wood, $brick, $iron) > $warehouse_capacity * 0.5){
						$c1 = round($wood / 100);
						$c2 = round($brick / 130);
						$c3 = round($iron / 160);
						$c4 = round($crop / 70);
						$c = min($c1, $c2, $c3, $c4) - 10;
						
						if($c > 0) build_infantry(2, $c);

					}
					
					if($crop > $granary_capacity * 0.9){
						sell(500, 4, 500, 3, 5, 9000);
					}
					
				}else if($server == "s3.travian.jp" && $user == "Hömeless"){

					if($wood > $warehouse_capacity * 0.9){
						$c1 = round($wood / 95);
						$c2 = round($brick / 75);
						$c3 = round($iron / 40);
						$c4 = round($crop / 40);
						$c = min($c1, $c2, $c3, $c4) - 10;
						build_infantry(1, $c);
					}

					if($brick > $warehouse_capacity * 0.9){
						$c1 = round($wood / 450);
						$c2 = round($brick / 515);
						$c3 = round($iron / 480);
						$c4 = round($crop / 80);
						$c = min($c1, $c2, $c3, $c4) - 2;
						build_cavalry(6, $c);
					}
					
					if($iron > $warehouse_capacity * 0.9){
						$c1 = round($wood / 130);
						$c2 = round($brick / 120);
						$c3 = round($iron / 170);
						$c4 = round($crop / 70);
						$c = min($c1, $c2, $c3, $c4) - 8;
						build_infantry(3, $c);
					}

					if($crop > $granary_capacity * 0.9){
						sell(1000, 4, 1000, rand(1, 2), 4, 1000);
					}

					//attack_and_farm_loop($village, $attack_time_left);

					//if($attack_time_left >=0 && $attack_time_left < 420){
					//	avoid_attack_teutonic(94, 29);
					//}

					//read_self_attack_reports();

				}

			// raid village
			}else if($server == "s3.travian.jp" && $user == "Hömeless" && $noraid == 0){
				/*
				if($attack_time_left >=0 && $attack_time_left < 360){
					echo "URGENT 2 !!!! $attack_time_left\n";
					sleep($attack_time_left - 50);
					build_infantry(1, 0);
					reinforce(94, 28, array(1 => 0));
				}
				attack_and_farm_loop($village, $attack_time_left);
				*/
				transfer_to_village($village, $main_village);
			

			// new village logic
			}else if($newbie > 0){
				
				if(time() - $last_beg > 2000)
					transfer_to_newbie($main_village, $village, $result);
				
				/*
				if($attack_time_left < 0){
					if(time() - $last_beg > 2000)
						transfer_to_newbie($main_village, $village, $result);
					
				}else if($attack_time_left < 3600){
					transfer_to_village($village, $main_village);
				}*/
			
			// villages in building
			}else {
				
				if($attack_time_left >= 0 && $attack_time_left < 3600){
					transfer_to_village($village, $main_village);
				}else{
					transfer_to_village($village, $main_village, false, 75);
				}
				
			}
		}
	}
	
	delete_self_trade_reports();

?>
