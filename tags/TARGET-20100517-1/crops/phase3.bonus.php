<?php

	// ----------------------------------------------------------------------------
	// Main
	// ----------------------------------------------------------------------------

	require_once('common.php');
	require_once('db.php');
	mysql_query("LOCK TABLES crop_crawler WRITE");
	
	$sql = "select count(1) from crop_crawler where crops is null and oasis is null";
	$res = mysql_query($sql);
	if(!$res) die(mysql_error());
	$row = mysql_fetch_row($res);
	
	if($row[0] != 0) die("run phase2 first.");

	$sql = "select x, y, d, c from crop_crawler where crops in (15, 9, 7) and (bonus = 0 or bonus is null)";
	$res = mysql_query($sql);
	if(!$res) die(mysql_error());

	while($row = mysql_fetch_row($res)){

		$x = $row[0];
		$y = $row[1];
		$d = $row[2];
		$c = $row[3];
		
		echo "($x, $y) ";
		
		// 7x7
		// todo: place near 400
		$sql = "select x, y, oasis, if(oasis = '12', 50, 25) as bonus from crop_crawler where x <= $x + 3 and x >= $x - 3 and y <= $y + 3 and y >= $y - 3 and oasis in ('12','10','11','3','6','9') order by cast(substr(oasis, 2) as unsigned) desc limit 3" ;
		// echo $sql . "\n";
		
		$res2 = mysql_query($sql);
		if(!$res2) die(mysql_error());
		
		$bonus = 0;
		while($row = mysql_fetch_row($res2)){
			$x1 = $row[0];
			$y1 = $row[1];
			$oasis = $row[2];
			$bonus = $bonus + $row[3];
			
			//echo "   ($x1, $y1)  $oasis \n";
			
		}
		
		echo $bonus . "%\n";
		
		$sql = "update crop_crawler set bonus = $bonus where x = $x and y = $y";
		if(!mysql_query($sql)) die(mysql_error());
	}
	

?>
