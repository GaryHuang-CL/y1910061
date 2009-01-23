<?php
	function calc_transfer_amount_newbie($carts, $amount_per_cart, $r1, $r2, $r3, $r4, $result)
	{
		if($carts == 0) return false;

		$r1 = max(0, $r1);
		$r2 = max(0, $r2);
		$r3 = max(0, $r3);
		$r4 = max(0, $r4);

		$curr_res = get_res_info($result);

		$r1 = min($curr_res[0], $r1);
		$r2 = min($curr_res[1], $r2);
		$r3 = min($curr_res[2], $r3);
		$r4 = min($curr_res[3], $r4);

		$total_capacity = $carts * $amount_per_cart;
		
		// enough carts, let's go 
		if($r1+$r2+$r3+$r4 < $total_capacity)
			return array($r1, $r2, $r3, $r4);

		$ratio = $total_capacity / ($r1+$r2+$r3+$r4);
		// echo "Ratio : $ratio ... \n";
		
		// fill it
		$t1 = min($r1, round($r1 * $ratio));
		$t2 = min($r2, round($r2 * $ratio));
		$t3 = min($r3, round($r3 * $ratio));
		$t4 = min($r4, $total_capacity - $t1 - $t2 - $t3);

		return array($t1, $t2, $t3, $t4);

	}

	// calculate real amount based on request amount and carts number
	function calc_transfer_amount($carts, $amount_per_cart, $r1, $r2, $r3, $r4)
	{
		if($carts == 0) return false;
		
		if($r1+$r2+$r3+$r4 < $amount_per_cart) return false;
		
		$carts = min(floor(($r1 + $r2 + $r3 + $r4) / $amount_per_cart), $carts);

		if($carts == 0) $carts = 1;
		
		$total = $carts * $amount_per_cart;

		// echo "Total : $total\n";
		
		$ratio = $total / ($r1+$r2+$r3+$r4);
		
		// echo "Ratio : $ratio\n";
		
		// fill it
		$t1 = min($r1, round($r1 * $ratio));
		$t2 = min($r2, round($r2 * $ratio));
		$t3 = min($r3, round($r3 * $ratio));
		$t4 = min($r4, $total - $t1 - $t2 - $t3);

		return array($t1, $t2, $t3, $t4);

	}

	function get_res_info($result)
	{
		// <td id=l4 title=3000>40184/160000</td>
		$ret = preg_match('/<td id=l4 title=[-0-9]+>([0-9]+)\/([0-9]+)<\/td>/', $result, $matches);
		if(!$ret) die("FAILED : get_res_info() : wood");
		
		$wood = $matches[1];
		$max_wood = $matches[2];

		// brick
		$ret = preg_match('/<td id=l3 title=[-0-9]+>([0-9]+)\/([0-9]+)<\/td>/', $result, $matches);
		if(!$ret) die("FAILED : get_res_info() : brick");
		
		$brick = $matches[1];
		$max_brick = $matches[2];

		// iron
		$ret = preg_match('/<td id=l2 title=[-0-9]+>([0-9]+)\/([0-9]+)<\/td>/', $result, $matches);
		if(!$ret) die("FAILED : get_res_info() : iron");
		
		$iron = $matches[1];
		$max_iron = $matches[2];
		
		// food
		$ret = preg_match('/<td id=l1 title=[-0-9]+>([0-9]+)\/([0-9]+)<\/td>/', $result, $matches);
		if(!$ret) die("FAILED : get_res_info() : food");
		
		$food = $matches[1];
		$max_food = $matches[2];
		
		return array($wood, $brick, $iron, $food, $max_wood, $max_brick, $max_iron, $max_food);
	}
	
	// transfer current village to target x,y
	function transfer_confirm($market_id, $target_x, $target_y, $r1, $r2, $r3, $r4)
	{
		global $server;
		
		if($r1 == 0) $r1 = '';
		if($r2 == 0) $r2 = '';
		if($r3 == 0) $r3 = '';
		if($r4 == 0) $r4 = '';
		
		$url = "http://$server/build.php";
		$referer = "http://$server/build.php?id=$market_id";
		
		// id=27&r1=10&r2=20&r3=30&r4=40&dname=&x=-37&y=-71&s1.x=&s1.y=&s1=ok
		$postfields = "id=$market_id&r1=$r1&r2=$r2&r3=$r3&r4=$r4&dname=&x=$target_x&y=$target_y&s1.x=&s1.y=&s1=ok";
		
		// echo "$referer\n";
		echo "$url\n";
		echo "$postfields\n";
		
		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_REFERER, $referer);

		$result = curl_exec ($ch);
		curl_close ($ch);
		
		// echo $result;
		
		// <input type="hidden" name="id" value="27">
		$ret = preg_match('/<input type="hidden" name="id" value="([0-9]+)">/', $result, $matches);
		if(!$ret) die("get id failed.");
		
		$id = $matches[1];
		// echo "id = " . $id . "\n";
		if($id != $market_id) die("id doesn't match.");


		// <input type="hidden" name="a" value="113321">
		$ret = preg_match('/<input type="hidden" name="a" value="([0-9]+)">/', $result, $matches);
		if(!$ret) die("get a failed.");
		
		$a = $matches[1];
		// echo "a = " . $a . "\n";

		// <input type="hidden" name="sz" value="11387">
		$ret = preg_match('/<input type="hidden" name="sz" value="([0-9]+)">/', $result, $matches);
		if(!$ret) die("get sz failed.");
		
		$sz = $matches[1];
		// echo "sz = " . $sz . "\n";

		// <input type="hidden" name="kid" value="377635">
		$ret = preg_match('/<input type="hidden" name="kid" value="([0-9]+)">/', $result, $matches);
		if(!$ret) die("get kid failed.");
		
		$kid = $matches[1];
		// echo "kid = " . $kid . "\n";

		// id=27&a=113321&sz=11387&kid=380038&r1=1000&r2=1500&r3=500&r4=&s1.x=&s1.y=&s1=ok
		$postfields = "id=$id&a=$a&sz=$sz&kid=$kid&r1=$r1&r2=$r2&r3=$r3&r4=$r4&s1.x=&s1.y=&s1=ok";
		echo "$postfields\n";
		
		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_REFERER, $url);

		$result = curl_exec ($ch);
		curl_close ($ch);

	}

	function get_village_xy($village)
	{
		global $account;
		
		$sql = "select x, y from villages where account = $account and id = $village";
		$res = mysql_query($sql);
		if(!$res) die(mysql_error());
		$row = mysql_fetch_row($res);
		
		if(!$row) die("get_village_xy failed. $village");
		
		$x = $row[0];
		$y = $row[1];
		
		return array($x, $y);
	}

	// if below 70%, we need tranfer
	function transfer_to_newbie($village_from, $village_to, $result)
	{
		global $account;
		
		$curr_res = get_res_info($result);
		
		$r1 = max(0, round($curr_res[4] * 0.7) - $curr_res[0]);
		$r2 = max(0, round($curr_res[5] * 0.7) - $curr_res[1]);
		$r3 = max(0, round($curr_res[6] * 0.7) - $curr_res[2]);
		$r4 = max(0, round($curr_res[7] * 0.7) - $curr_res[3]);

		if($r1 + $r2 + $r3 + $r4 < 300){
			
			// transfer back if needed
			if(0.8 * $curr_res[4] < $curr_res[0] || 
			   0.8 * $curr_res[5] < $curr_res[1] || 
			   0.8 * $curr_res[6] < $curr_res[2] || 
			   0.8 * $curr_res[7] < $curr_res[3] ){
			   	   transfer_to_village($village_to, $village_from, false, 80);
			}
			return;
		}

		list($result, $carts, $amount_per_cart, $market_id) = get_transfer_page($village_from);

		$trans_res = calc_transfer_amount_newbie($carts, $amount_per_cart, $r1, $r2, $r3, $r4, $result);

		if($trans_res){
			echo "Try newbie transfer $trans_res[0], $trans_res[1], $trans_res[2], $trans_res[3]\n";

			list($x, $y) = get_village_xy($village_to);
			transfer_confirm($market_id, $x, $y, $trans_res[0], $trans_res[1], $trans_res[2], $trans_res[3]);
			
			$curr_time = time();
			$sql = "update villages set last_beg = $curr_time where account = $account and id = $village_to";
			if(!mysql_query($sql)) die(mysql_error());
		}

	}

	function transfer_to_village($village_from, $village_to, $except_crop = false, $reserve_ratio = 0)
	{
		list($x, $y) = get_village_xy($village_to);
		
		transfer_to_xy($village_from, $x, $y, $except_crop, $reserve_ratio);
	}

	// return array($result_page, $carts, $cart_capacity, $market_id)
	function get_transfer_page($village_id)
	{
		global $server;
		
		$url = "http://$server/build.php?newdid=$village_id&gid=17";

		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);

		$result = curl_exec ($ch);
		curl_close ($ch);
		
		// <tr><td colspan="2">XXX 15/20<br><br></td></tr>
		$ret = preg_match('/<tr><td colspan="2">.+?([0-9]+)\/([0-9]+)<br><br><\/td><\/tr>/', $result, $matches);
		if(!$ret){
			echo "FAILED : get carts.\n";
			return false;
		}
		
		$carts = $matches[1];
		// echo "Carts : $carts \n";
		
		// diff between s5 and 3x
		// <a href="#" onMouseUp="add_res(1);" onClick="return false;">(3000)</a></td>
		// <a href="#" onClick="add_res(1); return false;" onDBLclick="add_res(1); return false;">(1000)</a></td>
		if(!preg_match('/<a href="#" onMouseUp="add_res\(1\);" onClick="return false;">\(([0-9]+)\)<\/a><\/td>/', $result, $matches)){
			if(!preg_match('/<a href="#" onClick="add_res\(1\); return false;" onDBLclick="add_res\(1\); return false;">\(([0-9]+)\)<\/a><\/td>/', $result, $matches)){
				echo "FAILED : get amount per cart\n";
				return false;
			}
		}
		
		$amount_per_cart = $matches[1];
		// echo "Amount per cart : $amount_per_cart \n";

		// <input type="hidden" name="id" value="27">
		$ret = preg_match('/<input type="hidden" name="id" value="([0-9]+)">/', $result, $matches);
		if(!$ret){
			echo "get market id failed.";
			return false;
		}
		
		$market_id = $matches[1];

		return array($result, $carts, $amount_per_cart, $market_id);
	}

	
	function transfer_by_cart($village_id, $target_x, $target_y, $c1, $c2, $c3, $c4, $r1 = 0, $r2 = 0, $r3 = 0, $r4 = 0)
	{
		list($result, $carts, $amount_per_cart, $market_id) = get_transfer_page($village_id);
		
		if(!$result) return false;

		$m1 = $c1 * $amount_per_cart;
		$m2 = $c2 * $amount_per_cart;
		$m3 = $c3 * $amount_per_cart;
		$m4 = $c4 * $amount_per_cart;

		$curr_res = get_res_info($result);

		if($m1 > $curr_res[0] - $r1) $m1 = 0;
		if($m2 > $curr_res[1] - $r2) $m2 = 0;
		if($m3 > $curr_res[2] - $r3) $m3 = 0;
		if($m4 > $curr_res[3] - $r4) $m4 = 0;

		if($m1 + $m2 + $m3 + $m4 > 0 && ($m1 + $m2 + $m3 + $m4) <= $carts * $amount_per_cart){
			transfer_confirm($market_id, $target_x, $target_y, $m1, $m2, $m3, $m4);
		}
	}

	// transfer all resources from a village to target x,y
	function transfer_to_xy($village_id, $target_x, $target_y, $except_crop = false, $reserve_ratio = 0)
	{
		list($result, $carts, $amount_per_cart, $market_id) = get_transfer_page($village_id);
		
		if(!$result) return false;
		
		$curr_res = get_res_info($result);

		$reserve_1 = round($curr_res[4] * $reserve_ratio / 100); // wood, brick, iron
		$reserve_2 = round($curr_res[7] * $reserve_ratio / 100); // crop

		$curr_res[0] = max(0, $curr_res[0] - $reserve_1);
		$curr_res[1] = max(0, $curr_res[1] - $reserve_1);
		$curr_res[2] = max(0, $curr_res[2] - $reserve_1);
		$curr_res[3] = max(0, $curr_res[3] - $reserve_2);

		if($except_crop) $curr_res[3] = 0;
		
		$trans_res = calc_transfer_amount($carts, $amount_per_cart, $curr_res[0], $curr_res[1], $curr_res[2], $curr_res[3]);

		if($trans_res){
			transfer_confirm($market_id, $target_x, $target_y, $trans_res[0], $trans_res[1], $trans_res[2], $trans_res[3]);
		}
	}
	
	// rid : 1-4 wood,brick,iron,food
	// m1 : offer
	// m2 : for
	function sell($m1, $rid1, $m2, $rid2, $free = 2, $reserve = 0)
	{
		global $server;

		$url = "http://$server/build.php?gid=17&t=2";
		echo $url;
		
		$ch = my_curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		$result = curl_exec ($ch);
		curl_close ($ch);

		// get current resource
		$curr_res = get_res_info($result);
		if($curr_res[$rid1 - 1] < $m1){
			echo "No enough resource to sell.\n";
			return false;
		}

		if($curr_res[$rid1 - 1] < $reserve){
			echo "Resource too few.\n";
			return false;
		}

		// get free merchants
		// <p class="f10">è§êlÅF 19/20</p><p><input type="image"
		$ret = preg_match('/<p class="f10">\S+ ([0-9]+)\/[0-9]+<\/p><p><input type="image"/', $result, $matches);
		if(!$ret){
			echo "FAILED : get free merchants.\n";
			return false;
		}
		
		$free_m = $matches[1];
		echo "Free merchants : $free_m \n";
		
		if($free_m <= $free){
			echo "No enough free merchants.\n";
			return false;
		}
		
		
		// <input type="hidden" name="id" value="27">
		$ret = preg_match('/<input type="hidden" name="id" value="([0-9]+)">/', $result, $matches);
		if(!$ret) die("get id failed.");
		
		$id = $matches[1];

		// <input type="hidden" name="a" value="113321">
		$ret = preg_match('/<input type="hidden" name="a" value="([0-9]+)">/', $result, $matches);
		if(!$ret) die("get a failed.");
		
		$a = $matches[1];

		
		//id=23&t=2&a=4&m1=1000&rid1=2&d2=2&m2=2000&rid2=4&s1.x=21&s1.y=7&s1=ok
		$postfields = "id=$id&t=2&a=$a&m1=$m1&rid1=$rid1&d2=2&m2=$m2&rid2=$rid2&s1.x=21&s1.y=7&s1=ok";
		
		echo "$postfields\n";

		$referer = $url;
		$url = "http://$server/build.php";
		
		$ch = my_curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_REFERER, $referer);

		$result = curl_exec ($ch);
		curl_close ($ch);
		
		return true;
	}

	// transfer any resource from current village to any village, with reserve carts
	function transfer_resouce_to_xy($village_from, $x, $y, $r1, $r2, $r3, $r4, $reserve_carts)
	{
		list($result, $carts, $amount_per_cart, $market_id) = get_transfer_page($village_from);
		
		if(!$result) return false;
		
		$curr_res = get_res_info($result);
		
		if($curr_res[0] < $r1 || $curr_res[1] < $r2 || $curr_res[2] < $r3 || $curr_res[3] < $r4) return false;
		
		$total = $r1 + $r2 + $r3 + $r4;
		
		$carts_needed = ceil($total / $amount_per_cart);
		
		if($reserve_carts + $carts_needed > $carts) return false;
		
		transfer_confirm($market_id, $x, $y, $r1, $r2, $r3, $r4);
		
		echo "Transfering $r1 | $r2 | $r3 | $r4 to ($x|$y)\n";
	}

	// transfer all crops to my village
	function transfer_crop_to_village($village_from, $village_to, $reserve = 0)
	{
		list($x, $y) = get_village_xy($village_to);
		
		transfer_crop_to_xy($village_from, $x, $y, $reserve);
	}

	// transfer all crops to any village
	function transfer_crop_to_xy($village_from, $x, $y, $reserve = 0)
	{
		list($result, $carts, $amount_per_cart, $market_id) = get_transfer_page($village_from);
		
		if(!$result) return false;
		
		$curr_res = get_res_info($result);
		
		if($curr_res[3] <= $reserve) return false;
		
		$total = $curr_res[3] - $reserve;
		
		$carts_needed = ceil($total / $amount_per_cart);
		
		if($carts_needed == 0) return;
		if($carts <= 0) return;
		
		$total = min($carts, $carts_needed) * $amount_per_cart;
		
		transfer_confirm($market_id, $x, $y, 0, 0, 0, $total);
		
		echo "Transfering 0 | 0 | 0 | $total to ($x|$y)\n";
	}

	function sell_for_crop($wood, $brick, $iron, $offer, $crop, $reserve, $reserve_carts)
	{
		if($wood >= $brick && $wood >= $iron && $wood >= $reserve + $offer)
			sell($offer, 1, $crop, 4, $reserve_carts, $reserve);
		
		else if($brick >= $wood && $brick >= $iron && $brick >= $reserve + $offer)
			sell($offer, 2, $crop, 4, $reserve_carts, $reserve);

		else if($iron >= $wood && $iron >= $brick && $iron >= $reserve + $offer)
			sell($offer, 3, $crop, 4, $reserve_carts, $reserve);
	}
?>
