<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
</head>
<?php
	require_once("common.php");
	require_once("transfer.php");

	// ----------------------------------------------------------------------------
	// Main
	// ----------------------------------------------------------------------------
	if(!array_key_exists('id', $_GET)) die("No id.");
	if(!array_key_exists('a', $_GET)) die("No a.");

	$village = $_GET['id'];
	$account = intval($_GET['a']);

	require_once("db.php");

	$sql = "select server, user, password, race, main_village, last_report from accounts where id = $account";
	$res = mysql_query($sql);
	if(!$res) die(mysql_error());
	$row = mysql_fetch_row($res);
	if(!$row) die("Account not found. $account \n");
	
	$server       = $row[0];

	$sql = "select empty_space from servers where addr = '$server'";
	$res = mysql_query($sql);
	if(!$res) die(mysql_error());
	$row = mysql_fetch_row($res);
	if(!$row) die("Server not found. $server \n");
	
	$empty_space = $row[0];

	$sql = "select id, name from build_names where account = $account and village = $village and NOW() < date_add(`timestamp`, INTERVAL 20 MINUTE) order by id";
    $res = mysql_query($sql);
    if(!$res) die(mysql_error());
    
    // echo mysql_num_rows($res) . "<br>";
    
    $download = (mysql_num_rows($res) != 40);
    
    if($download){
		
		if($village != 0){
			$url = "http://$server/dorf1.php?newdid=$village";
			$referer = "http://$server/dorf1.php";
		}else{
			$url = "http://$server/dorf1.php";
			$referer = "http://$server/dorf1.php";
		}
		
		$ch = my_curl_init(true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_REFERER, $referer);
		$result = curl_exec ($ch);
		curl_close ($ch);
		
		$resources = get_res_info($result);
		echo "$resources[0]/$resources[4]<br>";
		echo "$resources[1]/$resources[5]<br>";
		echo "$resources[2]/$resources[6]<br>";
		echo "$resources[3]/$resources[7]<br><br>";

	    echo "建設中：<br><ul>";

		// <td>鋼鐵鑄造廠 (等級 3)</td><td><span id=timer1>0:04:43</span>
		if(preg_match_all('/<td>([^<]+?)<\/td><td><span id=timer1>(.+?)<\/span>/', $result, $matches, PREG_SET_ORDER)){
			foreach ($matches as $val) {
				echo "<li>" . $val[1] . "&nbsp;" . $val[2];
			}
		}
		echo "</ul><br>\n";
		
		
		// <area href="build.php?id=1" coords="101,33,28" shape="circle" title="伐木場 等級 0">
		// <area href="build.php?id=1"
		// coords="101,33,28" shape="circle"
		// title="Holzfäller Stufe 1" alt="" />

		if(!preg_match_all('/<area href="build\.php\?id=([0-9]+)"\s+coords=".+?" shape="circle"\s+title="(.+?)"/', $result, $matches, PREG_SET_ORDER)) die("No matching dorf1.");

		$buildings = array();
		// print_r($matches);
		
		foreach ($matches as $val) {
			$buildings[$val[1]] = $val[2];
		}

		// DORF2
		$url = "http://$server/dorf2.php?newdid=$village";
		$referer = "http://$server/dorf2.php";

		$ch = my_curl_init(true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_REFERER, $referer);
		$result = curl_exec ($ch);
		curl_close ($ch);

		// <area href="build.php?id=19" title="兵營 等級 9" coords="53,91,53,37,128,37,128,91,91,112" shape="poly">
		if(!preg_match_all('/<area href="build\.php\?id=([0-9]+)" title="(.+?)" coords=".+?" shape=".+?">/', $result, $matches, PREG_SET_ORDER)) die("No matching dorf2.");

		foreach ($matches as $val) {
			$buildings[$val[1]] = $val[2];
		}
	}else{
	    while($row = mysql_fetch_row($res))
	    {
	    	$id = $row[0];
	    	$name = $row[1];
	    	
	    	$buildings[$id] = $name;
	    }
	}
	
    $sql = "select build.id, build.seq, build.gid, gids.name from build left outer join gids on gids.gid = build.gid " .
           " where build.account = $account and build.village = $village order by build.seq";
    
    $res = mysql_query($sql);
    if(!$res) die(mysql_error());
    
    echo "建設予定：<br><ul>";
    while($row = mysql_fetch_row($res))
    {
    	$id = $row[0];
		if(!array_key_exists($id, $buildings)){
			print_r($builings);
			die("<br>No build id found. " . $id);
		}
		
    	$seq = $row[1];
    	$gid = $row[2];
    	$gname = $row[3];
    	
    	if($gid == 0){
    		echo '<li><a href="abort.php?a=' . $account . '&seq=' . $seq . '">' . $id . ") " . $buildings[$id] . '</a>';
    	}else{
    		echo '<li><a href="abort.php?a=' . $account . '&seq=' . $seq . '">' . $id . ") " . $gname . '</a>';
    	}
    }
    
    echo '</ul>';

	ksort($buildings);
	
    echo "建物：<br><ul>";
	foreach ($buildings as $id => $name) {

		if($name == $empty_space){
			echo '<li><a href="gids.php?a=' . $account . '&v=' . $village . '&id=' . $id . '">' . $id . ") " . $name . '</a>';	
		}else{
    		echo '<li><a href="queue.php?a=' . $account . '&v=' . $village . '&id=' . $id . '">' . $id . ") " . $name . '</a>';	
    	}
    	
    	if($download){
    		$sql = "replace into build_names(account, village, id, name) values($account, $village, $id, '$name')";
    		if(!mysql_query($sql)) die(mysql_error());
    	}
	}
    echo '</ul>';

?>
