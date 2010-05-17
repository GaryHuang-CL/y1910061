<?php
	function auto_reinforce_axe($x, $y, $num)
	{
		global $server;
		
		$url = "http://$server/a2b.php";
		
		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		$result = curl_exec ($ch);
		curl_close ($ch);

		// How many army there ?
		// onClick="document.snd.t1.value=1; return false;
		if(!preg_match('/onClick="document\.snd\.t3\.value=([0-9]+); return false;/', $result, $matches)){
			echo "failed to get current army.\n";
			return false;
		}

		$curr_army = $matches[1];
		echo $curr_army . " army.\n";

		if($curr_army < $num){
			echo "no engouth reinforce army there. $curr_army need $num \n";
			return false;
		}

		$curr_army = $num;
		
		// b=1&t1=80&t4=&t7=&t9=&t2=&t5=&t8=&t10=&t3=&t6=&c=2&dname=&x=-128&y=-46&s1.x=&s1.y=&s1=ok
		$postfields = 'b=1&t1=&t4=&t7=&t9=&t2=&t5=&t8=&t10=&t3=' . $curr_army . '&t6=&c=2&dname=&x=' . $x . '&y=' . $y . '&s1.x=&s1.y=&s1=ok';

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

		// <input type="hidden" name="t2" value="10">
		$ret = preg_match('/<input type="hidden" name="t3" value="([0-9]+)">/', $result, $matches);
		if(!$ret) die("get t3 failed.");
		
		$t3 = $matches[1];
		echo "t3 = " . $t3 . "\n";

		// id=39&a=3953&c=2&kid=357519&t1=80&t2=0&t3=0&t4=0&t5=0&t6=0&t7=0&t8=0&t9=0&t10=0&t11=0&s1.x=&s1.y=&s1=ok
		$postfields = 'id=' . $id . '&a=' . $a . '&c=' . $c . '&kid=' . $kid . '&t1=0&t2=0&t3=' . $t3 . '&t4=0&t5=0&t6=0&t7=0&t8=0&t9=0&t10=0'
		              . '&t11=0&s1.x=&s1.y=&s1=ok';
		
		echo $postfields . "\n";
		
		$ch = my_curl_init();
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
		
		echo "reinforcing ($x, $y) $num axes ... \n";
		return true;
	}

	function auto_reinforce_tk($x, $y, $num)
	{
		global $server;
		
		$url = "http://$server/a2b.php";
		
		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		$result = curl_exec ($ch);
		curl_close ($ch);

		// How many army there ?
		// onClick="document.snd.t1.value=1; return false;
		if(!preg_match('/onClick="document\.snd\.t6\.value=([0-9]+); return false;/', $result, $matches)){
			echo "failed to get current army.\n";
			return false;
		}

		$curr_army = $matches[1];
		echo $curr_army . " army.\n";

		if($curr_army < $num){
			echo "no engouth reinforce army there. $curr_army need $num \n";
			return false;
		}

		$curr_army = $num;
		
		// b=1&t1=80&t4=&t7=&t9=&t2=&t5=&t8=&t10=&t3=&t6=&c=2&dname=&x=-128&y=-46&s1.x=&s1.y=&s1=ok
		$postfields = 'b=1&t1=&t4=&t7=&t9=&t2=&t5=&t8=&t10=&t3=&t6=' . $curr_army . '&c=2&dname=&x=' . $x . '&y=' . $y . '&s1.x=&s1.y=&s1=ok';

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

		// <input type="hidden" name="t2" value="10">
		$ret = preg_match('/<input type="hidden" name="t6" value="([0-9]+)">/', $result, $matches);
		if(!$ret) die("get t6 failed.");
		
		$t6 = $matches[1];
		echo "t6 = " . $t6 . "\n";

		// id=39&a=3953&c=2&kid=357519&t1=80&t2=0&t3=0&t4=0&t5=0&t6=0&t7=0&t8=0&t9=0&t10=0&t11=0&s1.x=&s1.y=&s1=ok
		$postfields = 'id=' . $id . '&a=' . $a . '&c=' . $c . '&kid=' . $kid . '&t1=0&t2=0&t3=0&t4=0&t5=0&t6=' . $t6 . '&t7=0&t8=0&t9=0&t10=0'
		              . '&t11=0&s1.x=&s1.y=&s1=ok';
		
		echo $postfields . "\n";
		
		$ch = my_curl_init();
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
		
		echo "reinforcing ($x, $y) $num tk ... \n";
		return true;
	}

	require_once('common.php');
	require_once('login.php');

	login();

	auto_reinforce_axe(-60, -61, 4000);
	auto_reinforce_axe(-45, -49, 3000);
	auto_reinforce_axe(-46, -51, 18000);

	auto_reinforce_tk(-39, -79, 2000);
	auto_reinforce_tk(-38, -80, 2000);
	auto_reinforce_tk(-68, -16, 1800);
	auto_reinforce_tk(-42, -80, 1500);
	auto_reinforce_tk(-3,  -38, 1400);


?>
