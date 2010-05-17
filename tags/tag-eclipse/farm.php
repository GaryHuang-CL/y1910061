<?php

	require_once("attack_func.php");
	
	// Target : villages without playing
	// Return true if there are clubs remain
	
	function farm($result, $village)
	{
		global $server;
		$url = "http://$server/a2b.php";
		$min_clubs = 5;

		// How many clubs there ?
		// onClick="document.snd.t1.value=1; return false;
		if(!preg_match('/onClick="document\.snd\.t1\.value=([0-9]+); return false;/', $result, $matches)){
			echo "no club there.\n";
			return false;
		}

		$curr_clubs = $matches[1];
		echo $curr_clubs . " clubs.\n";
		
		if($curr_clubs < $min_clubs){
			echo("no enough clubs (min).\n");
			return false;
		}
		
		// Get a target
		$sql = "SELECT x, y, `raid` FROM `targets` WHERE `village` = $village and `invalid` = 0 and NOW() > date_add(`timestamp`, INTERVAL (`interval` * 70) MINUTE) order by date_add(`timestamp`, INTERVAL (`interval` * 70) MINUTE) limit 1";
		
		$res = mysql_query($sql);
		if(!$res) die(mysql_error());

		$row = mysql_fetch_row($res);
		if(!$row){
			echo "Warning: too many clubs. $village \n";
			return false;
		}

		$target_x = $row[0];
		$target_y = $row[1];
		$raid = $row[2];

		if($raid == 0){
			$clubs = $min_clubs;
		}else{
			$clubs = $min_clubs * $raid;
		}

		echo $row[0] . " , " . $row[1] . "\n";

		if($curr_clubs < $clubs){
			echo("no enough clubs. $clubs\n");
			return false;
		}

		
		if($curr_clubs < $clubs + $min_clubs){
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