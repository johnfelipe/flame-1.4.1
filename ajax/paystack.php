<?php
$types = array('pro','wallet');
$data['status'] = 400;
if ($s == 'initialize') {
	if (!empty($_POST['type']) && in_array($_POST['type'], $types) && !empty($_POST['email'])) {
		$amount = 0;
		if ($_POST['type'] == 'pro') {
			$amount = intval($fl['config']['pro_pkg_price']) * 100;
		}
		elseif ($_POST['type'] == 'wallet' && !empty($_POST['amount']) && is_numeric($_POST['amount']) && $_POST['amount'] > 0) {
			$amount = FL_Secure($_POST['amount']);
		}
		$callback_url = $fl['config']['site_url'] . "/ajax_requests.php?f=paystack&s=paid&type=".$_POST['type'].'&amount='.$amount;

		$result = array();
	    $reference = uniqid();

		//Set other parameters as keys in the $postdata array
		$postdata =  array('email' => $_POST['email'], 'amount' => $amount,"reference" => $reference,'callback_url' => $callback_url);
		$url = "https://api.paystack.co/transaction/initialize";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($postdata));  //Post Fields
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$headers = [
		  'Authorization: Bearer '.$fl['config']['paystack_secret_key'],
		  'Content-Type: application/json',

		];
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$request = curl_exec ($ch);

		curl_close ($ch);

		if ($request) {
		    $result = json_decode($request, true);
		    if (!empty($result)) {
				if (!empty($result['status']) && $result['status'] == 1 && !empty($result['data']) && !empty($result['data']['authorization_url']) && !empty($result['data']['access_code'])) {
				 	$db->where('user_id',$fl['user']['user_id'])->update(T_USERS,array('paystack_ref' => $reference));
				  	$data['status'] = 200;
				  	$data['url'] = $result['data']['authorization_url'];
				}
				else{
			        $data['message'] = $result['message'];
				}
			}
			else{
				$data['message'] = $fl['lang']['payment_declined'];
			}
		}
		else{
			$data['message'] = $fl['lang']['payment_declined'];
		}
	}
	else{
		$data['message'] = $fl['lang']['please_check_details'];
	}
}

if ($s == 'paid') {
	if (!empty($_GET['reference'])) {
		$payment  = CheckPaystackPayment($_GET['reference']);
		if ($payment) {
			if ($_GET['type'] == 'pro') {
        		$update = array('is_pro' => 1,'verified' => 1);
			    $go_pro = $db->where('user_id',$fl['user']['id'])->update(T_USERS,$update);

			    if ($go_pro === true) {
			    	$payment_data = array(
			    		'user_id' => $fl['user']['id'],
			    		'type'    => 'pro',
			    		'amount'  => intval($fl['config']['pro_pkg_price']),
			    		'date'    => date('n') . '/' . date('Y'),
			    		'expire'  => strtotime("+30 days"),
		    		'time' => time()
			    	);

			    	$db->insert(T_PAYMENTS,$payment_data);

			    	$db->where('user_id',$fl['user']['id'])->update(T_LISTS,array('featured' => 1));
			    	$db->where('user_id',$fl['user']['id'])->update(T_QUIZZES,array('featured' => 1));
			    	$db->where('user_id',$fl['user']['id'])->update(T_VIDEOS,array('featured' => 1));
			    	$db->where('user_id',$fl['user']['id'])->update(T_MUSIC,array('featured' => 1));
			    	$db->where('user_id',$fl['user']['id'])->update(T_POLLS_PAGES,array('featured' => 1));
			    	$db->where('user_id',$fl['user']['id'])->update(T_NEWS,array('featured' => 1));

			    	$_SESSION['upgraded'] = true;
			    	header("Location: " . FL_Link('go_pro'));
				    exit();
			    }
			    else{
			    	$data['message'] = $fl['lang']['payment_declined'];
			    }
        	}
        	elseif ($_GET['type'] == 'wallet' && !empty($_GET['amount']) && is_numeric($_GET['amount']) && $_GET['amount'] > 0) {
        		$amount  = FL_Secure($_GET['amount'] / 100);
				$update  = array('wallet' => ($fl['user']['wallet'] += $amount));

				$db->where('user_id',$fl['user']['id'])->update(T_USERS,$update);
				$_SESSION['refilled_balance'] = $amount;

				$payment_data = array(
		    		'user_id' => $fl['user']['id'],
		    		'type'    => 'wallet',
		    		'amount'  => $amount,
		    		'date'    => date('n') . '/' . date('Y'),
		    		'expire'  => 0,
		    		'time' => time()
		    	);

			    $db->insert(T_PAYMENTS,$payment_data);
			    header("Location: " . FL_Link('settings/wallet'));
				exit();
        	}
		}
	}
	header("Location: " . FL_Link(''));
    exit();
}