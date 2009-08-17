<?php
	require_once("attack_func.php");

	function calc_score($score_str)
	{
		echo "Scores:  $score_str\n";
		
		if(empty($score_str)){
			return 100;
		}
		
		$scores = explode('|', $score_str);

		if(empty($scores)){
			return 100;

		}else{
			
			$score_total = 0;
			$c = 0;
			
			foreach($scores as $s){
				if(!empty($s)){
					$c++;
					$score_total += intval($s);
				}
			}
			
			if($c > 0){
				return round($score_total / $c);
			}else{
				return 100;
			}
		}
	}
	
	
	// Target : villages without playing
	// Return true if there are clubs remain
	
	function farm($result, $village)
	{
		global $server;
		global $account;
		global $farm_lo, $farm_hi;
		global $village_x, $village_y;
		
		$url = "http://$server/a2b.php";

		$max_clubs = $farm_hi;
		$min_clubs = $farm_lo;

		$troops = get_troops($result);

		$curr_clubs = 0;
		$curr_axes = 0;
		$curr_tks = 0;
		
		if(isset($troops[1]))
			$curr_clubs = $troops[1];

		if(isset($troops[3]))
			$curr_axes = $troops[3];

		if(isset($troops[6]))
			$curr_tks = $troops[6];

		if($curr_clubs < $min_clubs && $curr_axes < $min_clubs && $curr_tks < $min_clubs)
			return false;

		echo "$curr_clubs clubs, $curr_axes axes, $curr_tks tks availabe.\n";
		
		// Get a target
		$sql = "SELECT x, y, `raid`, `score`, `player` FROM `targets` WHERE account = $account and `village` = $village and `invalid` = 0 and NOW() > `timestamp` order by (sqrt((x - $village_x) * (x - $village_x) + (y - $village_y) * (y - $village_y))) / IF(avg_score=0, 1, avg_score) limit 1";
		echo $sql . "\n";
		$res = mysql_query($sql);
		if(!$res) die(mysql_error());

		$row = mysql_fetch_row($res);
		if(!$row){
			echo "Warning: too many clubs. $village \n";
			return false;
		}

		$target_x = $row[0];
		$target_y = $row[1];
		$raid     = $row[2];
		$score_str= $row[3];
		$player   = $row[4];
		
		$score = calc_score($score_str);
		
		echo "Average score:  $score \n";
		
		$old_min_clubs = $min_clubs;

		if($raid > 1){
			$min_clubs = $min_clubs * $raid;
			$max_clubs = $max_clubs * $raid;
		}

		echo "Want to raid : (" . $row[0] . " , " . $row[1] . ")\n";

		if($curr_clubs < $min_clubs && $curr_axes < $min_clubs && $curr_tks < $min_clubs){
			echo("no enough troops. $min_clubs \n");
			return false;
		}

		$clubs = '';
		$axes = '';
		$tks = '';
		
		if($curr_clubs >= $min_clubs){
			if($curr_clubs <= $max_clubs){
				$clubs = $curr_clubs;
			}else{
				$clubs = $min_clubs + round(($max_clubs - $min_clubs) * $score * $score / 10000);
				
				if($curr_clubs - $clubs < $old_min_clubs)
					$clubs = $curr_clubs;
			}
		}else if($curr_axes >= $min_clubs){
			if($curr_axes <= $max_clubs){
				$axes = $curr_axes;

			}else{
				$axes = $min_clubs + round(($max_clubs - $min_clubs) * $score * $score / 10000);
				
				if($curr_axes - $axes < $old_min_clubs)
					$axes = $curr_axes;
			}
		}else if($curr_tks >= $min_clubs){
			if($curr_tks <= $max_clubs){
				$tks = $curr_tks;

			}else{
				$tks = $min_clubs + round(($max_clubs - $min_clubs) * $score * $score / 10000);
				
				if($curr_axes - $tks < $old_min_clubs)
					$tks = $curr_tks;
			}
		}
		
		$distance = sqrt( ($target_x - $village_x) * ($target_x - $village_x) + ($target_y - $village_y) * ($target_y - $village_y) );
		echo "distance: $distance\n";
		
		$second_cost = round($distance / 5 * 3600);
		echo "second_cost: $second_cost\n";
		
		$second_cost = max($second_cost, 138 * 60);
		$next_raid_interval = rand($second_cost, 3 * $second_cost);
		echo "next_raid_interval: $next_raid_interval\n";
		
		// Update the target
		$sql = "update `targets` set `timestamp` = FROM_UNIXTIME(unix_timestamp(now()) + $next_raid_interval) where account = $account and x = " . $target_x . " and y = " . $target_y;
		mysql_query($sql);

		if(attack_func($target_x, $target_y, $clubs, $axes, $tks, '', '', '', $result, $player)){
			return ($curr_clubs - intval($clubs) >= $old_min_clubs ||
				    $curr_axes - intval($axes) >= $old_min_clubs ||
				    $curr_tks - intval($tks) >= $old_min_clubs);
		}
		
		return true;
	}
?>
