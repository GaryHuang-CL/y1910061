<?php
	require_once('common.php');

	$url = "http://$server/a2b.php";

	$ch = my_curl_init(true);

	echo $argv[1] . "\n";
	echo $argv[2] . "\n";
	
	usleep($argv[2]);
	
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $argv[1]);
	curl_setopt($ch, CURLOPT_REFERER, $url);
	
	$result = curl_exec ($ch);
	curl_close ($ch);

?>