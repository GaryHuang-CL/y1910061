<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
</head>
<?php
	function parse_html($html)
	{
		$ret = array();
		
		$doc = new DOMDocument();
		$doc->loadHTML($html);
		$xpath = new DOMXPath($doc);

		$table = $doc->getElementById('vlist');
		
		$query = 'tbody/tr';
		$entries = $xpath->query($query, $table);
		
		foreach ($entries as $entry) {
		    $nameNode = $entry->childNodes->item(1);
		    $coods = $entry->childNodes->item(2)->getElementsByTagName('div');
		    
		    list(,$x) = explode("(", $coods->item(0)->nodeValue);
		    list($y,) = explode(")", $coods->item(2)->nodeValue);
		    
		    list(,$id) = explode("=", $nameNode->firstChild->attributes->getNamedItem("href")->nodeValue);
		    
		    $name = $nameNode->nodeValue;
		    $ret[$id] = array($name, $x, $y);
		}

		return $ret;
	}
	
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
	
	$all_id = parse_html($result);

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
