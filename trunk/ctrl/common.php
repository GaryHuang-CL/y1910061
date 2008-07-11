<?php
	$server = "s1.travian.jp";
	$user = "xxx";
	$password = "xxxxxx";
	
	define("COOKIE_FILE", "../cookie.txt");
	define("BUSY_FILE", "../busy");

	require_once("../transfer.php");

	function my_curl_init(){
		
		if(file_exists(BUSY_FILE)){
			die("Busy.");
		}

		// $proxy = 'secure-gw.tetra-tis.net:8080';
		$user_agent = 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.0; ja; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11';
		$cookie = COOKIE_FILE;

		$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
		$header[]  = "Accept-Language: ja,en-us;q=0.7,en;q=0.3";
		$header[]  = "Accept-Charset: UTF-8,*";
		$header[]  = "Keep-Alive: 300";

		if(isset($proxy)){
			$header[] = "Proxy-Connection: keep-alive";
		}else{
			$header[] = "Connection: keep-alive";
		}
		
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_USERAGENT, $user_agent);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_COOKIEJAR, $cookie);
		curl_setopt ($ch, CURLOPT_COOKIEFILE, $cookie);
		curl_setopt ($ch, CURLOPT_MAXREDIRS, 3);
		//curl_setopt ($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt ($ch, CURLOPT_ENCODING, 'gzip,deflate');

		
		if(isset($proxy)){
			curl_setopt ($ch, CURLOPT_PROXY, $proxy);
		}
		
		// sleep(rand(1, 5));
		return $ch;
	}

?>
