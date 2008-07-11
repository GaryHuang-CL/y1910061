<?php

	require_once('common.php');
	$url = "http://travian.always.hk/query.php?server=s2.travian.hk&used=all&wood_min=&wood_max=&clay_min=&clay_max=&iron_min=&iron_max=&crop_min=9&crop_max=15&areaFromX=-400&areaToX=400&areaFromY=-400&areaToY=400";

	$start = intval(file_get_contents("page.dat"));

	if($start == 0) $start = 1;
	
	for($i = $start; $i <= 510; $i++){
		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL, $url . '&page=' . $i);
		curl_setopt($ch, CURLOPT_REFERER, 'http://travian.always.hk/');
		

		$result = curl_exec ($ch);
		
		if(!$result){
			die(curl_error($ch));
		}
		echo $result;

		
		file_put_contents("page.dat", $i+1);

		curl_close ($ch);

	}
	
?>
