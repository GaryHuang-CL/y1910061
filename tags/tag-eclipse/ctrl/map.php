<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="styles.css" />
	<script language="Javascript1.2" src="xstooltip.js"></script>
</head>

<div id="tooltip" class="xstooltip">
</div> 

<div style="width:900;height:900;margin:0;padding:0;">
<?php

	require_once("common.php");
	require_once("db.php");
	
	// ----------------------------------------------------------------------------
	// Main
	// ----------------------------------------------------------------------------

    $sql = "select max(`daystamp`) from populations";
    
    $res = mysql_query($sql);
    if(!$res) die(mysql_error());
    
    $row = mysql_fetch_row($res);
    $today = $row[0];

	$sql = " select min(x), max(y) from populations ";
    $res = mysql_query($sql);
    if(!$res) die(mysql_error());
    
    $row = mysql_fetch_row($res);
    $ox = $row[0];
    $oy = $row[1];
	
	$sql = " select x, y, population, player_name, village_name, ally_name, d, c, distance, nearest_village from populations where daystamp = $today ";

    $res = mysql_query($sql);
    if(!$res) die(mysql_error());
	
    while($row = mysql_fetch_row($res)){
    	$x = $row[0];
    	$y = $row[1];
    	$pop = $row[2];
    	$name = $row[3];
    	$vname = $row[4];
    	$ally = $row[5];
    	$d = $row[6];
    	$c = $row[7];
    	
    	$x1 = abs($x - $ox);
    	$y1 = abs($y - $oy);
    	
    	$img = (strstr($ally, "HKR") ? "b2.gif" : "b5.gif");
    	
    	if(strstr($ally, "te")){
    		$img = "b4.gif";
    	}
    	
    	// <img onmouseover="ts(this, 'heroryo', '398', '(21, -3)', 'vname', 'ally');" onload="l(this, 21, -3);" onmouseout="th();" src="b.jpg"/>
		echo "<img onmouseover=\"ts(this, '" . $name . "', '" . $pop . "', '($x, $y)', '$vname', '$ally');\" onload=\"l(this, $x1, $y1);\" onmouseout=\"th();\" onclick=\"ck('$d', '$c');\" src=\"" .
		     $img . 
		     "\"/>\n";
    }
    
?>
</div>