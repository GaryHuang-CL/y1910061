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
	
	// t1: club, t3: axe, t6: tk, t11: hero
	// t8: catapult t7:ram
	function attack_func($target_x, $target_y, $t1, $t3, $t6, $t7, $t8, $t11, $result, $target_player = "")
	{
		global $server;
		
		$url = "http://$server/a2b.php";

		assert($t1 > 0 || $t1 == '');
		assert($t3 > 0 || $t3 == '');
		assert($t6 > 0 || $t6 == '');
		assert($t7 > 0 || $t7 == '');
		assert($t8 > 0 || $t8 == '');
		
		assert($t11 == 1 || $t11 == '');


		$hero_post_str = '';
		// onClick="document.snd.t11.value=1; return false;"
		if(preg_match('/onClick="document\.snd\.t11\.value=1; return false;/', $result, $matches)){
			// echo "Hero in home.\n";
			
			if($t11 == ''){
				$hero_post_str = '&t11=';
			}else{
				$hero_post_str = '&t11=1';
			}
			
		}

		// Post it
		// b=1&t1=1&t4=&t7=&t9=&t2=&t5=&t8=&t10=&t3=&t6=&c=3&dname=&x=-69&y=-2&s1.x=&s1.y=&s1=ok
		// b=1&t1=50&t4=&t7=&t9=&t2=&t5=&t8=&t10=&t3=&t6=&t11=1&c=3&dname=&x=-122&y=-45&s1.x=&s1.y=&s1=ok
		$postfields = 'b=1&t1=' . $t1 . '&t4=&t7='. $t7. '&t9=&t2=&t5=&t8='. $t8 . '&t10=&t3='. $t3 . '&t6=' . $t6
					. $hero_post_str
		            . '&c=3&dname=&x=' . $target_x . '&y=' . $target_y . '&s1.x=&s1.y=&s1=ok';

		echo $postfields . "\n";
		
		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_REFERER, $url);

		$result = curl_exec ($ch);
		curl_close ($ch);

		// Check if succeeded
		// "hidden" name="kid"
		$ret = preg_match('/"hidden" name="kid"/', $result, $matches);
		
		if($ret){
			// <input type="hidden" name="id" value="39">
			$ret = preg_match('/<input type="hidden" name="id" value="([0-9]+)">/', $result, $matches);
			if(!$ret) die("get id failed.");
			
			$id = $matches[1];
			// echo "id = " . $id . "\n";

			// <input type="hidden" name="a" value="46137">
			$ret = preg_match('/<input type="hidden" name="a" value="([0-9]+)">/', $result, $matches);
			if(!$ret) die("get a failed.");
			
			$a = $matches[1];
			// echo "a = " . $a . "\n";

			// <input type="hidden" name="c" value="3">
			$ret = preg_match('/<input type="hidden" name="c" value="([0-9]+)">/', $result, $matches);
			if(!$ret) die("get c failed.");
			
			$c = $matches[1];
			// echo "c = " . $c . "\n";

			// <input type="hidden" name="kid" value="356724">
			$ret = preg_match('/<input type="hidden" name="kid" value="([0-9]+)">/', $result, $matches);
			if(!$ret) die("get kid failed.");
			
			$kid = $matches[1];
			// echo "kid = " . $kid . "\n";

			// <input type="hidden" name="t1" value="10">
			$ret = preg_match('/<input type="hidden" name="t1" value="([0-9]+)">/', $result, $matches);
			if(!$ret) die("get t1 failed.");
			
			$t1 = $matches[1];
			// echo "t1 = " . $t1 . "\n";

			// <input type="hidden" name="t3" value="10">
			$ret = preg_match('/<input type="hidden" name="t3" value="([0-9]+)">/', $result, $matches);
			if(!$ret) die("get t3 failed.");
			
			$t3 = $matches[1];
			// echo "t3 = " . $t3 . "\n";

			// <input type="hidden" name="t6" value="10">
			$ret = preg_match('/<input type="hidden" name="t6" value="([0-9]+)">/', $result, $matches);
			if(!$ret) die("get t6 failed.");
			
			$t6 = $matches[1];
			// echo "t6 = " . $t6 . "\n";

			// <input type="hidden" name="t7" value="10">
			$ret = preg_match('/<input type="hidden" name="t7" value="([0-9]+)">/', $result, $matches);
			if(!$ret) die("get t7 failed.");
			
			$t7 = $matches[1];
			// echo "t7 = " . $t7 . "\n";

			// <input type="hidden" name="t8" value="10">
			$ret = preg_match('/<input type="hidden" name="t8" value="([0-9]+)">/', $result, $matches);
			if(!$ret) die("get t8 failed.");
			
			$t8 = $matches[1];
			// echo "t8 = " . $t8 . "\n";

			// <input type="hidden" name="t11" value="10">
			$ret = preg_match('/<input type="hidden" name="t11" value="([0-9]+)">/', $result, $matches);
			if(!$ret) die("get t11 failed.");
			
			$t11 = $matches[1];
			// echo "t11 = " . $t11 . "\n";

			if($target_player){
				// <td class="s7"><a href="spieler.php?uid=11783">Vinsfeld</a></td></tr>
				$ret = preg_match('#<td class="s7"><a href="spieler\.php\?uid=[0-9]+">([^<]+)</a></td></tr>#', $result, $matches);
				if(!$ret) die("get player failed.");
				
				$player = $matches[1];
				if($player != $target_player){
					echo "Error: player changed.\n";
					$sql = "update `targets` set `invalid` = 1, `invalid_msg` = '名義変更' where x = " . $target_x . " and y = " . $target_y;
					if(!mysql_query($sql)) die(mysql_error());
					return false;
				}
			}


			// id=39&a=5941&c=3&kid=322334&t1=1&t2=0&t3=0&t4=0&t5=0&t6=0&t7=0&t8=0&t9=0&t10=0&t11=0&s1.x=&s1.y=&s1=ok
			$postfields = 'id=' . $id . '&a=' . $a . '&c=' . $c . '&kid=' . $kid . '&t1=' . $t1 . '&t2=0&t3=' . $t3 . '&t4=0&t5=0&t6=' . $t6 . '&t7=' . $t7 . '&t8=' . $t8 . '&t9=0&t10=0'
			              . '&t11=' . $t11 . '&s1.x=&s1.y=&s1=ok';
			
			echo $postfields . "\n";
			
			$ch = my_curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_REFERER, $url);

			$result = curl_exec ($ch);
			curl_close ($ch);
			
			if(!$result){
				die(curl_error($ch));
			}

			echo "Attacking (" . $target_x . " , " . $target_y . ")\n";
			
			return true;
			
		}else{
			
			echo "Error: no kid found.\n";
			
			handle_bad_target($result, $target_x, $target_y);
			
			return false;
		}

	}
?>