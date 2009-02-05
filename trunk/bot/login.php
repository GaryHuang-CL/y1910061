<?php

	require_once('common.php');
	
	function login()
	{
		global $server;
		global $user;
		global $password;

		$url  = "http://$server/dorf1.php";
		
		echo $url . "\n";
		
		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		
		$result = curl_exec ($ch);
		curl_close ($ch);

		// check if need login
		$ret = preg_match('/statistiken\.php/', $result);
		
		//echo $result;
		
		if(!$ret){
			
			// Announce
			if(preg_match('/<a href="dorf1\.php\?ok/', $result)){
				echo "ANNOUNCE ...\n";

				// file_put_contents("announce.html", $result);

				$filename = "announce.html";
			    if (!$handle = fopen($filename, 'w')) {
			         die("Cannot open file ($filename)");
			    }

			    if (fwrite($handle, $result) === FALSE) {
			        die("Cannot write to file ($filename)");
			    }
				
				fclose($handle);


				$ch = my_curl_init();
				curl_setopt($ch, CURLOPT_URL, $url . '?ok=1');
				$result = curl_exec ($ch);
				curl_close ($ch);
				
				if(preg_match('/statistiken\.php/', $result)){
					echo "login ok ....\n";
					return $result;
				}else{
					die("FAILED: login ....");
				}
			}


			// <input type="hidden" name="login" value="1198128845">
			$ret = preg_match('/<input type="hidden" name="login" value="([0-9]+)"\s*>/', $result, $matches);
			if(!$ret) die("get post value login failed.");
			
			$login = $matches[1];
			// echo $login . "\n";
			
			// <input class="fm fm110" type="text" name="e96c60c" value="" maxlength="15"> <span class="e f7"></span>
			$ret = preg_match('/<input class="fm fm110" type="text" name="([a-z0-9]+)" value=".*?" maxlength="15"\s*> <span class="e f7"><\/span>/', $result, $matches);
			if(!$ret) die("get post name user failed.");

			$var_user = $matches[1];
			// echo $var_user . "\n";
			
			// <input class="fm fm110" type="password" name="ee54ffc" value="" maxlength="20"> <span class="e f7"></span>
			$ret = preg_match('/<input class="fm fm110" type="password" name="([a-z0-9]+)" value=".*?" maxlength="20"\s*> <span class="e f7"><\/span>/', $result, $matches);
			if(!$ret) die("get post name password failed.");

			$var_password = $matches[1];
			// echo $var_password . "\n";
			
			// <p align="center"><input type="hidden" name="e50439e" value="">
			$ret = preg_match('/<input type="hidden" name="([a-z0-9]{7})" value="([a-z0-9]*)"\s*>/', $result, $matches);
			if(!$ret) die("get post anonymous name and value failed.");

			$var_anonymous_name = $matches[1];
			$var_anonymous_value = $matches[2];
			// echo $var_anonymous_name . "\n";
			// echo $var_anonymous_value . "\n";


			// w=1152%3A864&login=1198128731&e96c60c=Kimon&ee54ffc=********&e50439e=cc87212bcd&s1.x=&s1.y=&s1=login&autologin=ja
			$postfields = 'w=1152%3A864'
				        . '&login=' . $login 
						. '&' . $var_user . '=' . urlencode($user) 
						. '&' . $var_password . '=' . $password 
						. '&' . $var_anonymous_name . '=' . $var_anonymous_value
						. '&s1.x=&s1.y=&s1=login';
			
			echo $postfields . "\n";
			
			$ch = my_curl_init();
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);

			$result = curl_exec ($ch);
			curl_close ($ch);

			// echo $result;
			
			$ret = preg_match('/statistiken\.php/', $result);
			if($ret){
				echo "login ok.\n";
			}else{
				echo $result;
				die("FAILED: login.");
			}

		}else{
			echo "cookie login ok.\n";
		}
		
		return $result;
	}
	
?>
