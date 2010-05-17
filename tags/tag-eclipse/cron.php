<?php
	function get_a2b_page()
	{
		global $server;

		// get a2b page
		$url = "http://$server/a2b.php";
		echo $url . "\n";
		
		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		$result = curl_exec ($ch);
		curl_close ($ch);

		return $result;
	}

	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

	// ----------------------------------------------------------------------------
	// Functions
	// ----------------------------------------------------------------------------
	require_once('utils.php');
	require_once('farm.php');
	require_once('attack.php');
	require_once('build.php');
	require_once('login.php');
	require_once('db.php');
	require_once('transfer.php');
	require_once('army.php');

	// ----------------------------------------------------------------------------
	// Main
	// ----------------------------------------------------------------------------
	if(file_exists(CATAPULT_BUSY_FILE)){
		die("Catapulting...");
	}
	
	$result = login();
	
	$sql = "select id, auto_transfer from villages order by rand()";
	$res = mysql_query($sql);
	if(!$res) die(mysql_error());
	
	$main_village = 0;
	
	while($row = mysql_fetch_row($res)){
		$village = $row[0];
		$auto_transfer = $row[1];

		if($auto_transfer > 0){
			
			// villages in heavy defending
			if($village == 999999){
				transfer_to_village($village, $auto_transfer, true);
				
			// resource village
			}else{
				transfer_to_village($village, $auto_transfer);
			}
			
		}else{
			if($village > 0)
				$result = switch_village($village);
			
			build($village, $result);
			
			if($village == $main_village){

				build_infantry(2, 25);

				$result = get_a2b_page();
				
				if(!attack($result, $village)){
					for($i = 0; $i < 20; $i++){
						if(!farm($result, $village)) break;
						
						// refresh a2b page
						$result = get_a2b_page();
					}
				}
/*			}else{
				// villages in building
				transfer_to_village($village, $main_village, false, 70);
*/
			}
		}
		
	}
?>
