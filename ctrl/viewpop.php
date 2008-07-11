<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
</head>
	
<?php

	$time_start = microtime(true);

	// ----------------------------------------------------------------------------
	// Main
	// ----------------------------------------------------------------------------

	$chars = array("-", "①", "②", "③", "④", "⑤", "⑥", "⑦", "⑧", "⑨", "⑩", "⑪", "⑫", "⑬", "⑭", "⑮", "⑯", "⑰", "⑱", "⑲", "⑳");
	
	require_once("db.php");
	require_once("common.php");
	
	$limit = 0;
	
	if(array_key_exists('limit', $_GET)){
		$limit = $_GET['limit'];;
	}



    $sql = "select max(`daystamp`) from populations";
    
    $res = mysql_query($sql);
    if(!$res) die(mysql_error());
    
    $row = mysql_fetch_row($res);
    $today = $row[0];

	$sql = "select m.* from " .
	" (select a.x, a.y, c.pop as pop1, b.pop as pop2, a.pop as pop3, a.player_name, a.village_name, a.ally_name, a.d, a.c, a.distance, IFNULL(d.invalid, 3) as invalid, d.invalid_msg, a.nearest_village, v.name, IFNULL(d.interval, 1), IFNULL(d.raid, 1)" .
	" from " .
	" (select x, y, population as pop, player_name, village_name, ally_name, d, c, distance, nearest_village from populations where daystamp = $today ";
	
	if(array_key_exists('x', $_GET)){
		$sql = $sql . " and x = " . intval($_GET['x']);
	}
	
	$sql = $sql . ") as a " .

	" left outer join " .
	" (select x, y, population as pop from populations where daystamp = $today - 1) as b " .
	" on a.x = b.x and a.y = b.y " .
		
	" left outer join " .
	" (select x, y, population as pop from populations where daystamp = $today - 2) as c " .
	" on a.x = c.x and a.y = c.y " .

	" left outer join targets as d" .
	" on a.x = d.x and a.y = d.y " .

	" left outer join villages as v" .
	" on v.id = a.nearest_village) as m ";
	
	
	// Limit :
	// 1 : fixed pop
	// 2 : farmed 
	if($limit == 1){
		$sql = $sql . " where m.pop3 <= m.pop2 and m.pop2 <= m.pop1 ";
	}else if($limit == 2){
		$sql = $sql . " where m.invalid = 0 ";
	}else if(array_key_exists('a', $_GET)){
		$ally = $_GET['a'];;
		$sql = $sql . " where m.ally_name LIKE '%" . $ally . "%' ";
	}


	$sql = $sql . " order by m.distance ";

	$p = array_key_exists('p', $_GET) ? intval($_GET['p']) : 0;
	
	$sql = $sql . " limit ". ($p * 20) . ", 20 ";

    $res = mysql_query($sql);
    if(!$res) die(mysql_error());
	
	echo "<table>";
	
    while($row = mysql_fetch_row($res)){
    	$x = $row[0];
    	$y = $row[1];
    	
    	$day0 = $row[2];
    	$day1 = $row[3];
    	$day2 = $row[4];
    	
    	$player_name  = $row[5];
    	$village_name = $row[6];
    	$ally_name    = $row[7];
    	
    	$d = $row[8];
    	$c = $row[9];
    	
    	$distance = $row[10];
    	$invalid  = $row[11];
    	$errmsg   = $row[12];

		$my_v_id = $row[13];
		$my_v_name = $row[14];
		
		$interval = $row[15];
		$raid = $row[16];
    	if($raid == 0) $raid = 1;

    	echo "<tr><td><a href=\"http://$server/karte.php?d=$d&c=$c\">$village_name</a></td><td>$x</td><td>$y</td><td>$distance</td><td>$player_name</td><td>$ally_name</td>";

		if($day0 == $day1){
			echo "<td style=\"background-color:gray\">$day0</td>";
		}else{
    		echo "<td>$day0</td>";
    	}
    	
    	if($day1 == $day2){
    		echo "<td style=\"background-color:gray\">$day1</td><td style=\"background-color:gray\">$day2</td><td>";
    	}else{
    		echo "<td>$day1</td><td>$day2</td><td>";
    	}

		$new_raid = $raid + 1;
		
	    // farm 1 hour
    	if($invalid == 0 && $interval == 1){
    		if($new_raid <= 20){
    			echo "<a href=\"alter.php?x=$x&y=$y&t=0&i=1&r=$new_raid\">" . $chars[$raid] . "</a>";
    		}else{
    			echo "<a href=\"alter.php?x=$x&y=$y&t=3\">" . $chars[$raid] . "</a>";
    		}
    		
    	}else{
    		echo "<a href=\"alter.php?x=$x&y=$y&t=0&i=1&r=1\">×</a>";
    	}
    	
    	echo "</td><td>";

	    // farm 2 hour
    	if($invalid == 0 && $interval == 2){
    		if($new_raid <= 5){
	    		echo "<a href=\"alter.php?x=$x&y=$y&t=0&i=2&r=$new_raid\">" . $chars[$raid] . "</a>";
    		}else{
    			echo "<a href=\"alter.php?x=$x&y=$y&t=3\">" . $chars[$raid] . "</a>";
    		}
    	}else{
    		echo "<a href=\"alter.php?x=$x&y=$y&t=0&i=2&r=1\">×</a>";
    	}
    	
    	echo "</td><td>";

	    // farm 4 hour
    	if($invalid == 0 && $interval == 4){
    		if($new_raid <= 5){
	    		echo "<a href=\"alter.php?x=$x&y=$y&t=0&i=4&r=$new_raid\">" . $chars[$raid] . "</a>";
    		}else{
    			echo "<a href=\"alter.php?x=$x&y=$y&t=3\">" . $chars[$raid] . "</a>";
    		}
    	}else{
    		echo "<a href=\"alter.php?x=$x&y=$y&t=0&i=4&r=1\">×</a>";
    	}
    	
    	echo "</td><td>";

	    // farm 8 hour
    	if($invalid == 0 && $interval == 8){
    		if($new_raid <= 5){
	    		echo "<a href=\"alter.php?x=$x&y=$y&t=0&i=8&r=$new_raid\">" . $chars[$raid] . "</a>";
    		}else{
    			echo "<a href=\"alter.php?x=$x&y=$y&t=3\">" . $chars[$raid] . "</a>";
    		}
    	}else{
    		echo "<a href=\"alter.php?x=$x&y=$y&t=0&i=8&r=1\">×</a>";
    	}
    	
    	echo "</td><td>";

		// missions
		$sql = "select `seq`, `hour`, `min_clubs`, `recursive` from `mission` where x = $x and y = $y";
	    $res2 = mysql_query($sql);
	    if(!$res2) die(mysql_error());
	    while($row2 = mysql_fetch_row($res2)){
	    	$seq = $row2[0];
	    	$hour = $row2[1];
	    	$min_clubs = $row2[2];
	    	$recursive = $row2[3];
	    	
    		echo "<a href=\"addmission.php?seq=$seq&d=1\">$hour/$min_clubs";
    		
    		if($recursive > 0) echo "*";
    		
    		echo "</a><br>";
    	}
    	
   		echo "</td><td><a href=\"addmission.php?x=$x&y=$y\">Add</a></td>";
    	echo "</td><td>$my_v_name</td><td>$errmsg</td></tr>\n";
    	
    }
    
    echo "</table><br>";
    
    if($p > 0){
	    echo '<a href="viewpop.php?p=' . ($p - 1);
	   	echo "&limit=" . $limit;
	   	
	   	echo '">Prev</a>&nbsp;';
    }
    
    $p = $p + 1;
    echo '<a href="viewpop.php?p=' . $p;
   	echo "&limit=" . $limit;
	
   	echo '">Next</a><br><hr>';

	$time_end = microtime(true);
	echo round($time_end - $time_start, 5);

?>
</html>