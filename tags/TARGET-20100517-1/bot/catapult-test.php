<?php
	require_once('login.php');

	// 99: 笋@
	// 1 : ���؏�
	// 2 : �D�B
	// 3 : �c���
	// 4 : �_��
	// 5 : ���؏�
	// 6 : �A��
	// 7 : �|�c�h����
	// 8 : ����
	// 9 : ��X
	// 10 : �q��
	// 11 : ���q
	// 12 : �c��
	// 13 : �b��
	// 14 : ���Z��
	// 15 : ��䵑��
	// 16 : �W���y
	// 17 : �s��
	// 18 : ��g��
	// 19 : ���z
	// 20 : �n�I
	// 21 : �H��
	// 22 : �����@
	// 24 : ����K
	// 25 : �s�{
	// 26 : �c�{
	// 27 : ������
	// 28 : ���Տ�
	// 29 : �啺�z
	// 30 : ��n�I
	// 37 : �p�Y��

	$rally_point_level = 20;

	// club, axe, tk, ram, catapult, hero, target_one, target_two
	$plan = array(
			array('',    99999,   99999,  99,  1,   1,  99, ''),
			array('',     '',     '',     '',  1,   '', 99, ''),
			array('',     '',     '',     '',  1,   '', 99, ''),
			array('',     '',     '',     '',  1,   '', 99, ''),
			array('',     '',     '',     '',  1,   '', 99, ''),
			array('',     '',     '',     '',  1,   '', 99, ''),
			array('',     '',     '',     '',  1,   '', 99, ''),
			array('',     '',     '',     '',  1,   '', 99, ''),
			array('',     '',     '',     '',  1,   '', 99, ''),
			array('',     '',     '',     '',  1,   '', 99, ''),
			array('',     '',     '',     '',  1,   '', 99, ''),
			array('',     '',     '',     '',  1,   '', 99, ''),
			array('',     '',     '',     '',  1,   '', 99, ''),
			array('',     '',     '',     '',  1,   '', 99, ''),
			array('',     '',     '',     '',  1,   '', 99, ''),
			array('',     '',     '',     '',  1,   '', 99, ''),
			array('',     '',     '',     '',  1,   '', 99, ''),
			array('',     '',     '',     '',  1,   '', 99, ''),
			array('',     '',     '',     '',  1,   '', 99, ''),
			array('',     '',     '',     '',  1,   '', 99, ''),
			array('',     '',     '',     '',  1,   '', 99, ''),
			array('',     '',     '',     '',  1,   '', 99, ''),
			array('',     '',     '',     '',  1,   '', 99, ''),
			array('',     '',     '',     '',  1,   '', 99, ''),
			array('',     '',     '',     '',  1,   '', 99, ''),
			array('',     '',     '',     '',  1,   '', 99, ''),
			array('',     '',     '',     '',  1,   '', 99, ''),
			array('',     '',     '',     '',  1,   '', 99, ''),
			array('',     '',     '',     '',  1,   '', 99, ''),
			array('',     '',     '',     '',  1,   '', 99, ''),
			array('',     '',     '',     '',  1,   '', 99, ''),
			array('',     '',     '',     '',  1,   '', 99, ''),
			array('',     '',     '',     '',  1,   '', 99, ''),

	);

	$target_x = -45;
	$target_y = -50;


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

	$filename = "./catapult.sh";
	
	$content = '';
	foreach ($posts as $i => $postfields) {
		$content = $content . 'php catapult-child.php "' . $postfields. '" ' . ($i * 1000000 * 0.01) . " &\n";
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
	
	system("chmod 755 $filename");
	system($filename);

	echo "done.\n";

	exit();

?>