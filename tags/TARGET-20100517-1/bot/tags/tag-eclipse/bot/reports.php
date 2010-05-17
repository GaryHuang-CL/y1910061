<?php
	// ----------------------------------------------------------------------------
	// Main
	// ----------------------------------------------------------------------------
	require_once("db.php");

	if(!array_key_exists('a', $_GET)) die("no parameter.");
	
	$account = $_GET['a'];

	$sql = "select server from accounts where id = $account";
    $res = mysql_query($sql);
    if(!$res) die(mysql_error());
	$row = mysql_fetch_row($res);
	if(!$row) die("Account not found.");
	$server = $row[0];

	if(array_key_exists('id', $_GET)){
		$id = intval($_GET['id']);
		$sql = "update reports set `read` = 1 where account = $account and `id` = $id";
	    if(!mysql_query($sql)) die(mysql_error());

		$url = "http://$server/berichte.php?id=$id";
		header("Location: $url");

	}else{
		echo '<head><meta http-equiv="content-type" content="text/html; charset=UTF-8"></head>';

		$sql = "select id, title, `read` from reports where account = $account order by id desc limit 100";
	    $res = mysql_query($sql);
	    if(!$res) die(mysql_error());
	    
	    while($row = mysql_fetch_row($res)){
	    	$id = $row[0];
	    	$title = $row[1];
	    	$mark = $row[2];
	    	
	    	if($mark == 1){
	    		echo "<a href=\"http://$server/berichte.php?id=$id\">$title</a><br>\n";
	    	}else{
	    		echo "<a href=\"reports.php?a=$account&id=$id\"><b>$title</b></a><br>\n";
	    	}
		}
	}
?>
