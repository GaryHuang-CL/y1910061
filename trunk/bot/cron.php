<?php
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

	require_once('db.php');
	
	$sql = "select id, server, user, busy from accounts where next_check_time < now() order by rand()";
	$res = mysql_query($sql);
	if(!$res) die(mysql_error());
	while($row = mysql_fetch_row($res)){
		$id = $row[0];
		$server = $row[1];
		$user = $row[2];
		$busy = $row[3];
		
		if($id == 1) continue; // squash
		if($id == 16) continue; // Kimon@com4, conflict here.
		
		echo " ----- $user @ $server -----\n";

		$output = array();
		exec("/usr/local/bin/php cron_one.php $id", $output);
		foreach ($output as $line)
			echo "$line\n";
	}
	
?>
