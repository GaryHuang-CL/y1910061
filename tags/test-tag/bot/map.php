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

	$members = array(
"inblackhole",
"Kimon",
"3x3x3",
"vandal",
"bear28",
"hope",
"ibm",
"dell",
"compaq",
"girlkiller",
"SmilingFish",
"slayer",
"boxer",
"nana",
"moonlight",
"JACK",
"MICK",
"ONS",
"vipman",
"ahura",
"hemes",
"aegis",
"aether",
"bellona",
"valhalla",
"eos",
"fama",
"bilbo",
"Hömeless",
"momoe",
"takadan",
"hong",
"rose",
"sulan",
"sky",
"sign",
"savagefox",
"grasscabin",
"sunday",
"zala",
"monk",
"attack",
"shark",
"MoOo",
"Chieftain",
"Blademaster",
"Paladin",
"T-Bag",
"solar",
"oomaru",
"priest",
"ichigo",
"murata",
"leave",
"yxytclal",
"jissy",
"kikurage",
"kola",
"storm",
"ashila",
"YogSothoth",
"mephisto",
"yamashita",
"kulumaru",
"soda",
"check",
"kissy",
"donburi",
"azathoth",
"icenova",
"tyakaman",
"wani",
"cissy",
"meramin",
"maron",
"y62778811",
"Cthulhu",
"HellFire",
"Marines",
"zone696830",
"archer",
"rose-1",
"huanghjh",
"sulan",
"rose",
"sulan03",
"momoe",
"sasaki",
"tanaka",
"togami",
"nakao",
"sato",
"suzuki",
"Magic",
"Angel",
"MapleStory",
"wtn2008",
"Be37",
"papurica731",
"jking",
"hero",
"docomo2",
"oUo",
"nomik",
);
	
	foreach($members as $i=>$id){
		$members[$i] = strtolower($id);
	}

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
    	
    	$img = "b5.gif";
    	
    	$warning_base = 150;
    	
    	if(array_key_exists('w', $_GET)){
    		$warning_base = intval($_GET['w']);
    	}
    	
    	if($pop > $warning_base){
   			$img = "b4.gif";
   		}
    	
    	if(in_array(strtolower($name), $members)){
    		if($pop > $warning_base)
    			$img = "b1.gif";
    		else
    			$img = "b2.gif";
    	}
    	
    	// $x1 = $x1 * 7;
    	// $y1 = $y1 * 7;

    	$x1 = $x1 * 12;
    	$y1 = $y1 * 12;
    	
    	// <img onmouseover="ts(this, 'heroryo', '398', '(21, -3)', 'vname', 'ally');" onload="l(this, 21, -3);" onmouseout="th();" src="b.jpg"/>
		//echo "<img style=\"width:7;height:7;position:absolute;top:$y1;left:$x1;\" onmouseover=\"ts(this, '" . $name . "', '" . $pop . "', '($x, $y)', '$vname', '$ally');\" onmouseout=\"th();\" onclick=\"ck('$d', '$c');\" src=\"" .
		echo "<img style=\"position:absolute;top:$y1;left:$x1;\" onmouseover=\"ts(this, '" . $name . "', '" . $pop . "', '($x, $y)', '$vname', '$ally');\" onmouseout=\"th();\" onclick=\"ck('$d', '$c');\" src=\"" .
		     $img . 
		     "\"/>\n";
    }
    
?>
</div>