<?php
	// ----------------------------------------------------------------------------
	// Main
	// ----------------------------------------------------------------------------
	require_once("db.php");
	
	if(array_key_exists('id', $_GET)){
		$id = intval($_GET['id']);
		$sql = "select content from ally_reports where `id` = $id";
	    $res = mysql_query($sql);
	    if(!$res) die(mysql_error());
		$row = mysql_fetch_row($res);
		
		echo $row[0];

	}else{
		echo '<head><meta http-equiv="content-type" content="text/html; charset=UTF-8"></head>';

		$sql = "select id, title, attack_power, defend_power from ally_reports order by id desc limit 50";
	    $res = mysql_query($sql);
	    if(!$res) die(mysql_error());
	    echo "<table>\n";
	    
	    while($row = mysql_fetch_row($res)){
	    	$id = $row[0];
	    	$title = $row[1];
	    	$attack_power = $row[2];
	    	$defend_power = $row[3];
	    	$link = $_SERVER['PHP_SELF'] . "?id=$id" ;
	    	
	    	echo "<tr><td><a href=\"$link\">$title</a></td><td>$attack_power</td><td>$defend_power</td></tr>\n";
		}
		
		echo "\n</table>";
	}
?>
