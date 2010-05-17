<?php

	function parse($result, $x, $y)
	{
		// <div id="f6"></div>
		// f6 is 15 crops, f1 is 9 crops
		if(!preg_match('/<div id="f([0-9])"><\/div>/', $result, $matches)) die("failed to parse.");
		
		$crops = 6;
		if($matches[1] == '6'){
			$crops = 15;
		} else if($matches[1] == '1'){
			$crops = 9;
		}

		$sql = "update crop_crawler set crops = $crops where x = $x and y = $y";
		
		if(!mysql_query($sql)) die(mysql_error());

	}

	// ----------------------------------------------------------------------------
	// Main
	// ----------------------------------------------------------------------------

	require_once('db.php');

	$handle = @fopen("always.txt", "r");
	if ($handle) {
		
		$flag = 0;
	    while (!feof($handle)) {
	        $buffer = fgets($handle, 4096);
	        if(strlen($buffer) < 10) continue;
	        
	        //echo $buffer;
	        $a = explode("|", $buffer);
	        
	        $x = $a[0];
	        $y = $a[1];
	        $crops = $a[5];
	        $d = $a[7];
	        $c = $a[8];
	        $bonus = $a[13];
	        
	        // ##############################################
	        // THIS MUST BE CHANGED
	        $server = 12;
	        
	        // ##############################################

	        if($crops >= 9){
	        	
	        	if($flag == 0){
					$sql = "insert crops(server, x, y, crops, d, c, bonus) values ($server, $x, $y, $crops, $d, '$c', $bonus) ";
					$flag++;
				}else if($flag < 20){
					$sql .= ", ($server, $x, $y, $crops, $d, '$c', $bonus)";
					$flag++;
				}else{
					$sql .= ", ($server, $x, $y, $crops, $d, '$c', $bonus)";
		        	echo ".";
					if(!mysql_query($sql)) die(mysql_error());
					$flag = 0;
				}
	        }
	    }
	    
	    if($flag > 0){
	    	if(!mysql_query($sql)) die(mysql_error());
	    }
	    fclose($handle);
	}
	

?>
