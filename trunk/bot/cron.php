<?php
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

	require_once('db.php');
	
	$sql = "select id, server, user, busy from accounts order by rand()";
	$res = mysql_query($sql);
	if(!$res) die(mysql_error());
	while($row = mysql_fetch_row($res)){
		$id = $row[0];
		$server = $row[1];
		$user = $row[2];
		$busy = $row[3];
		
		if($id == 2) continue; // inblackhole
		if($id == 8) continue; // papurica731
		if($id == 11) continue;
		
		echo " ----- $user @ $server -----\n";

		$output = array();
		exec("/usr/local/bin/php cron_one.php $id", $output);
		foreach ($output as $line)
			echo "$line\n";

	}
	
?>
