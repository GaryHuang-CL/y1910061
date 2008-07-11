<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
</head>
<?php
	
	// ----------------------------------------------------------------------------
	// Main
	// ----------------------------------------------------------------------------

	require_once("common.php");
	require_once("db.php");
	
    $sql = " select a.id, a.name, IFNULL(b.num, 0) from `villages` as a " . 
           " left outer join (select count(1) as num, village from build group by village) as b " .
           " on a.id =  b.village order by name";
    
    $res = mysql_query($sql);
    if(!$res) die(mysql_error());
    
    
    echo "<table>";
    while($row = mysql_fetch_row($res))
    {
    	$id = $row[0];
    	$name = $row[1];
    	$num = $row[2];
    	
		$url = "http://$server/dorf1.php?newdid=$id";
		$referer = "http://$server/dorf1.php";

		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_REFERER, $referer);
		$result = curl_exec ($ch);
		curl_close ($ch);
		
		$resources = get_res_info($result);

    	echo '<tr><td><a href="village.php?id=' . $id . '">' . $name . ' (' . $num . ')</a><td>';

		echo "<td align=right>$resources[0]</td><td align=right>&nbsp;&nbsp;</td>";
		echo "<td align=right>$resources[1]</td><td align=right>&nbsp;&nbsp;</td>";
		echo "<td align=right>$resources[2]</td><td align=right>&nbsp;&nbsp;</td>";
		echo "<td align=right>$resources[3]</td><td align=right>&nbsp;&nbsp;</td></tr>";

    }
    echo "</table>";

?>
