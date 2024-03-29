<?php
	
	function get_report_line($result, $type)
	{
		if(preg_match('#<tr><th>' . $type . '</th>(.*?)</td></tr>#', $result, $match)){
			
			$line = $match[1];
			$ret = explode ('</td>', $line);
			
			if(count($ret) < 9){
				echo "get_report_line: element too few `$line`\n";
			}
			
			foreach($ret as $i=>$x){
				$ret[$i] = strip_tags($x);
				if(!is_numeric($ret[$i])){
					echo "get_report_line: not numeric [$i] $x\n";
					return false;
				}
			}
			
			return $ret;
		}else{
			echo "get_report_line: can not match `$type`\n";
			return false;
		}
	}
	
	// record self attack reports for farm suppose
	function record_report($id, $title)
	{
		global $account;
		
		$sql = "replace into reports(account, id, title) values($account, $id, '$title')";
		if(!mysql_query($sql)) die(mysql_error());
	}

/*
	function get_village_name_from_report_title($title)
	{
		// β ひのがtown of tomaton＠UMAを攻撃しました
		// 0000がにゃんこ村を攻撃しました
		// if(preg_match('#zaútočil na (.+)$#', $title, $match)){
		// crops.no-ip.org 攻擊 崖 上 的 波 兒 村 莊
		if(preg_match('#が(.+)を攻撃しました$#', $title, $match)){
			return $match[1];
		}else if(preg_match('# 攻擊 (.+)$#', $title, $match)){
			return $match[1];
		}else{
			return false;
		}
	}
*/
	
	// read self attack reports for farm suppose
	function read_report($id, $title)
	{
		global $server;
		global $user;
		global $report_str;
		global $account;
		
		// x_world will be get at 0:30
		// run at hostjava.net
		$tblname = "x_world_" . str_replace(".", "_", $server) . "_" . date('ymd', time() - 3600 * 4);
		
		$url = "http://$server/berichte.php?id=$id";
		echo $url . "\n";
		
		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		$result = curl_exec ($ch);
		curl_close ($ch);

		// <td colspan="10"><a href=spieler.php?uid=6431>Hömeless</a> 所有の <a href=karte.php?d=298467&c=80>新しい村</a></td>
		if(!preg_match_all('#<a href="?spieler\.php\?uid=[0-9]+"?>([^<]+)</a>#', $result, $matches, PREG_SET_ORDER)){
			record_report($id, "【プレイヤー不明】$title");
			return;
		}

		if(count($matches) < 3){
			record_report($id, "【プレイヤー不足】$title");
			return;
		}

		// attacked !
		if($matches[1][1] != $user){
			// echo "attacked.\n";
			record_report($id, "【防衛】$title");
			return;
		}

		$player = $matches[2][1];

		if(!preg_match_all('#<a href="karte\.php\?d=([0-9]+)#', $result, $matches, PREG_SET_ORDER)){
			record_report($id, "【村不明】$title");
			return;
		}

		if(count($matches) < 2){
			record_report($id, "【村不足】$title");
			return;
		}

		$village_id = $matches[1][1];

		$sql = "select x, y from $tblname where id = $village_id";
		$res = mysql_query($sql);
		if(!$res){
			record_report($id, "【X-WORLD】$title $village_id $tblname");
			return;
		}

	    $row = mysql_fetch_row($res);

		if(!$res){
			record_report($id, "【X-WORLD-2】$title $village_id");
			return;
		}
	    
	    $x = $row[0];
	    $y = $row[1];


		// $soldiers = get_report_line($result, '兵士');
		$soldiers = get_report_line($result, $report_str[0]);
		if(!$soldiers){
			record_report($id, "【兵士情報不明】$title");
			return;
		}

		// $died = get_report_line($result, '死傷');
		$died = get_report_line($result, $report_str[1]);
		
		// $captured = get_report_line($result, '捕虜');
		$captured = get_report_line($result, $report_str[2]);
		
		if(!$died && !$captured){
			record_report($id, "【死傷捕虜不明】$title");
			return;
		}

		if(!$died){
			$died = $captured;
		}else if($captured){
			if(count($died) != count($captured)){
				record_report($id, "【死傷捕虜数合わない】$title");
				return;
			}
			
			for($i = 0; $i < count($died); $i++){
				$died[$i] += $captured[$i];
			}
		}

    	$died_total = array_sum($died);
    	if($captured){
    		$died_total -= array_sum($captured);
    	}

		if(array_sum($soldiers) == array_sum($died)){
			    	
			// remove this one from raid list?
	    	$sql = "update targets set invalid = 1, invalid_msg = '全滅' where x = $x and y = $y";
	    	if(!mysql_query($sql)) die(mysql_error());
	    	
			record_report($id, "【全滅】$title ($x,$y)");
			return;
		}
		

		// <img class="res" src="img/un/r/1.gif">44 
		// <img class="res" src="img/un/r/2.gif">44 
		// <img class="res" src="img/un/r/3.gif">44 
		// <img class="res" src="img/un/r/4.gif">49 
		// <img class="r1" src="img/x.gif" alt="木材" title="木材" />871
		if(!preg_match_all('#<img class="r[1-4]" src="img/x.gif" alt="[^"]+" title="[^"]+" />([0-9]+)#', $result, $matches, PREG_SET_ORDER)){
			echo "FAILED: can not read report well $id resources \n";
			record_report($id, "【資源未知】$title");
			return;
		}

		$total = 0;
		foreach($matches as $match){
			$total += $match[1];
		}
		
		
		$max_raid = (($soldiers[0] - $died[0]) * 60 + ($soldiers[2] - $died[2]) * 50 + ($soldiers[5] - $died[5]) * 80);
		$score = round(($total * 100) / $max_raid);


		echo "DEBUG: $total, $max_raid " . "\n";
		
    	$sql = "select score from targets where x = $x and y = $y";
	    $res = mysql_query($sql); if(!$res) die(mysql_error());
	    $row = mysql_fetch_row($res); 
	    if(!$row){
			record_report($id, "【ファーム対象外】$title");
			return;
	    }
	    
		$old_score = $row[0];
		if(strlen($old_score) == 0){
			$scores = array();
		}else{
    		$scores = explode('|', $old_score);
    	}
    	
    	array_push($scores, $score);

		if(count($scores) > 10){
			array_shift($scores);
		}
		
		$new_score = implode('|', $scores);
		$avg_score = calc_score($new_score);
			
	    if($died_total > 0){
	    	echo "$died_total died.\n";
    		$sql = "update targets set `timestamp` = date_add(now(),  interval 2 day), `score` = '$new_score', `avg_score` = $avg_score, invalid_msg = '死傷' where account = $account and x = $x and y = $y";
    	}else if($total == $max_raid){
			echo "reraid...($x,$y) $total\n";
	    	$sql = "update targets set `timestamp` = date_sub(now(),  interval 3 day), `score` = '$new_score', `avg_score` = $avg_score where account = $account and x = $x and y = $y";
	    }else if($total == 0 && count($scores) >= 3 && $scores[count($scores) - 2] == 0 && $scores[count($scores) - 3] == 0){
	    	echo "raid nothing 3 times ... ($x,$y) \n";
	    	$sql = "update targets set invalid = 1, invalid_msg = 'ゼロ３回', `score` = '$new_score', `avg_score` = $avg_score where account = $account and x = $x and y = $y";
	    }else if($total == 0){
	    	echo "raid nothing ... ($x,$y) \n";
	    	$sql = "update targets set `timestamp` = date_add(now(),  interval 1 day), `score` = '$new_score', `avg_score` = $avg_score where account = $account and x = $x and y = $y";
	    }else{
	    	$sql = "update targets set `timestamp` = `timestamp`, `score` = '$new_score', `avg_score` = $avg_score where account = $account and x = $x and y = $y";
	    }
	    
    	if(!mysql_query($sql)) die(mysql_error());

		// <td class="c">0</td>
		// <td>1</td>
		if($died_total > 0){
			record_report($id, "【死傷】$title (" . array_sum($died) . ")");
		}else if($total == 0){
			echo "raid zero.\n";
			record_report($id, "【無駄】$title");
		}

	}
	
	// read self attack reports for farm suppose
	function read_self_attack_reports()
	{
		// TEST
		/*
		$id = "4436942";
		if(!mysql_query("delete from reports where id = $id")) die(mysql_error());
		read_report($id, "- 攻擊 村莊");


		return;
		*/
		
		global $server;

		$s = 0;
		while(true){
			
			$url = "http://$server/berichte.php?s=$s&t=3";
			echo $url . "\n";
			
			$ch = my_curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			$result = curl_exec ($ch);
			curl_close ($ch);
			
			// <td class="sub"><a href="berichte.php?id=26188365">? 攻擊 我已經蓋陷阱了＠＠</a> （新）</td>
			// <div><a href="berichte.php?id=34902718">C03 attacks g rudios`s village</a> (new)</div>
			// <a href="berichte.php?id=34909013">C03 attacks KamE_kAze</a> </div>

			if(!preg_match_all('#<a href="berichte\.php\?id=([0-9]+)">([^<]+)</a> [^<]+</div>#', $result, $matches, PREG_SET_ORDER))
				break;
			
			foreach($matches as $match){
				$id = $match[1];
				$title = $match[2];
				read_report($id, $title);
				
			}
			
			$s += 10;
		}
	}
	
	/*
	function mail_report($id, $title, $result)
	{
		global $server;
		
		if(!preg_match('#(<table cellspacing="1" cellpadding="2" class="tbg">.+</table>)</p></div></div>#s', $result, $match)){
			echo "mail report failed.\n";
			return;
		}
		
		$to  = 'inblackhole.japan-s1@blogger.com';

		// subject
		$subject= '=?UTF-8?B?' . base64_encode($title) . '?=';
		
		// message
		$message = $match[1];

		// links
		$message = str_replace('<a href="', '<a href="http://' . $server . '/', $message);
		$message = str_replace(' src="', ' src="http://' . $server . '/', $message);
					
		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
		
		// Additional headers
		$headers .= 'To: Blogger <inblackhole.japan-s1@blogger.com>' . "\r\n";
		$headers .= 'From:  inblackhole <inblackhole@gmail.com>' . "\r\n";

		// Mail it
		mail($to, $subject, $message, $headers);

	}*/

	function read_ally_report($id, $title, $ally1, $ally2)
	{
		global $server;
		global $user;
		
		$url = "http://$server/berichte.php?id=$id";
		echo $url . "\n";
		
		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		$result = curl_exec ($ch);
		curl_close ($ch);

		if(!preg_match('#(<table cellspacing="1" cellpadding="2" class="tbg">.+</table>)</p></div></div>#s', $result, $match)){
			echo "read ally report contents failed.\n";
			return;
		}

		$content = mysql_escape_string($match[1]);
		
		// datetime
		//<td class="s7">日付： 09/02/09 時間： 14:34:16</span><span> 時</td>
		if(!preg_match('#<td class="s7">日付： ([0-9]+/[0-9]+/[0-9]+) 時間： ([0-9]+:[0-9]+:[0-9]+)</span><span> 時</td>#', $result, $match)){
			echo "FAILED: can not read ally report datetime $id \n";
			return;
		}
		
		$datetime = $match[1] . " " . $match[2];
		
		// <td colspan="10"><a href=spieler.php?uid=6431>Hömeless</a> 所有の <a href=karte.php?d=298467&c=80>新しい村</a></td>
		if(!preg_match_all('#<a href="?spieler\.php\?uid=([0-9]+)"?>([^<]+)</a>#', $result, $matches, PREG_SET_ORDER)){
			echo "FAILED: can not read ally report well $id \n";
			return;
		}
		
		if(count($matches) < 3){
			echo "FAILED: can not read ally report well $id ..\n";
			return;
		}

		// <td colspan="10"><a href=spieler.php?uid=6431>Hömeless</a> 所有の <a href=karte.php?d=298467&c=80>新しい村</a></td>
		if(!preg_match_all('#<a href=karte\.php\?d=([0-9]+)&c=([a-z0-9]{2})>([^<]+)</a>#', $result, $matches3, PREG_SET_ORDER)){
			echo "FAILED: can not read ally report well $id ,,\n";
			return;
		}

		if(count($matches3) < 2){
			echo "FAILED: can not read ally report well $id ,,,,\n";
			return;
		}

		$title = mysql_escape_string($title);
		$ally1 = mysql_escape_string($ally1);
		$ally2 = mysql_escape_string($ally2);

		$attack_id = $matches[1][1];
		$attacker = mysql_escape_string($matches[1][2]);
		$attack_village_id = $matches3[0][1];
		$attack_village_id_c = $matches3[0][2];
		$attack_village = mysql_escape_string($matches3[0][3]);
		
		$defend_id = $matches[2][1];
		$defender = mysql_escape_string($matches[2][2]);
		$defend_village_id = $matches3[1][1];
		$defend_village_id_c = $matches3[1][2];
		$defend_village = mysql_escape_string($matches3[1][3]);
		
		// <tr><td>兵士</td><td class="c">0</td><td class="c">0</td><td class="c">0</td><td>2</td><td class="c">0</td><td class="c">0</td><td class="c">0</td><td class="c">0</td><td class="c">0</td><td class="c">0</td></tr>
		if(!preg_match_all('#<tr><td>兵士</td>(.+?)</tr>#', $result, $matches, PREG_SET_ORDER)){
			echo "FAILED: can not read ally report well $id ....\n";
			return;
		}

		$attack_power_str = $matches[0][1];
		$attack_power = 0;
		
		if(!preg_match_all('#<td>([0-9]+)</td>#', $attack_power_str, $matches2, PREG_SET_ORDER)){
			echo "FAILED: can not read ally report well $id ......\n";
		}

		for($i = 0; $i < count($matches2); $i++){
			$attack_power += $matches2[$i][1];
		}

		if(count($match) < 2){
			$defend_power = 9999999;
		}else{
			$defend_power = 0;
			
			for($i = 1; $i < count($matches); $i++){
				$defend_power_str = $matches[$i][1];
				
				if(!preg_match_all('#<td>([0-9]+)</td>#', $defend_power_str, $matches2, PREG_SET_ORDER)){
					continue;
				}

				for($j = 0; $j < count($matches2); $j++){
					$defend_power += $matches2[$j][1];
				}
			}
		}
		
		
		$sql = "replace into ally_reports(id, attack_uid, attacker, attack_village, attack_village_id, attack_ally, attack_power, defend_uid, defender, defend_village, defend_village_id, defend_ally, defend_power, datetime, content, title, attack_village_id_c, defend_village_id_c) " .
		       " values($id, $attack_id, '$attacker', '$attack_village', $attack_village_id, '$ally1', $attack_power, $defend_id, '$defender', '$defend_village', $defend_village_id, '$ally2', $defend_power, '$datetime', '$content', '$title', '$attack_village_id_c', '$defend_village_id_c')";
		if(!mysql_query($sql)) die(mysql_error());

	}

	/*
	http://s1.travian.jp/allianz.php?s=3


	<td class="s7"><a href="http://s1.travian.jp/berichte.php?id=8193920">sibahuaU܂</a></td>


	-- reports ---
  	id  	int(10)
	title 	varbinary(255)
	read 	int(11)
	
	*/

	function read_ally_reports()
	{
		global $server;
		global $last_report;
		global $account;
		
		$url = "http://$server/allianz.php?s=3";
		echo $url . "\n";
		
		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		$result = curl_exec ($ch);
		curl_close ($ch);
		
		if(!preg_match_all('#<td class="s7"><a href="berichte\.php\?id=([0-9]+)">([^<]+)</a></td>\s+<td class="c f8">(\S*) - (\S*)</td>#', $result, $matches, PREG_SET_ORDER)){
			echo "failed to get ally reports list.\n";
			return;
		}

		$maxid = $last_report;
		
		foreach($matches as $match){
			$id = $match[1];
			$title = $match[2];
			$ally1 = $match[3];
			$ally2 = $match[4];
			
			if($id <= $maxid) continue;
			
			read_ally_report($id, $title, $ally1, $ally2);
		}

		$id = $matches[0][1];
		$title = $matches[0][2];
		
		if($id > $maxid){
			$sql = "update accounts set last_report = $id where id = $account";
			if(!mysql_query($sql)) die(mysql_error());
		}
	}

	// <td class="nbr"><span>&#8226;</span>&nbsp; <a href="?newdid=74731&s=0&t=2">新しい村</a></td><td class="right"><table class="dtbl" cellspacing="0" cellpadding="0">
	// <td class="nbr"><span class="c2">&#8226;</span>&nbsp; <a href="?newdid=70637&s=0&t=2" class="active_vl">ö</a></td><td class="right"><table class="dtbl" cellspacing="0" cellpadding="0">
	function get_all_village_names($result)
	{
		$all = array();
		// <td class="nbr"><span>&#8226;</span>&nbsp; <a href="http://speed.travian.hk/dorf1.php?newdid=91866">Base</a></td>
		// <td class="nbr"><span class="c2">&#8226;</span>&nbsp; <a href="" class="active_vl">Home</a></td>
		if(preg_match_all('/<td class="nbr">.+?newdid=([0-9]+)&s=[0-9]+&t=2"[^>]*>(.+?)<\/a><\/td>/', $result, $matches, PREG_SET_ORDER)){
			foreach ($matches as $val) {
				array_push($all, $val[2]);
			}
		}

		return $all;
	}
	
	// read trade reports and delete all selfish trade reports
	function delete_self_trade_reports()
	{
		global $server;
		
		$village_names = array();
		
		for($s = 0; $s <= 10; $s = $s + 10){
			$url = "http://$server/berichte.php?s=$s&t=2";
			echo $url . "\n";
			
			$ch = my_curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			$result = curl_exec ($ch);
			curl_close ($ch);

			if(empty($village_names)){
				$village_names = get_all_village_names($result);
				
				// print_r($village_names);
			}
			
			for($i = 0; $i < 10; $i++){
				// <td class="s7"><a href="berichte.php?id=605449">öが新しい村に供給しました</a> （新）</td>
				if(!preg_match_all('#<td class="s7"><a href="berichte\.php\?id=([0-9]+)">([^<]+)が([^<]+)に供給しました</a> [^<]+</td>#', $result, $matches, PREG_SET_ORDER))
					break;

				$targets = array();
				foreach($matches as $match){
					$id = $match[1];
					$from = $match[2];
					$to = $match[3];
					
					if(in_array($from, $village_names) && in_array($to, $village_names)){
						// <td width="22"><input type="Checkbox" name="n1" value="605561"></td>
						if(preg_match('#<td width="22"><input type="Checkbox" name="(n[0-9]+)" value="' . $id .  '"></td>#', $result, $match)){
							$targets[$id] = $match[1];
						}else{
							echo "delete_self_trade_reports : failed to get post var name. $id\n";
						}
					}
				}
				
				if(!empty($targets)){
					// t=2&n1=605561&del=%E6%B6%88%E5%8E%BB
					// <input class="std" name="del" type="Submit" value="消去">
					$referer = $url;
					$url = "http://$server/berichte.php";

					$postfields = 't=2&';

					foreach($targets as $id=>$pname){
						$postfields = $postfields . $pname . '=' . $id . '&';
					}

					$postfields .= 'del=%E6%B6%88%E5%8E%BB';
					echo $postfields . "\n";
					
					$ch = my_curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
					curl_setopt($ch, CURLOPT_REFERER, $referer);

					$result = curl_exec ($ch);
					curl_close ($ch);
				}else{
					break;
				}
			}
		}
	}
?>
