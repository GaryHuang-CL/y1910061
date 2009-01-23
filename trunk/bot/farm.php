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

		$max_clubs = 9;
		$min_clubs = 4;

		// How many clubs there ?
		// onClick="document.snd.t1.value=1; return false;
		if(!preg_match('/onClick="document\.snd\.t1\.value=([0-9]+); return false;/', $result, $matches)){
			// echo "no club there.\n";
			return false;
		}

		$curr_clubs = $matches[1];
		
		echo $curr_clubs . " clubs availabe.\n";
		
		if($curr_clubs < $min_clubs){
			// echo("no enough clubs (min).\n");
			return false;
		}
		
		// Get a target
		$sql = "SELECT x, y, `raid`, `score` FROM `targets` WHERE `village` = $village and `invalid` = 0 and NOW() > date_add(`timestamp`, INTERVAL (`interval` * 80) MINUTE) order by date_add(`timestamp`, INTERVAL (`interval` * 80) MINUTE) limit 1";
		
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
		
		$score = calc_score($score_str);
		
		echo "Average score:  $score \n";
		
		$old_min_clubs = $min_clubs;

		if($raid > 1){
			$min_clubs = $min_clubs * $raid;
			$max_clubs = $max_clubs * $raid;
		}

		echo "Want to raid : (" . $row[0] . " , " . $row[1] . ")\n";

		if($curr_clubs < $min_clubs){
			echo("no enough clubs. $min_clubs \n");
			return false;
		}

		
		if($curr_clubs <= $max_clubs){
			$clubs = $curr_clubs;

		}else{
			$clubs = $min_clubs + round(($max_clubs - $min_clubs) * $score / 100);
			
			if($curr_clubs - $clubs < $old_min_clubs)
				$clubs = $curr_clubs;
		}
		
		// Update the target
		$sql = "update `targets` set `timestamp` = now() where x = " . $target_x . " and y = " . $target_y;
		mysql_query($sql);

		if(attack_func($target_x, $target_y, $clubs, '', '', '', '', '', $result)){
			return $clubs < $curr_clubs;
		}
		
		return true;
	}
?>