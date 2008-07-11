<?php
	// </div><div id="ltime">演算 <b>17</b> ms<br>伺服器時間： <span id="tp1" class="b">13:47:25</span> </div>
	// </div><div id="ltime">実行処理速度 <b>8</b> ms<br>サーバー時間： <span id="tp1" class="b">21:48:35</span> </div>
	function get_server_hour($result)
	{
		if(!preg_match('/<span id="tp1" class="b">([0-9]+):[0-9]+:[0-9]+<\/span>/', 
			$result, $matches)){
			die("Error: get server hour.");
		}
		
		return intval($matches[1]);
	}
	
	function handle_bad_target($result, $target_x, $target_y)
	{
		// try to find out error message
		// </form></p><div class="f10 e b">玩家 t21782146 因違反規則而被封鎖</div></div></div></div>
		
		$ret = preg_match('/<div class="f10 e b">(.+?)<\/div><\/div><\/div><\/div>/', $result, $matches);
		if(!$ret){
			$invalid_msg = "Unknown";
		}else{
			$invalid_msg = $matches[1];
		}
		
		echo $invalid_msg . "\n";
		
		$sql = "update `targets` set `invalid` = 1, `invalid_msg` = '" .
		        mysql_real_escape_string($invalid_msg) . 
		        "' where x = " . $target_x . " and y = " . $target_y;
		
		if(!mysql_query($sql)) die(mysql_error());
		
		$sql = "delete from `mission` where x = " . $target_x . " and y = " . $target_y;
		if(!mysql_query($sql)) die(mysql_error());
		
	}
	
	
	function being_attacked()
	{
		global $server;
		
		// <td><b class="c5 f10" align="right">&raquo; 1</b></td>
		// <td><b class="c5 f10">攻&#25802;</b></td>

		// <td align="center" class="f10">在</td>
		// <td class="f10"><span id=timer2>0:27:23</span> 小時</td>
		
		$url  = "http://$server/dorf1.php";
		
		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		
		$result = curl_exec ($ch);
		curl_close ($ch);

		$pos = strpos($result, '<td><b class="c5 f10" align="right">&raquo;');
		
		if ($pos === false) return false;

		echo "************ Being ATTACKED !!! ************\n";
		
		$ret = preg_match('/<td class="f10"><span id=timer[0-9]>([0-9]+):[0-9]+:[0-9]+<\/span>/', $result, $matches, 0, $pos);
		
		if($ret){
			echo intval($matches[1]) . " hours remain. \n";
			if(intval($matches[1]) <= 1) return true;
		}
		return false;
		

	}
?>
