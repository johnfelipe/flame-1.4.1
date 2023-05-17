<?php
$types = array('pro','wallet');
$data['status'] = 400;
if ($s == 'initialize') {
	if (!empty($_POST['type']) && in_array($_POST['type'], $types) && !empty($_POST['email']) && !empty($_POST['name']) && !empty($_POST['phone'])) {
		$amount = 0;
		if ($_POST['type'] == 'pro') {
			$amount = intval($fl['config']['pro_pkg_price']);
		}
		elseif ($_POST['type'] == 'wallet' && !empty($_POST['amount']) && is_numeric($_POST['amount']) && $_POST['amount'] > 0) {
			$amount = FL_Secure($_POST['amount'] / 100);
		}
		$callback_url = $fl['config']['site_url'] . "/ajax_requests.php?f=cashfree&s=paid&type=".$_POST['type'].'&amount='.$amount;
		$result = array();
	    $order_id = uniqid();
	    $name = FL_Secure($_POST['name']);
	    $email = FL_Secure($_POST['email']);
	    $phone = FL_Secure($_POST['phone']);


	    $secretKey = $fl['config']['cashfree_secret_key'];
		$postData = array( 
		  "appId" => $fl['config']['cashfree_client_key'], 
		  "orderId" => "order".$order_id, 
		  "orderAmount" => $amount, 
		  "orderCurrency" => "INR", 
		  "orderNote" => "", 
		  "customerName" => $name, 
		  "customerPhone" => $phone, 
		  "customerEmail" => $email,
		  "returnUrl" => $callback_url, 
		  "notifyUrl" => $callback_url,
		);
		 // get secret key from your config
		 ksort($postData);
		 $signatureData = "";
		 foreach ($postData as $key => $value){
		      $signatureData .= $key.$value;
		 }
		 $signature = hash_hmac('sha256', $signatureData, $secretKey,true);
		 $signature = base64_encode($signature);
		 $cashfree_link = 'https://test.cashfree.com/billpay/checkout/post/submit';
		 if ($fl['config']['cashfree_mode'] == 'live') {
		 	$cashfree_link = 'https://www.cashfree.com/checkout/post/submit';
		 }

		$form = '<form id="redirectForm" method="post" action="'.$cashfree_link.'"><input type="hidden" name="appId" value="'.$fl['config']['cashfree_client_key'].'"/><input type="hidden" name="orderId" value="order'.$order_id.'"/><input type="hidden" name="orderAmount" value="'.$amount.'"/><input type="hidden" name="orderCurrency" value="INR"/><input type="hidden" name="orderNote" value=""/><input type="hidden" name="customerName" value="'.$name.'"/><input type="hidden" name="customerEmail" value="'.$email.'"/><input type="hidden" name="customerPhone" value="'.$phone.'"/><input type="hidden" name="returnUrl" value="'.$callback_url.'"/><input type="hidden" name="notifyUrl" value="'.$callback_url.'"/><input type="hidden" name="signature" value="'.$signature.'"/></form>';
		$data['status'] = 200;
		$data['html'] = $form;

	}
	else{
		$data['message'] = $fl['lang']['please_check_details'];
	}
}
if ($s == 'paid') {
	if (empty($_POST['txStatus']) || $_POST['txStatus'] != 'SUCCESS') {
		header("Location: " . FL_Link(''));
        exit();
	}
	$orderId = $_POST["orderId"];
	$orderAmount = $_POST["orderAmount"];
	$referenceId = $_POST["referenceId"];
	$txStatus = $_POST["txStatus"];
	$paymentMode = $_POST["paymentMode"];
	$txMsg = $_POST["txMsg"];
	$txTime = $_POST["txTime"];
	$signature = $_POST["signature"];
	$data = $orderId.$orderAmount.$referenceId.$txStatus.$paymentMode.$txMsg.$txTime;
	$hash_hmac = hash_hmac('sha256', $data, $fl['config']['cashfree_secret_key'], true) ;
	$computedSignature = base64_encode($hash_hmac);
	if ($signature == $computedSignature) {
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
		    	header("Location: " . FL_Link(''));
		        exit();
		    }
    	}
    	elseif ($_GET['type'] == 'wallet' && !empty($_GET['amount']) && is_numeric($_GET['amount']) && $_GET['amount'] > 0) {
    		$amount  = FL_Secure($_GET['amount']);
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
	else{
		header("Location: " . FL_Link(''));
        exit();
	}
	header("Location: " . FL_Link(''));
    exit();
}
header("Content-type: application/json");
echo json_encode($data);
exit();