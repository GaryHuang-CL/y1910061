<?php
	
	// ----------------------------------------------------------------------------
	// Main again
	// ----------------------------------------------------------------------------

	if(!array_key_exists('x', $_GET) || !array_key_exists('y', $_GET) || !array_key_exists('a', $_GET) ) {
		if(!array_key_exists('seq', $_GET)) die("No post.");
	}else{
		$x = $_GET['x'];
		$y = $_GET['y'];
		$account = $_GET['a'];
	}

	require_once("db.php");

	if(array_key_exists('h', $_GET) && array_key_exists('c', $_GET) && array_key_exists('r', $_GET)  && array_key_exists('v', $_GET)) {
		$hour = $_GET['h'];
		$clubs = $_GET['c'];
		$recursive = $_GET['r'];
		$village = $_GET['v'];
		$referer = $_GET['referer'];
		$ram = intval($_GET['ram']);

		$sql = " insert into mission(account, `village`, `x`, `y`, `hour`, `min_clubs`, `recursive`, `ram`) VALUES($account, $village, $x, $y, $hour, $clubs, $recursive, $ram)";

    	if(!mysql_query($sql)) die(mysql_error());
    	
		header("Location: $referer");

		exit();

	}else if(array_key_exists('d', $_GET)){

		$sql = " delete from mission where `seq` = " . intval($_GET['seq']);

    	if(!mysql_query($sql)) die(mysql_error());

		$referer = $_SERVER['HTTP_REFERER'];
		header("Location: $referer");

		exit();

	}

	$sql = " select id, name from villages where account = $account";
	$res = mysql_query($sql);
	if(!$res) die(mysql_error());

	echo "$x, $y<br>";
?>

<form method="GET" action="addmission.php">
<input type="hidden" name="x" value="<?php echo $x;?>">
<input type="hidden" name="y" value="<?php echo $y;?>">
<input type="hidden" name="a" value="<?php echo $account;?>">
<input type="hidden" name="referer" value="<?php echo $_SERVER['HTTP_REFERER'];?>">
	
Hour:
<select name="h"> 
<option value="0" >0</option>
<option value="1" >1</option>
<option value="2" >2</option>
<option value="3" >3</option>
<option value="4" >4</option>
<option value="5" >5</option>
<option value="6" >6</option>
<option value="7" >7</option>
<option value="8" >8</option>
<option value="9" >9</option>
<option value="10" >10</option>
<option value="11" >11</option>
<option value="12" >12</option>
<option value="13" >13</option>
<option value="14" >14</option>
<option value="15" >15</option>
<option value="16" >16</option>
<option value="17" >17</option>
<option value="18" >18</option>
<option value="19" >19</option>
<option value="20" >20</option>
<option value="21" >21</option>
<option value="22" >22</option>
<option value="23" >23</option>
</select>
<br>

Clubs:
<select name="c"> 
<option value="10" >10</option>
<option value="20" >20</option>
<option value="30" >30</option>
<option value="40" >40</option>
<option value="50" >50</option>
<option value="60" >60</option>
<option value="70" >70</option>
<option value="80" >80</option>
<option value="90" >90</option>
<option value="100" >100</option>
<option value="110" >110</option>
<option value="120" >120</option>
<option value="130" >130</option>
<option value="140" >140</option>
<option value="150" >150</option>
<option value="160" >160</option>
<option value="170" >170</option>
<option value="180" >180</option>
<option value="190" >190</option>
<option value="200" >200</option>
<option value="210" >210</option>
<option value="220" >220</option>
<option value="230" >230</option>
<option value="240" >240</option>
<option value="250" >250</option>
<option value="260" >260</option>
<option value="270" >270</option>
<option value="280" >280</option>
<option value="290" >290</option>
<option value="300" >300</option>
<option value="350" >350</option>
<option value="400" >400</option>
<option value="500" >500</option>
<option value="600" >600</option>
<option value="700" >700</option>
<option value="800" >800</option>
<option value="80000" >80000</option>
</select>
<br>

Village:
<select name="v">
<?php
	while($row = mysql_fetch_row($res)){
		$id = $row[0];
		$name = $row[1];
		
		echo '<option value="' . $id . '" >' . $name . '</option>';
	}
?>
</select>
<br>

Recursive:
<select name="r"> 
<option value="0" >No</option>
<option value="1" >Yes</option>
</select>
<br>

Ram (non-recruseive only):
<select name="ram"> 
<option value="0" >No</option>
<option value="1" >Yes</option>
</select>
<br>

<input type="submit" value="ok">
</form>