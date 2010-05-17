<?php

	function get_post_str($result, $n)
	{
		// <input type="hidden" name="t2" value="10">
		$ret = preg_match('/<input type="hidden" name="t' . $n . '" value="([0-9]+)">/', $result, $matches);
		if(!$ret){
			echo ("get t$n failed.");
			return false;
		}
		
		$x = $matches[1];
		return "&t$n=$x";
	}
	

	function reinforce($x, $y, $army_types, $arrive_time = false)
	{
		global $server;
		
		$url = "http://$server/a2b.php";
		
		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		$result = curl_exec ($ch);
		curl_close ($ch);

		$groups = array();
		
		// How many army there ?
		foreach($army_types as $t){
			// onClick="document.snd.t1.value=1; return false;
			if(!preg_match('/onClick="document\.snd\.t' . $t . '\.value=([0-9]+); return false;/', $result, $matches)){
				echo "failed to get current army. $t \n";
				continue;
			}

			$curr_army = $matches[1];
			echo $curr_army . " army.\n";

			if($curr_army <= 0){
				echo "no reinforce army ($t) there.\n";
			}else{
				$groups[$t] = $curr_army;
			}
		}
		
		if(empty($groups)){
			echo "there is no any army there.\n";
			return false;
		}
		
		$str = '';
		$arr = array(1,4,7,9,2,5,8,10,3,6);
		
		foreach($arr as $n){
			if(array_key_exists($n, $groups)){
				$curry_army = $groups[$n];
				$str .= "&t$n=$curr_army";
			}
			else
				$str .= "&t$n=";
		}

		// b=1&t1=80&t4=&t7=&t9=&t2=&t5=&t8=&t10=&t3=&t6=&c=2&dname=&x=-128&y=-46&s1.x=&s1.y=&s1=ok
		$postfields = 'b=1' . $str . '&c=2&dname=&x=' . $x . '&y=' . $y . '&s1.x=&s1.y=&s1=ok';

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
		if(!preg_match('/"hidden" name="kid"/', $result, $matches)){
			echo "no kid found.\n";
			return false;
		}

		// <input type="hidden" name="id" value="39">
		$ret = preg_match('/<input type="hidden" name="id" value="([0-9]+)">/', $result, $matches);
		if(!$ret) die("get id failed.");
		
		$id = $matches[1];
		echo "id = " . $id . "\n";

		// <input type="hidden" name="a" value="46137">
		$ret = preg_match('/<input type="hidden" name="a" value="([0-9]+)">/', $result, $matches);
		if(!$ret) die("get a failed.");
		
		$a = $matches[1];
		echo "a = " . $a . "\n";

		// <input type="hidden" name="c" value="3">
		$ret = preg_match('/<input type="hidden" name="c" value="([0-9]+)">/', $result, $matches);
		if(!$ret) die("get c failed.");
		
		$c = $matches[1];
		echo "c = " . $c . "\n";

		// <input type="hidden" name="kid" value="356724">
		$ret = preg_match('/<input type="hidden" name="kid" value="([0-9]+)">/', $result, $matches);
		if(!$ret) die("get kid failed.");
		
		$kid = $matches[1];
		echo "kid = " . $kid . "\n";

		// get arrive time
		// <span id=tp2>14:24:06</span>
		$ret = preg_match('#<span id=tp2>([0-9]+):([0-9]+):([0-9]+)</span>#', $result, $matches);
		if(!$ret) die("get arrive time failed.");
		
		$h = $matches[1];
		$m = $matches[2];
		$s = $matches[3];
		
		echo "arrive time = $h:$m:$s \n";

		if($arrive_time){
			
			$current_seconds = $h * 3600 + $m * 60 + $s;

			if(!preg_match('#([0-9]+):([0-9]+):([0-9]+)#', $arrive_time, $matches)){
				echo "Can not parse arrive time in parameter.\n";
				return false;
			}
			
			$h = $matches[1];
			$m = $matches[2];
			$s = $matches[3];

			$arrive_seconds = $h * 3600 + $m * 60 + $s;
			
			if($arrive_seconds < $current_seconds){
				echo "Too late .\n";
				return false;
			}
			
			$wait = $arrive_seconds - $current_seconds;
			
			if($wait > 300){
				echo "Too long to wait.\n";
				return $wait;
			}
			
			echo "Sleep $wait seconds ... \n";
			sleep($wait);
		}
		
		$str = '';
		
		for($i = 1; $i <= 11; $i++){
			$s = get_post_str($result, $i);
			if($s == false) return false;
			$str .= $s;
		}
		
		// id=39&a=3953&c=2&kid=357519&t1=80&t2=0&t3=0&t4=0&t5=0&t6=0&t7=0&t8=0&t9=0&t10=0&t11=0&s1.x=&s1.y=&s1=ok
		$postfields = 'id=' . $id . '&a=' . $a . '&c=' . $c . '&kid=' . $kid . $str . '&s1.x=&s1.y=&s1=ok';
		
		echo $postfields . "\n";
		
		$ch = my_curl_init(true);
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_REFERER, $url);

		$result = curl_exec ($ch);
		curl_close ($ch);

		if(!$result){
			echo curl_error($ch);
			return false;
		}
		
		echo "reinforcing ($x, $y) ... \n";
		return true;

	}

	function generate_post_str($result, $t, $n, $num)
	{
		if($t == $n) 
			return "&t$t=$num";
		else if(preg_match('/onClick="document.snd.t' . $n . '.value=([0-9]+)/', $result, $matches))
			return "&t$n=";
		
		return '';
	}

	// Teutonic : 5, 6
	
	function build_cavalry($t, $base) 
	{
		global $server;
		
		$url = "http://$server/build.php?gid=20";

		echo $url . "\n";

		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		$result = curl_exec ($ch);
		curl_close ($ch);
		
		// onClick="document.snd.t5.value=0"
		$ret = preg_match('/onClick="document.snd.t' . $t . '.value=([0-9]+)/', $result, $matches);
		
		if(!$ret){
			echo "Failed to build cavalry.\n";
			return;
		}

		$num = intval($matches[1]);

		if($num <= $base){
			echo "No resource to build cavalry.\n";
			return;
		}
		
		$num = $num - $base;

		// <input type="hidden" name="id" value="19">
		$ret = preg_match('/<input type="hidden" name="id" value="([0-9]+)">/', $result, $matches);
		if(!$ret) die("get id failed.");
		
		$id = $matches[1];
		echo "id = " . $id . "\n";

		// <input type="hidden" name="z" value="15">
		$ret = preg_match('/<input type="hidden" name="z" value="([0-9]+)">/', $result, $matches);
		if(!$ret) die("get z failed.");
		
		$z = $matches[1];
		echo "z = " . $z . "\n";


		// <input type="hidden" name="a" value="2">
		$ret = preg_match('/<input type="hidden" name="a" value="([0-9]+)">/', $result, $matches);
		if(!$ret) die("get a failed.");
		
		$a = $matches[1];
		echo "a = " . $a . "\n";

		$str = '';
		
		// t3
		$str .= generate_post_str($result, $t, 3, $num);
		
		// t4
		$str .= generate_post_str($result, $t, 4, $num);

		// t5
		$str .= generate_post_str($result, $t, 5, $num);

		// t6
		$str .= generate_post_str($result, $t, 6, $num);

		// id=19&z=4802&a=2&t1=1&s1.x=&s1.y=&s1=ok
		$postfields = 'id=' . $id . '&z=' . $z . '&a=' . $a . $str . '&s1.x=&s1.y=&s1=ok';

		echo $postfields . "\n";

		$referer = $url;
		$url = "http://$server/build.php";

		echo $url . "\n";
		
		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_REFERER, $referer);

		$result = curl_exec ($ch);
		curl_close ($ch);
		
		if(!$result){
			die(curl_error($ch));
		}
		
		echo "Building... $num cavalry.\n";
	}

	// Teutonic : 1, 2, 3, 4
	
	function build_infantry($t, $base) 
	{
		global $server;
		
		$url = "http://$server/build.php?gid=19";

		echo $url . "\n";

		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		$result = curl_exec ($ch);
		curl_close ($ch);
		
		// onClick="document.snd.t1.value=0"
		$ret = preg_match('/onClick="document.snd.t' . $t . '.value=([0-9]+)/', $result, $matches);
		
		if(!$ret){
			echo "Failed to build infantry.\n";
			return;
		}

		$num = intval($matches[1]);

		if($num <= $base){
			echo "No resource to build infantry.\n";
			return;
		}
		
		$num = $num - $base;

		// <input type="hidden" name="id" value="19">
		$ret = preg_match('/<input type="hidden" name="id" value="([0-9]+)">/', $result, $matches);
		if(!$ret) die("get id failed.");
		
		$id = $matches[1];
		echo "id = " . $id . "\n";

		// <input type="hidden" name="z" value="15">
		$ret = preg_match('/<input type="hidden" name="z" value="([0-9]+)">/', $result, $matches);
		if(!$ret) die("get z failed.");
		
		$z = $matches[1];
		echo "z = " . $z . "\n";


		// <input type="hidden" name="a" value="2">
		$ret = preg_match('/<input type="hidden" name="a" value="([0-9]+)">/', $result, $matches);
		if(!$ret) die("get a failed.");
		
		$a = $matches[1];
		echo "a = " . $a . "\n";

		$str = '';
		
		// t1
		$str .= generate_post_str($result, $t, 1, $num);

		// t2
		$str .= generate_post_str($result, $t, 2, $num);

		// t3
		$str .= generate_post_str($result, $t, 3, $num);

		// t4
		$str .= generate_post_str($result, $t, 4, $num);

		// id=19&z=4802&a=2&t1=1&s1.x=&s1.y=&s1=ok
		$postfields = 'id=' . $id . '&z=' . $z . '&a=' . $a . $str . '&s1.x=&s1.y=&s1=ok';

		echo $postfields . "\n";

		$referer = $url;
		$url = "http://$server/build.php";

		echo $url . "\n";
		
		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_REFERER, $referer);

		$result = curl_exec ($ch);
		curl_close ($ch);
		
		if(!$result){
			die(curl_error($ch));
		}
		
		echo "Building... $num infantry.\n";
	}

?>
