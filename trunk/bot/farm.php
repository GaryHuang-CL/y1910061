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
		$url = "http://$server/a2b.php";

		$max_clubs = 400;
		$min_clubs = 20;

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
		$sql = "SELECT x, y, `raid`, `score`, `player` FROM `targets` WHERE `village` = $village and `invalid` = 0 and NOW() > date_add(`timestamp`, INTERVAL (`interval` * 80) MINUTE) order by date_add(`timestamp`, INTERVAL (`interval` * 80) MINUTE) limit 1";
		
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
				$clubs = $min_clubs + round(($max_clubs - $min_clubs) * $score / 100);
				
				if($curr_clubs - $clubs < $old_min_clubs)
					$clubs = $curr_clubs;
			}
		}else if($curr_axes >= $min_clubs){
			if($curr_axes <= $max_clubs){
				$axes = $curr_axes;

			}else{
				$axes = $min_clubs + round(($max_clubs - $min_clubs) * $score / 100);
				
				if($curr_axes - $axes < $old_min_clubs)
					$axes = $curr_axes;
			}
		}else if($curr_tks >= $min_clubs){
			if($curr_tks <= $max_clubs){
				$tks = $curr_tks;

			}else{
				$tks = $min_clubs + round(($max_clubs - $min_clubs) * $score / 100);
				
				if($curr_axes - $tks < $old_min_clubs)
					$tks = $curr_tks;
			}
		}
		
		// Update the target
		$sql = "update `targets` set `timestamp` = now() where x = " . $target_x . " and y = " . $target_y;
		mysql_query($sql);

		if(attack_func($target_x, $target_y, $clubs, $axes, $tks, '', '', '', $result, $player)){
			return ($curr_clubs - intval($clubs) >= $old_min_clubs ||
				    $curr_axes - intval($axes) >= $old_min_clubs ||
				    $curr_tks - intval($tks) >= $old_min_clubs);
		}
		
		return true;
	}
?>
