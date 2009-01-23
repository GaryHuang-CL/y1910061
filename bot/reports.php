<?php
	// ----------------------------------------------------------------------------
	// Main
	// ----------------------------------------------------------------------------
	require_once("db.php");
	require_once("attack_ac.php");
	
	if(array_key_exists('id', $_GET)){
		$id = intval($_GET['id']);
		$sql = "update reports set `read` = 1 where `id` = $id";
	    if(!mysql_query($sql)) die(mysql_error());

		$url = "http://$server/berichte.php?id=$id";
		header("Location: $url");

	}else{
		echo '<head><meta http-equiv="content-type" content="text/html; charset=UTF-8"></head>';

		$sql = "select id, title, `read` from reports order by id desc limit 100";
	    $res = mysql_query($sql);
	    if(!$res) die(mysql_error());
	    
	    while($row = mysql_fetch_row($res)){
	    	$id = $row[0];
	    	$title = $row[1];
	    	$mark = $row[2];
	    	
	    	if($mark == 1){
	    		echo "<a href=\"http://$server/berichte.php?id=$id\">$title</a><br>\n";
	    	}else{
	    		echo "<a href=\"reports.php?id=$id\"><b>$title</b></a><br>\n";
	    	}
		}
	}
?>
