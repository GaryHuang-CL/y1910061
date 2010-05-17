<?php
	require_once('db.php');
	
	$d = 1;
	for($y = 400; $y >= -400; $y = $y - 1){
		$sql = "insert into crop_crawler(x, y, d) values";
		
		for($x = -400; $x <= 400; $x = $x + 1){
			$sql = $sql . "($x, $y, $d)";
			if($x < 400) $sql = $sql . ",";
			$d++;
		}
		
		if(!mysql_query($sql)) die(mysql_error());
	}

?>
