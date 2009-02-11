<?php
	// ----------------------------------------------------------------------------
	// Main
	// ----------------------------------------------------------------------------
	require_once("db.php");
	require_once("attack_ac.php");
	
	if(array_key_exists('id', $_GET)){
		$id = intval($_GET['id']);
		$sql = "select content from ally_reports where `id` = $id";
	    $res = mysql_query($sql);
	    if(!$res) die(mysql_error());
		$row = mysql_fetch_row($res);
		
		$content = $row[0];
		
		$content = str_replace('<a href="', '<a href="http://' . $server . '/', $content);
		$content = preg_replace('#<a href=([^"])#', '<a href=http://' . $server . '/$1', $content);
		$content = str_replace(' src="', ' src="http://' . $server . '/', $content);

		echo $content;

	}else{
		echo '<head><meta http-equiv="content-type" content="text/html; charset=UTF-8"></head>';

		$start = 0;
		if(array_key_exists('p', $_GET)){
			$start = $_GET['p'] * 50;
		}
		
		$sql = "select id, title, attack_power, defend_power, attack_ally, defend_ally, `datetime` from ally_reports where 1 = 1";

		if(array_key_exists('u', $_GET)){
			$uid = $_GET['u'];
			$sql = $sql . " and (attack_uid = $uid or defend_uid = $uid) ";
		}

		if(array_key_exists('min', $_GET)){
			$min_power = $_GET['min'];
			$sql = $sql . " and (attack_power >= $min_power or defend_power >= $min_power) ";
		}

		$sql = $sql . " order by id desc limit $start, 50";
		
	    $res = mysql_query($sql);
	    if(!$res) die(mysql_error());
	    echo "<table>\n";
	    
	    while($row = mysql_fetch_row($res)){
	    	$id = $row[0];
	    	$title = $row[1];
	    	$attack_power = $row[2];
	    	$defend_power = $row[3];
	    	$ally1 = $row[4];
	    	$ally2 = $row[5];
	    	$datetime = $row[6];
	    	
	    	$link = $_SERVER['PHP_SELF'] . "?id=$id" ;
	    	
	    	echo "<tr><td><a href=\"$link\">$title</a></td><td>$attack_power</td><td>$defend_power</td><td>$ally1</td><td>$ally2</td><td>$datetime</td></tr>\n";
		}
		
		echo "\n</table>";
		
		
		$link = $_SERVER['PHP_SELF'] . "?p=" . ($start / 50 + 1);
		echo "<p><a href=\"$link\">Next</a></p>\n";
	}
?>
