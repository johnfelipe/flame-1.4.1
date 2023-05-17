<?php
$types = array('pro','wallet');
$data['status'] = 400;
if (!empty($_GET['type']) && in_array($_GET['type'], $types) && !empty($_POST['payment_id']) && !empty($_POST['order_id']) && !empty($_POST['merchant_amount']) && !empty($_POST['currency'])) {
	$amount = 0;
	if ($_GET['type'] == 'pro') {
		$amount = intval($fl['config']['pro_pkg_price']);
	}
	elseif ($_GET['type'] == 'wallet' && !empty($_POST['merchant_amount']) && is_numeric($_POST['merchant_amount']) && $_POST['merchant_amount'] > 0) {
		$amount = FL_Secure($_POST['merchant_amount'] / 100);
	}
	$currency_code = "INR";
    $check = array(
	    'amount' => $_POST['merchant_amount'],
	    'currency' => $currency_code,
	);
	$json = CheckRazorpayPayment($_POST['payment_id'],$check);
    if (!empty($json) && empty($json->error_code)) {
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
			    $data['status'] = 200;
				$data['url'] = FL_Link('go_pro');
		    }
		    else{
		    	$data['message'] = $fl['lang']['payment_declined'];
		    }
    	}
    	elseif ($_GET['type'] == 'wallet') {
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
			$data['status'] = 200;
			$data['url'] = FL_Link('settings/wallet');
    	}
    }
    else{
    	$data['message'] = $fl['lang']['payment_declined'];
    }
}
else{
	$data['message'] = $fl['lang']['please_check_details'];
}
header("Content-type: application/json");
echo json_encode($data);
exit();