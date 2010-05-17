<?php
	if(!array_key_exists('s', $_GET)){
		include("list.php");
		exit();
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
 <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
 <meta name="keywords" content="cropfinder, crop, multicrop, megacrop, travian, finder, traviantool, tool, travian tool, travia, game, computer, games, online, online games, computer games"/>
</head>

<body>
<form action="index.php" method="get">
 <table>
 <tr>
 <td>
 伺服:</td>
 <td>
 <select name="s">
 <?php
include("db.php");

$sql = "select id, name from servers order by reverse(name)";
$res = mysql_query($sql);
if(!$res) die(mysql_error());
while($row = mysql_fetch_row($res)){
	echo '<option value="' . $row[0] . '" ';
	
	if(array_key_exists('s', $_GET) && $_GET['s'] == $row[0]){
		echo "selected";
	}
	
	echo '>' . $row[1] . '</option>';
}

	if(array_key_exists('x', $_REQUEST) && array_key_exists('y', $_REQUEST) && array_key_exists('t', $_REQUEST) && array_key_exists('b', $_REQUEST)){
		$rx = intval($_REQUEST['x']);
		$ry = intval($_REQUEST['y']);
		$t = intval($_REQUEST['t']);
		$b = intval($_REQUEST['b']);
	}else{
		$rx = 0;
		$ry = 0;
		$t = 15;
		$b = 75;
	}

 ?>
 </select>
 </td>
 </tr>
 <tr>
 <td>中心坐標 x:</td>
 <td><input type="text" name="x" value="<?php echo $rx;?>" size="4" maxlength="4"/></td>
 <td>y:</td>
 <td><input type="text" name="y" value="<?php echo $ry;?>" size="4" maxlength="4"/></td>
 </tr>
 <tr>
 <td>類型:</td>
 <td>
 <select name="t">
 <option value="15" <?php if($t==15) echo "selected"; ?>>15田</option>
 <option value="9" <?php if($t==9) echo "selected"; ?>> 9田</option>
 <option value="0" <?php if($t==0) echo "selected"; ?>> 9田+15田</option>
 </select>
 </td>
 <td>綠洲加成:</td>
 <td>
 <select name="b">
 <option value="0" <?php if($b==0) echo "selected"; ?>>0+%</option>
 <option value="25" <?php if($b==25) echo "selected"; ?>>25+%</option>
 <option value="50" <?php if($b==50) echo "selected"; ?>>50+%</option>
 <option value="75" <?php if($b==75) echo "selected"; ?>>75+%</option>
 <option value="100" <?php if($b==100) echo "selected"; ?>>100+%</option>
 <option value="125" <?php if($b==125) echo "selected"; ?>>125+%</option>
 <option value="150" <?php if($b==150) echo "selected"; ?>>150%</option>
 </select>
 </td>
 </tr>
 <tr>
 <td><input type="submit"/></td>
 </tr>
 </table>
 </form>

<?php
	if(array_key_exists('x', $_REQUEST) && array_key_exists('y', $_REQUEST) && array_key_exists('t', $_REQUEST) && array_key_exists('b', $_REQUEST) && array_key_exists('s', $_GET)){
		$rx = intval($_REQUEST['x']);
		$ry = intval($_REQUEST['y']);
		$t = intval($_REQUEST['t']);
		$s = intval($_GET['s']);
		$b = intval($_REQUEST['b']);
		
		$sql = "select name from servers where id = $s";
		$res = mysql_query($sql);
		if(!$res) die(mysql_error());
		$row = mysql_fetch_row($res);
		$server = $row[0];
		
		
		//$sql = "select x, y, crops, bonus, d, c, round(sqrt((x - $rx) * (x - $rx) + (y - $ry) * (y - $ry)), 1) as distance from crops where x <= $rx + $rd and x >= $rx - $rd and y <= $ry + $rd and y >= $ry - $rd and bonus >= $b and server = $s ";
		$sql = "select x, y, crops, bonus, d, c, round(sqrt((x - $rx) * (x - $rx) + (y - $ry) * (y - $ry)), 1) as distance from crops where bonus >= $b and server = $s ";
		
		if($t != 0){
			$sql .= " and crops = $t ";
		}
		$sql .= " order by distance limit 30";

		$res = mysql_query($sql);
		if(!$res) die(mysql_error());

		if(mysql_num_rows($res) == 0){
			echo "<p>沒有結果。";
		}else{

			echo "<p><table>";
			while($row = mysql_fetch_row($res)){
				$x = $row[0];
				$y = $row[1];
				$crops = $row[2];
				$bonus = $row[3];
				$d = $row[4];
				$c = $row[5];
				$distance = $row[6];
				
				echo "<tr><td><a href=\"http://$server/karte.php?d=$d&c=$c\">($x,$y)</a></td><td>$crops 田</td><td align=right>+$bonus%</td><td align=right>$distance</td></tr>\n";
			}
			
			echo "</table>";
		}
	}
?>

</body>
</html>

