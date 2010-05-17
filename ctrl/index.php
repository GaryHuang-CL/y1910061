<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
</head>
<?php
	
	// ----------------------------------------------------------------------------
	// Main added directly to remote svn repo
	// ----------------------------------------------------------------------------

	require_once("db.php");
	
	$combo = array();
	$combo[0] = "";
	
	$sql = " select id, name from villages ";
    $res = mysql_query($sql);
    if(!$res) die(mysql_error());
    while($row = mysql_fetch_row($res))
    {
    	$id = $row[0];
    	$name = $row[1];
    	
    	$combo[$id] = $name;
    }
    
    $sql = " select a.id, a.name, IFNULL(b.num, 0), a.auto_transfer from `villages` as a " . 
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
    	$auto_transfer = $row[3];
    	
    	echo '<tr><td valign=top><a href="village.php?id=' . $id . '">' . $name . ' (' . $num . ')</a> → </td><td>';
    	
    	echo '<form method="GET" action="auto_trans.php">';
    	echo '<input type="hidden" name="v" value="' . $id . '">';
    	echo '<select name="a" onchange="this.form.submit();">';
    	
    	foreach($combo as $combo_value => $combo_name){
    		$selected = "";
    		if($combo_value == $auto_transfer) $selected = " selected ";
    		
    		echo '<option value="' . $combo_value . '" ' . $selected . '>' . $combo_name . '</option>';
    	}
    	
    	echo "</select></form>\n";
    	echo "</tr>\n";
    }
    echo "</table>";

?>
