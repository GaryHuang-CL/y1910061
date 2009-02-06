<?php
	
	// record self attack reports for farm suppose
	function record_report($id, $title)
	{
		$sql = "insert into reports(id, title) values($id, '$title')";
		if(!mysql_query($sql)) die(mysql_error());
	}

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
	
	// read self attack reports for farm suppose
	function read_report($id, $title)
	{
		global $server;
		global $user;
		
		$url = "http://$server/berichte.php?id=$id";
		echo $url . "\n";
		
		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		$result = curl_exec ($ch);
		curl_close ($ch);

		// <td colspan="10"><a href=spieler.php?uid=6431>Hömeless</a> 所有の <a href=karte.php?d=298467&c=80>新しい村</a></td>
		if(!preg_match_all('#<td colspan="1[01]"><a href="?spieler\.php\?uid=[0-9]+"?>([^<]+)</a>#', $result, $matches, PREG_SET_ORDER)){
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

		if(preg_match('#<tr><td>[^<]+?</td><td>([0-9]+)</td>.*?</tr>(<tr><td>.*?</tr>)#', $result, $match)){
			$soldiers = $match[1];
			$died_str = $match[2];
			
			$died = 0;
			if(preg_match('#<td>([0-9]+)</td>#', $died_str, $match)){
				$died = $match[1];
			}
			echo "$died dead.\n";
			
		}else{
			record_report($id, "【兵士未知】$title");
			return;
		}

		if($soldiers == $died || count($matches) < 2){
			// echo "all die.\n";
			// get target village name
			// -- zaútočil na German
			// β ひのがtown of tomaton＠UMAを攻撃しました
			
			$xy = "";
			
			$village_name = get_village_name_from_report_title($title);
			if($village_name){
				$village_name = mysql_escape_string($village_name);
				$sql = "select distinct x, y from populations where village_name = '$village_name'";
				
			    $res = mysql_query($sql);
			    if(!$res) die(mysql_error());
			    while($row = mysql_fetch_row($res))
			    {
			    	$x = $row[0];
			    	$y = $row[1];
			    	
			    	$sql = "update targets set invalid = 1, invalid_msg = '全滅' where x = $x and y = $y";
			    	if(!mysql_query($sql)) die(mysql_error());
			    	
			    	$xy = $xy . "($x,$y)";
			    }
			}
			
			// try remove this one from raid list?
			record_report($id, "【全滅】$title $xy");
			return;

		}
		
		$player = $matches[1][1];

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
		
		$village_name = get_village_name_from_report_title($title);
		if($village_name){
			$village_name = mysql_escape_string($village_name);
			$player = mysql_escape_string($player);
			
			$sql = "select distinct x, y from populations where village_name = '$village_name' and player_name = '$player'";
			
		    $res = mysql_query($sql);
		    if(!$res) die(mysql_error());

		    if(mysql_num_rows($res) == 0){
				$sql = "select distinct x, y from populations where player_name = '$player'";
			    $res = mysql_query($sql);
			    if(!$res) die(mysql_error());
		    }
		    
		    if(mysql_num_rows($res) == 1){
			    $row = mysql_fetch_row($res);
		    	$x = $row[0];
		    	$y = $row[1];
		    	
		    	$sql = "select score from targets where x = $x and y = $y";
			    $res = mysql_query($sql); if(!$res) die(mysql_error());
			    $row = mysql_fetch_row($res); 
			    if(!$row){
					record_report($id, "【レコード】$title");
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
			}else{
				record_report($id, "【村名重複】$title");
				return;
			}
		}else{
			record_report($id, "【村名】$title");
			return;
		}

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

	// we record all damage above 100
	function read_ally_report($id, $title)
	{
		global $server;
		global $user;
		/*
		if(strstr($title, '偵察')){
			echo "scout, ignore.\n";
			return;
		}*/
		
		$url = "http://$server/berichte.php?id=$id";
		echo $url . "\n";
		
		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		$result = curl_exec ($ch);
		curl_close ($ch);

		if(!preg_match_all('#<td colspan="1[01]"><a href="?spieler\.php\?uid=[0-9]+"?>([^<]+)</a>#', $result, $matches, PREG_SET_ORDER)){
			echo "FAILED: can not read report well $id\n";
			return;
		}
		
		if(count($matches) < 2){
			echo "all die.\n";
			// try remove this one from raid list?
			//record_report($id, $title);
			return;
		}
		
		// <td class="c">0</td>
		// <td>1</td>
		// <tr><td>死傷</td><td>5</td><td class="c">0</td><td>6</td><td class="c">0</td><td class="c">0</td><td class="c">0</td><td class="c">0</td><td class="c">0</td><td class="c">0</td><td class="c">0</td><td class="c">0</td></tr>
		if(preg_match('#<td>[0-9]+</td>.*?</tr>(.+?)</tr>#', $result, $match)){
			
			if(!preg_match_all('#<td>([0-9]+)</td>#', $match[1], $matches, PREG_SET_ORDER)){
				echo "Noone died.\n";
				return;
			}
			
			echo $match[1] . "\n";
			echo "soldier died.\n";
			
			$total = 0;
			foreach($matches as $match){
				$num = $match[1];
				$total += $num;
			}

			if($total > 40){
				mail_report($id, $title, $result);
				return;
			}
		}else{
			echo "failed to match.\n";
		}

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
		
		if(!preg_match_all('#<td class="s7"><a href="berichte\.php\?id=([0-9]+)">([^<]+)</a></td>#', $result, $matches, PREG_SET_ORDER)){
			echo "failed to get ally reports list.\n";
			return;
		}

		$maxid = $last_report;
		
		foreach($matches as $match){
			$id = $match[1];
			$title = $match[2];
			
			if($id <= $maxid) continue;
			
			read_ally_report($id, $title);
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