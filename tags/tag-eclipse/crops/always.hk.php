<?php

	require_once('common.php');
	
	// Cookie: PHPSESSID=0egq8s0jni01giu58pt3a2t9v0; lc=ja; charset=utf-8; __utma=257590734.3175717163136396300.1220154355.1220154355.1220154355.1; __utmb=257590734.3.10.1220154355; __utmc=257590734; __utmz=257590734.1220154355.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none)
	// $url = "http://travian.always.hk/query.php?server=s6.travian.tw&used=all&wood_min=&wood_max=&clay_min=&clay_max=&iron_min=&iron_max=&crop_min=9&crop_max=15&areaFromX=-400&areaToX=400&areaFromY=-400&areaToY=400";
    $url = "http://travian.always.hk/query.php?server=s6.travian.hk&used=all&wood_min=&wood_max=&clay_min=&clay_max=&iron_min=&iron_max=&crop_min=9&crop_max=15&crop_oasis=&areaFromX=-400&areaToX=400&areaFromY=-400&areaToY=400";

	$start = intval(file_get_contents("page.dat"));

	if($start == 0) $start = 1;
	
	for($i = $start; $i <= 509; $i++){
		$ch = my_curl_init(true);
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
