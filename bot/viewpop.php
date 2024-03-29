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

	$limit = 0;
	
	if(array_key_exists('limit', $_GET)){
		$limit = $_GET['limit'];;
	}

	if(!array_key_exists('x', $_GET) || !array_key_exists('y', $_GET) || !array_key_exists('a', $_GET)) die("No x|y|a.");
	
	$ox = $_GET['x'];
	$oy = $_GET['y'];
	$account = $_GET['a'];

	$sql = "select server from accounts where id = $account";
    $res = mysql_query($sql);
    if(!$res) die(mysql_error());
	$row = mysql_fetch_row($res);
	if(!$row) die("Account not found.");
	$server = $row[0];

	$fx = $_GET['fx'];

	$tblname = "x_world_" . str_replace(".", "_", $server) . "_" . date('ymd', time() - 3600);
	$tblname_yesterday = "x_world_" . str_replace(".", "_", $server) . "_" . date('ymd', time() - 3600 * 24);
	$tblname_before_yesterday = "x_world_" . str_replace(".", "_", $server) . "_" . date('ymd', time() - 3600 * 48);
	$tblname_idle_player = "idle_players_" . str_replace(".", "_", $server);
	$tblname_idle_village = "idle_villages_" . str_replace(".", "_", $server);

	$p = array_key_exists('p', $_GET) ? intval($_GET['p']) : 0;
	$start = ($p * 20);
	
	$sql = "
select a.x, a.y, c.population as pop1, b.population as pop2, a.population as pop3, a.player, a.village, a.alliance, a.id, NULL, 
		round(sqrt((a.x - $ox) * (a.x - $ox) + (a.y - $oy) * (a.y - $oy)), 1) as distance, IFNULL(d.invalid, 3) as invalid, d.invalid_msg, NULL, NULL, IFNULL(d.interval, 1), IFNULL(d.raid, 1), d.score, e.id, a.uid
from $tblname a
left outer join $tblname_yesterday b on a.id = b.id
left outer join $tblname_before_yesterday c on a.id = c.id " .

	(($limit == 2) ? " inner join targets as d on d.account = $account and a.x = d.x and a.y = d.y and d.invalid = 0 "
				   : " left outer join targets as d on d.account = $account and a.x = d.x and a.y = d.y ") .
		
	(($limit == 1) ? " inner join $tblname_idle_village e on a.id = e.id "
				   : " left outer join $tblname_idle_village e on a.id = e.id ");

	if(array_key_exists('fx', $_GET))
		$sql .= " where a.x = " . $_GET['fx'];
	
	$sql .= " order by distance, player, village limit $start, 20 ";

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
		$score = $row[17];
		$idle = $row[18];
		$uid = $row[19];

		$sql = "select count(1) from ally_reports r where r.attack_uid = $uid or r.defend_uid = $uid";
	    $res2 = mysql_query($sql);
	    if(!$res2) die(mysql_error());
		
		$row2 = mysql_fetch_row($res2);
		$reports = $row2[0];
		
    	if($raid == 0) $raid = 1;
		if($raid > 5) $raid = 1;
		
    	echo "<tr><td><a href=\"http://$server/karte.php?z=$d\">$village_name</a></td><td>$x</td><td>$y</td><td>$distance</td><td>$player_name</td><td>$ally_name</td>";

		if(!empty($idle)){
    		echo "<td style=\"background-color:gray\">$day0</td><td style=\"background-color:gray\">$day1</td><td style=\"background-color:gray\">$day2</td><td>";
    	}else{
    		echo "<td>$day0</td><td>$day1</td><td>$day2</td><td>";
    	}

		$new_raid = $raid + 1;
		
		$referer = base64_encode($_SERVER['REQUEST_URI']);
		
    	if($invalid == 0 && $raid == 1){
    		echo "<a href=\"alter.php?referer=$referer&a=$account&x=$x&y=$y&t=3\">" . $chars[$raid] . "</a>";
    	}else{
    		echo "<a href=\"alter.php?referer=$referer&a=$account&x=$x&y=$y&t=0&i=1&r=1\">×</a>";
    	}
    	
    	echo "</td><td>";

    	if($invalid == 0 && $raid == 2){
    		echo "<a href=\"alter.php?referer=$referer&a=$account&x=$x&y=$y&t=3\">" . $chars[$raid] . "</a>";
    	}else{
    		echo "<a href=\"alter.php?referer=$referer&a=$account&x=$x&y=$y&t=0&i=1&r=2\">×</a>";
    	}
    	
    	echo "</td><td>";

    	if($invalid == 0 && $raid == 3){
    		echo "<a href=\"alter.php?referer=$referer&a=$account&x=$x&y=$y&t=3\">" . $chars[$raid] . "</a>";
    	}else{
    		echo "<a href=\"alter.php?referer=$referer&a=$account&x=$x&y=$y&t=0&i=1&r=3\">×</a>";
    	}
    	
    	echo "</td><td>";

    	if($invalid == 0 && $raid == 4){
    		echo "<a href=\"alter.php?referer=$referer&a=$account&x=$x&y=$y&t=3\">" . $chars[$raid] . "</a>";
    	}else{
    		echo "<a href=\"alter.php?referer=$referer&a=$account&x=$x&y=$y&t=0&i=1&r=4\">×</a>";
    	}
    	
    	echo "</td><td>";

    	if($invalid == 0 && $raid == 5){
    		echo "<a href=\"alter.php?referer=$referer&a=$account&x=$x&y=$y&t=3\">" . $chars[$raid] . "</a>";
    	}else{
    		echo "<a href=\"alter.php?referer=$referer&a=$account&x=$x&y=$y&t=0&i=1&r=5\">×</a>";
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
    	
   		echo "</td><td><a href=\"addmission.php?a=$account&x=$x&y=$y\">Add</a></td>";
    	echo "</td><td>$my_v_name</td><td>$score</td><td>";
    	
    	if($reports > 0)
    		echo '<a href="all-reports.php?u=' . $uid . '">Ｒ(' . $reports . ')';
    	echo "</td><td>$errmsg</td></tr>\n";
    	
    }
    
    echo "</table><br>";
    
    if($p > 0){
	    echo '<a href="viewpop.php?' . "a=$account&x=$ox&y=$oy&p=" . ($p - 1);
	   	echo "&limit=" . $limit;
	   	
	   	echo '">Prev</a>&nbsp;';
    }
    
    $p = $p + 1;
    echo '<a href="viewpop.php?' . "a=$account&x=$ox&y=$oy&p=" . $p;
   	echo "&limit=" . $limit;
	
   	echo '">Next</a><br><hr>';

	$time_end = microtime(true);
	echo round($time_end - $time_start, 5);


?>
</html>