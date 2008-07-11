<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
 <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
 <meta name="keywords" content="cropfinder, crop, multicrop, megacrop, travian, finder, traviantool, tool, travian tool, travia, game, computer, games, online, online games, computer games"/>
</head>

<body>

<?php
include("db.php");

$country = array("hk" => "香港", "tw" => "台灣", "jp" => "日本");
$servers = array();
$new_servers = array();

foreach($country as $k => $v){
	$servers[$k] = array();
}

$sql = "select id, name, new from servers order by id";

$res = mysql_query($sql);
if(!$res) die(mysql_error());

while($row = mysql_fetch_row($res)){
	$id = $row[0];
	$name = $row[1];
	$new = $row[2];
	
	$k = substr($name, -2);
	
	$servers[$k][$name] = $id;
	
	if($new){
		array_push($new_servers, $id);
	}
}

foreach($servers as $k => $v){
	if(count($v) > 0){
		ksort($v);
		
		echo "<p><h4>" . $country[$k] . "</h4><ul>\n";
		
		foreach($v as $name => $id){
			if(in_array($id, $new_servers)){
				$name = "<b>$name</b>";
			}
			echo '<li><a href="index.php?s=' . $id . '">' . $name . "</a>\n";
		}
		
		echo "</ul>\n";
	}
}

?>

</body>
</html>

