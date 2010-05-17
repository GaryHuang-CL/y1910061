<?php
	require_once('db.php');

	$show = "SHOW TABLES";
	$show_res = mysql_query($show) or die(mysql_error());
	
	while($row = mysql_fetch_row($show_res)) {
		if(preg_match('#^x_world#', $row[0])){
			if(substr($row[0], -6) < date('ymd', time() - 3600 * 240)){
				$sql = "DROP TABLE `" . $row[0] . "`";
				mysql_query($sql) or die(mysql_error());
				echo $sql . "<br/>\n";
			}
		}
	}
?>