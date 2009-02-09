<?php
	
	// record self attack reports for farm suppose
	function record_report($id, $title)
	{
		$sql = "insert into reports(id, title) values($id, '$title')";
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
		
		// x_world will be get at 0:30
		$tblname = "x_world_" . str_replace(".", "_", $server) . "_" . date('ymd', time() - 3600);
		
		$url = "http://$server/berichte.php?id=$id";
		echo $url . "\n";
		
		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		$result = curl_exec ($ch);
		curl_close ($ch);

		// <td colspan="10"><a href=spieler.php?uid=6431>Hömeless</a> 所有の <a href=karte.php?d=298467&c=80>新しい村</a></td>
		if(!preg_match_all('#<td colspan="1[01]"><a href="?spieler\.php\?uid=[0-9]+"?>([^<]+)</a>[^<]+<a href=karte\.php\?d=([0-9]+)&c=[a-z0-9]{2}>#', $result, $matches, PREG_SET_ORDER)){
			echo "FAILED: can not read report well $id\n";
			record_report($id, "【未知】$title");
			return;
		}

		// attacked !
		if($matches[0][1] != $user){
			// echo "attacked.\n";
			record_report($id, "【防衛】$title");
			return;
		}

		if(count($matches) < 2){
			record_report($id, "【防衛者情報不明】$title");
			return;
		}

		$player = $matches[1][1];
		$village_id = $matches[1][2];

		$sql = "select x, y from $tblname where id = $village_id";
		$res = mysql_query($sql);
		if(!$res){
			record_report($id, "【X-WORLD】$title");
			return;
		}

	    $row = mysql_fetch_row($res);

		if(!$res){
			record_report($id, "【X-WORLD-2】$title");
			return;
		}
	    
	    $x = $row[0];
	    $y = $row[1];

		if(preg_match('#<tr><td>[^<]+?</td><td>([0-9]+)</td>.*?</tr>(<tr><td>.*?</tr>)#', $result, $match)){
			$soldiers = $match[1];
			$died_str = $match[2];
			
			$died = 0;
			if(preg_match('#<td>([0-9]+)</td>#', $died_str, $match)){
				$died = $match[1];
			}
			echo "$died dead.\n";
			
		}else{
			record_report($id, "【クラブ以外あり】$title");
			return;
		}

		if($soldiers == $died){
			    	
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
		if(!preg_match_all('#<img class="res" src=".+?img/un/r/[1-4]\.gif">([0-9]+)#', $result, $matches, PREG_SET_ORDER)){
			echo "FAILED: can not read report well $id resources \n";
			record_report($id, "【資源未知】$title");
			return;
		}

		$total = 0;
		foreach($matches as $match){
			$total += $match[1];
		}
		
		$score = round(($total * 100) / (($soldiers - $died) * 60));
		
    	$sql = "select score from targets where x = $x and y = $y";
	    $res = mysql_query($sql); if(!$res) die(mysql_error());
	    $row = mysql_fetch_row($res); 
	    if(!$row){
			record_report($id, "【ファーム対象外】$title");
			return;
	    }
	    
		$old_score = $row[0];
		if(empty($old_score)){
			$scores = array();
		}else{
    		$scores = explode('|', $old_score);
    	}
    	
    	array_push($scores, $score);

		if(count($scores) > 5){
			array_shift($scores);
		}
		
		$new_score = implode('|', $scores);
		
    	if($total == $soldiers * 60){
			echo "reraid...($x,$y) $soldiers $total\n";
	    	$sql = "update targets set `timestamp` = date_sub(now(),  interval 1 day), `score` = '$new_score' where x = $x and y = $y";
	    }else{
	    	$sql = "update targets set `timestamp` = `timestamp`, `score` = '$new_score' where x = $x and y = $y";
	    }
	    
    	if(!mysql_query($sql)) die(mysql_error());

		// <td class="c">0</td>
		// <td>1</td>
		if($died > 0){

			record_report($id, "【死傷】$title ($died)");
			
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
		$id = "9464522";
		if(!mysql_query("delete from reports where id = $id")) die(mysql_error());
		read_report($id, "ホームがふひひを攻撃しました");
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
			
			if(!preg_match_all('#<td class="s7"><a href="berichte\.php\?id=([0-9]+)">([^<]+)</a> [^<]+</td>#', $result, $matches, PREG_SET_ORDER))
				break;
			
			foreach($matches as $match){
				$id = $match[1];
				$title = $match[2];
				read_report($id, $title);
				
			}
			
			$s += 10;
		}
	}
	
	
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

	}

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
		if(!preg_match_all('#<td colspan="1[01]"><a href="?spieler\.php\?uid=([0-9]+)"?>([^<]+)</a>[^<]+<a href=karte\.php\?d=([0-9]+)&c=([a-z0-9]{2})>([^<]+)</a>#', $result, $matches, PREG_SET_ORDER)){
			echo "FAILED: can not read ally report well $id \n";
			return;
		}
		
		if(count($matches) < 2){
			echo "FAILED: can not read ally report well $id ..\n";
			return;
		}
		
		$title = mysql_escape_string($title);
		$ally1 = mysql_escape_string($ally1);
		$ally2 = mysql_escape_string($ally2);

		$attack_id = $matches[0][1];
		$attacker = mysql_escape_string($matches[0][2]);
		$attack_village_id = $matches[0][3];
		$attack_village_id_c = $matches[0][4];
		$attack_village = mysql_escape_string($matches[0][5]);
		
		$defend_id = $matches[1][1];
		$defender = mysql_escape_string($matches[1][2]);
		$defend_village_id = $matches[1][3];
		$defend_village_id_c = $matches[1][4];
		$defend_village = mysql_escape_string($matches[1][5]);
		
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

				for($j = 0; $j < count($matches2); $i++){
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
