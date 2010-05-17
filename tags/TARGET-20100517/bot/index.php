<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
</head>
<?php
	
	require_once("db.php");
	
	$sql = "select id, server, user from accounts order by server";
	$res = mysql_query($sql);
	if(!$res) die(mysql_error());
	
	$prev_server = "";
	echo "<ul>";
	while($row = mysql_fetch_row($res)){
		$id = $row[0];
		$server = $row[1];
		$user = $row[2];
		
		if($server != $prev_server) echo "</ul><hr><p><h4>$server</h4><ul>";
		
		echo '<li><a href="account.php?a=' . $id . '">' . $user . "</a>";
		$prev_server = $server;
	}
	
	echo "</ul><hr>";
?>
<p>
<a href="reports.php">reports</a><br>
<a href="viewpop.php">viewpop</a><br>

