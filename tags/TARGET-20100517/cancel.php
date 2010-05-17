<?php
	function get_a2b_page()
	{
		global $server;

		// get a2b page
		$url = "http://$server/build.php?gid=16";
		echo $url . "\n";
		
		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		$result = curl_exec ($ch);
		curl_close ($ch);

		return $result;
	}


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

	$result = get_a2b_page();

	$ret = preg_match_all('/<a href="(build\.php\?id=[0-9]+&a=[0-9]+&t=[0-9]+)"><img src="http:\/\/img\.travian\.com\/hki\/img\/un\/a\/del\.gif"/', $result, $matches, PREG_SET_ORDER);

	if(!$ret) die("matching failed.");
	
	foreach ($matches as $val) {
		 $url = "http://$server/" . $val[1];
		 
		 echo $url . "\n";
		 
		 $ch = my_curl_init();
		 curl_setopt($ch, CURLOPT_URL, $url);
		 $result = curl_exec ($ch);
		 curl_close ($ch);
	}
	
	echo "Done.\n";

?>
