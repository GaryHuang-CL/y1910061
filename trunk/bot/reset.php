<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
</head>
<?php

	if(!array_key_exists('a', $_GET)) die("No a.");
	$account = $_GET['a'];
	
	require_once("common.php");
	require_once("db.php");

	$sql = "select server, user, password, race, main_village, last_report from accounts where id = $account";
	$res = mysql_query($sql);
	if(!$res) die(mysql_error());
	$row = mysql_fetch_row($res);
	if(!$row) die("Account not found. $account \n");
	
	$server       = $row[0];

	$url = "http://$server/dorf1.php";

	$ch = my_curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	$result = curl_exec ($ch);
	curl_close ($ch);
	
	$all = array();
	// <td class="nbr"><span>&#8226;</span>&nbsp; <a href="http://speed.travian.hk/dorf1.php?newdid=91866">Base</a></td>
	// <td class="nbr"><span class="c2">&#8226;</span>&nbsp; <a href="" class="active_vl">Home</a></td>
	if(preg_match_all('/<td class="nbr">.+?newdid=([0-9]+)[^0-9]*?">(.+?)<\/a><\/td>/', $result, $matches, PREG_SET_ORDER)){
		foreach ($matches as $val) {
			// echo $val[1] . ", " . $val[2] . "\n";
			array_push($all, array($val[1], $val[2]));
		}
	}
	
	// <td class="right dlist1">(-53</td>
	if(preg_match_all('/<td class="right dlist1">\(([-0-9]+)<\/td>/', $result, $matches, PREG_SET_ORDER)){
		$i = 0;
		
		if(count($matches) != count($all)) die("x num failed.");
		
		foreach ($matches as $val) {
			// echo $val[1] . "\n";
			array_push($all[$i], $val[1]);
			$i++;
		}
	}
	
	// <td class="left dlist3">-54)</td>
	if(preg_match_all('/<td class="left dlist3">([-0-9]+)\)<\/td>/', $result, $matches, PREG_SET_ORDER)){
		$i = 0;
		
		if(count($matches) != count($all)) die("y num failed.");
		
		foreach ($matches as $val) {
			// echo $val[1] . "\n";
			array_push($all[$i], $val[1]);
			$i++;
		}
	}
	
	$all_id = array();
	foreach ($all as $val) {
		echo "$val[0] , $val[1]  ($val[2], $val[3])<br>\n";
		$all_id[$val[0]] = array($val[1], $val[2], $val[3]);
	}

	$db_all = array();
	
	$sql = "select id from villages where account = $account";
	
	$res = mysql_query($sql);
	
	if(!$res) die(mysql_error());
	
    while($row = mysql_fetch_row($res))
    {
    	$id = $row[0];
		array_push($db_all, $id);
	}
	
	foreach ($all_id as $id => $val) {
		$name = mysql_escape_string($val[0]);
		$x = $val[1];
		$y = $val[2];
		
		if(in_array($id, $db_all)){			
			$sql = "update villages set name = '$name', x = $x, y = $y where id = $id and account = $account";
			if(!mysql_query($sql)) die(mysql_error());
		}else{
			echo "inserting $id, $name, $x, $y<br>\n";
			$sql = "insert into villages(account, id, name, x, y, noraid) values($account, $id, '$name', $x, $y, 1)";
			if(!mysql_query($sql)) die(mysql_error());
		}
	}
	
	foreach ($db_all as $id) {
		if(!array_key_exists($id, $all_id)){
			echo "deleting $id<br>\n";
			$sql = "delete from villages where id = $id and account = $account";
			if(!mysql_query($sql)) die(mysql_error());
		}
	}
	
?>
