<?php
	require_once('login.php');
	require_once('attack_ac.php');

	// 99: ç¬‹@
	// 1 : ”°–Øê
	// 2 : “DB
	// 3 : ècáæê
	// 4 : ”_ê
	// 5 : ‹˜–Ø±
	// 6 : âA±
	// 7 : |ècèh‘¢±
	// 8 : •²±
	// 9 : •ï“X
	// 10 : ‘qŒÉ
	// 11 : ’‘q
	// 12 : èc 
	// 13 : b±
	// 14 : ‹£‹Zê
	// 15 : ‘ºäµ‘åžê
	// 16 : WŒ‹êy
	// 17 : Žsê
	// 18 : ‘åŽgŠÙ
	// 19 : •ºšz
	// 20 : ”n’I
	// 21 : Hê
	// 22 : Œ¤‹†‰@
	// 24 : é’ÁœK
	// 25 : s‹{
	// 26 : c‹{
	// 27 : ›•¨ŒÉ
	// 28 : ŒðˆÕŠ
	// 29 : ‘å•ºšz
	// 30 : ‘å”n’I
	// 37 : ‰p—Y‘î

	$rally_point_level = 20;

	// club, axe, tk, ram, catapult, hero, target_one, target_two
	$plan = array(
			array(4900, 6270,  164,  110, 140,  1 ,15,11),
			array(0,    100,    '',   '' , 38, '' ,4, 4),
			array(0,    100,    '',   '' , 38, '' ,4, 4),
			array(0,    100,    '',   '' , 38, '' ,4, 4),
			array(0,    100,    '',   '' , 20, '' ,8, 9),
	);

	$target_x = 99;
	$target_y = 26;


	// ----------------------------------------------------------------------------
	// Main
	// ----------------------------------------------------------------------------

	login();
	
	$url = "http://$server/a2b.php";

	$posts = array();
	
	foreach($plan as $i => $wave){
		list($t1, $t3, $t6, $t7, $t8, $t11, $one, $two) = $wave;
		
		$postfields = 'b=1&t1=' . $t1 . '&t4=&t7='. $t7. '&t9=&t2=&t5=&t8='. $t8 . '&t10=&t3='. $t3 . '&t6=' . $t6
					. '&t11=' . $t11
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
		if(!preg_match('/"hidden" name="kid"/', $result, $matches)) die("no kid.");
		
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

		// <input type="hidden" name="t1" value="10">
		$ret = preg_match('/<input type="hidden" name="t1" value="([0-9]+)">/', $result, $matches);
		if(!$ret) die("get t1 failed.");
		
		$t1 = $matches[1];
		echo "t1 = " . $t1 . "\n";

		// <input type="hidden" name="t3" value="10">
		$ret = preg_match('/<input type="hidden" name="t3" value="([0-9]+)">/', $result, $matches);
		if(!$ret) die("get t3 failed.");
		
		$t3 = $matches[1];
		echo "t3 = " . $t3 . "\n";

		// <input type="hidden" name="t6" value="10">
		$ret = preg_match('/<input type="hidden" name="t6" value="([0-9]+)">/', $result, $matches);
		if(!$ret) die("get t6 failed.");
		
		$t6 = $matches[1];
		echo "t6 = " . $t6 . "\n";

		// <input type="hidden" name="t7" value="10">
		$ret = preg_match('/<input type="hidden" name="t7" value="([0-9]+)">/', $result, $matches);
		if(!$ret) die("get t7 failed.");
		
		$t7 = $matches[1];
		echo "t7 = " . $t7 . "\n";

		// <input type="hidden" name="t8" value="10">
		$ret = preg_match('/<input type="hidden" name="t8" value="([0-9]+)">/', $result, $matches);
		if(!$ret) die("get t8 failed.");
		
		$t8 = $matches[1];
		echo "t8 = " . $t8 . "\n";

		// <input type="hidden" name="t11" value="10">
		$ret = preg_match('/<input type="hidden" name="t11" value="([0-9]+)">/', $result, $matches);
		if(!$ret) die("get t11 failed.");
		
		$t11 = $matches[1];
		echo "t11 = " . $t11 . "\n";

 		// kata=4&id=39&a=4859&c=3&kid=355112&t1=0&t2=0&t3=4&t4=0&t5=0&t6=0&t7=0&t8=4&t9=0&t10=0&t11=1&s1.x=&s1.y=&s1=ok
 		// kata=4&kata2=4&id=39&a=14389&c=3&kid=353508&t1=253&t2=0&t3=0&t4=0&t5=0&t6=0&t7=0&t8=52&t9=0&t10=0&t11=0&s1.x=&s1.y=&s1=ok
		$postfields = 'kata=' . $one;
		
		if($rally_point_level == 20){
			$postfields = $postfields . '&kata2=' . $two;
		}
		
		$postfields = $postfields .	'&id=' . $id . '&a=' . $a . '&c=' . $c . '&kid=' . $kid . '&t1=' . $t1 . '&t2=0&t3=' . $t3 . '&t4=0&t5=0&t6=' . $t6 . '&t7=' . $t7 . '&t8=' . $t8 . '&t9=0&t10=0'
		              . '&t11=' . $t11 . '&s1.x=&s1.y=&s1=ok';
		
		$posts[$i] = $postfields;

	}

	$filename = "./catapult2.sh";
	
	$content = '';
	foreach ($posts as $i => $postfields) {
		if($i == 0){
			$content = $content . '/usr/local/bin/php catapult-child.php "' . $postfields. '" ' . (1000000 * 0) . " &\n";
		}else{
			$content = $content . '/usr/local/bin/php catapult-child.php "' . $postfields. '" ' . (1000000 * $i * 0.2) . " &\n";
		}
	}
	
    if (!$handle = fopen($filename, 'w')) {
         echo "Cannot open file ($filename)";
         exit;
    }

    // Write $somecontent to our opened file.
    if (fwrite($handle, $content) === FALSE) {
        echo "Cannot write to file ($filename)";
        exit;
    }
	
	fclose($handle);
	
	// file_put_contents($filename, $content);
	$output = array();
	system("chmod 755 $filename", $output);
	foreach ($output as $line)
		echo "$line\n";

	system("/bin/bash $filename", $output);
	foreach ($output as $line)
		echo "$line\n";

	echo "done.\n";

	exit();

?>
