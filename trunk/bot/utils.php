<?php
	// </div><div id="ltime">‰‰Z <b>17</b> ms<br>f•ŠíŠÔF <span id="tp1" class="b">13:47:25</span> </div>
	// </div><div id="ltime">Àsˆ—‘¬“x <b>8</b> ms<br>ƒT[ƒo[ŠÔF <span id="tp1" class="b">21:48:35</span> </div>
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
		// </form></p><div class="f10 e b">Šß‰Æ t21782146 ˆöˆá”½‹K‘¥§”í••½</div></div></div></div>
		// <div class="f10 e b">Šß‰Æ ?Ç—r ˆöˆá”½—VE‹K‘¥§”í••½</div>
		
		$ret = preg_match('/<div class="f10 e b">(.+?)<\/div>/', $result, $matches);
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
		// <td><b class="c5 f10">U&#25802;</b></td>

		// <td align="center" class="f10">İ</td>
		// <td class="f10"><span id=timer2>0:27:23</span> ¬</td>
		
		$url  = "http://$server/dorf1.php";
		
		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		
		$result = curl_exec ($ch);
		curl_close ($ch);

		$ret = detect_attack($result);
		
		if($ret == 0) return true;

		return false;

	}
	
	// return seconds remain
	function detect_attack($result)
	{
		$pos = strpos($result, '<img src="img/x.gif" class="att1"');
		
		if ($pos === false) return -1;

		echo "************ Being ATTACKED !!! ************\n";
		$ret = preg_match('/<td><span id="timer[0-9]">([0-9]+):([0-9]+):([0-9]+)<\/span>/', $result, $matches, 0, $pos);
		
		if($ret){
			$hour = intval($matches[1]);
			$minute = intval($matches[2]);
			$second = intval($matches[3]);
			
			$x = $hour * 3600 + $minute * 60 + $second;
			echo "$hour:$minute:$second remain. $x\n";
			return $x;
		}
		
		return -1;
	}
	
	function attack_mail($title)
	{
		$to  = 'dmwjgmt@docomo.ne.jp';

		// subject
		$subject= '=?UTF-8?B?' . base64_encode($title) . '?=';
		
		// Mail it
		mail($to, $subject, 'None');
	}
	
	// detect message
	function detect_message($result, $account, $message, $user, $server)
	{
		$none = (strpos($result, 'img/un/l/m2.gif') === false && strpos($result, 'img/un/l/m1.gif') === false);
		
		if($none && $message){
			$sql = "update accounts set message = 0 where id = $account";
			if(!mysql_query($sql)) die(mysql_error());
			return;
		}
		
		if($none === false && $message == 0){
			$sql = "update accounts set message = 1 where id = $account";
			if(!mysql_query($sql)) die(mysql_error());
			
			// attack_mail("M $user@$server");
		}
	}


	// return seconds remain
	function detect_oasis_attack($result)
	{
		$pos = strpos($result, '<img src="img/x.gif" class="att3"');
		
		if ($pos === false) return -1;

		echo "************ OASIS Being ATTACKED !!! ************\n";
		
		$ret = preg_match('/<td><span id="timer[0-9]">([0-9]+):([0-9]+):([0-9]+)<\/span>/', $result, $matches, 0, $pos);

		if($ret){
			$hour = intval($matches[1]);
			$minute = intval($matches[2]);
			$second = intval($matches[3]);
			
			$x = $hour * 3600 + $minute * 60 + $second;
			echo "$hour:$minute:$second remain. $x\n";
			return $x;
		}
		
		return -1;
	}


	function get_defence_oasis($result)
	{
		// <p class="b f16"><a href="karte.php?d=384819&c=b4"><span class="c0">è‹’’†ƒIƒAƒVƒX (-62|-80)</span></a></p>
		
		$regstr = '#<p class="b f16"><a href="(karte\.php\?d=[0-9]+&c=[a-z0-9]+)"><span class="c0">\S+ \(([-0-9]+)\|([-0-9]+)\)</span></a></p>#';
		
		if(!preg_match_all($regstr, $result, $matches, PREG_SET_ORDER)){
			echo "Can not find oasis.\n";
			return false;
		}

		$pos = strpos($result, $matches[0][0]);
		$res = array();
		
		foreach($matches as $match){
			$link_id = $match[1];
			$x = $match[2];
			$y = $match[3];
			
			$last_time_remain = 0;
			
			for($i = 0; $i < 30; $i++){
				
				// <td colspan="10" class="b"><a href="karte.php?d=386420&c=d6"><span class="c0">
				$target_str = '<td colspan="10" class="b"><a href="' . $link_id . '"><span class="c0">';
				
				$pos = strpos($result, $target_str, $pos + 1);
				
				if ($pos === false){
					//echo "Can not find oasis attack.\n";
					break;
				}
				
				// TIME REMAIN
				// <td width="50%">&nbsp; êŠF <span id=timer14>0:16:40</span> Œã</td>
				$regstr = '#<td width="50%">&nbsp; \S+ <span id=timer[0-9]+>([0-9]+):([0-9]+):([0-9]+)</span> \S+</td>#';
				if(!preg_match($regstr, $result, $match, 0, $pos)){
					echo "Can not find oasis attack time remain.\n";
					break;
				}
				
				$h = $match[1];
				$m = $match[2];
				$s = $match[3];
				
				$time_remain = $h * 3600 + $m * 60 + $s;
				
				// ignore this
				if($time_remain - $last_time_remain < 60){
					$last_time_remain = $time_remain;
					continue;
				}
				
				
				
				// ARRIVE TIME
				// <td width="50%">ŠÔF 15:44:45</span><span> </td></tr></table></td></table></p>
				$regstr = '#<td width="50%">\S+ ([0-9]+:[0-9]+:[0-9]+)</span><span> \S+</td></tr></table></td></table></p>#';
				if(!preg_match($regstr, $result, $match, 0, $pos)){
					echo "Can not find oasis attack arrive time.\n";
					break;
				}
				
				array_push($res, array($x, $y, $time_remain, $match[1]));
				$last_time_remain = $time_remain;
			}
		}
		
		return $res;
	}
	
	
	function calc_distance($x1, $y1, $x2, $y2)
	{
		return sqrt(($x1 - $x2) * ($x1 - $x2) + ($y1 - $y2) * ($y1 - $y2));
	}

	// sort by wait time index 4
	function cmp($a, $b)
	{
		if($a[4] == $b[4]) return 0;
		
		if($a[4] < $b[4]) return -1;
		
		return 1;
	}
	
	// $sec should < 60
	function minus_time_str($timestr, $sec)
	{
		list($hour, $minute, $second) = split(":", $timestr);
		
		if($hour == 0) $hour = 24;
		
		$t = $hour * 3600 + $minute * 60 + $second;

		assert($t > $sec);

		$t -= $sec;
		
		$hour = floor($t / 3600);
		
		$t -= $hour * 3600;
		
		$minute = floor($t / 60);
		
		$t -= $minute * 60;
		
		$second = $t;
		
		if($hour == 24) $hour = 0;
		
		return $hour . ":" . $minute . ":" . $second;
	}
	
	function auto_defence_oasis($home_x, $home_y, $defence_unit, $defence_unit_speed)
	{
		global $server;
		
		$url  = "http://$server/build.php?gid=16";
		
		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		$result = curl_exec ($ch);
		curl_close ($ch);
	
		$res = get_defence_oasis($result);

		print_r($res);
		
		if(empty($res)) return false;
		
		foreach($res as &$a){
			$x = $a[0];
			$y = $a[1];
			$remain = $a[2];
			$arrive = $a[3];
			
			$distance = calc_distance($home_x, $home_y, $x, $y);
			
			$need_time = 3600 * $distance / $defence_unit_speed;
			
			$wait_time = $remain - $need_time;
			
			array_push($a, $wait_time);
		}
		
		usort($res, "cmp");
		print_r($res);
				
		$last_wait_time = 0;
		
		foreach($res as $b){
			$x = $b[0];
			$y = $b[1];
			$arrive = $b[3];
			$wait_time = $b[4];
			
			//print_r($a);
			
			// too late
			if($wait_time < 0){
				echo "wait time < 0\n";
				continue;
			}
			
			// 20 minutes
			if($wait_time - $last_wait_time > 1200){
				echo "wait time - last wait time > 1200\n";
				break;
			}
			
			$arrive2 = minus_time_str($arrive, 30);
			
			echo "raid $x, $y $arrive2\n";
			raid($x, $y, array($defence_unit=>2), $arrive2);
			
			$last_wait_time = $wait_time;
		}
	}

?>
