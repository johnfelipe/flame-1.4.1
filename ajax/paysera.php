<?php
$types = array('pro','wallet');
$data['status'] = 400;
if ($s == 'get_url' && !empty($_GET['type']) && in_array($_GET['type'], $types) && !empty($_POST['amount']) && is_numeric($_POST['amount']) && $_POST['amount'] > 0) {
	$amount = 0;
	if ($_GET['type'] == 'pro') {
		$amount = intval($fl['config']['pro_pkg_price']) * 100;
	}
	elseif ($_GET['type'] == 'wallet' && !empty($_POST['amount']) && is_numeric($_POST['amount']) && $_POST['amount'] > 0) {
		$amount = FL_Secure($_POST['amount']);
	}
	$callback_url = $fl['config']['site_url'] . "/ajax_requests.php?f=paysera&s=paid&type=".$_GET['type'].'&amount='.$amount;
	require_once 'assets/import/Paysera.php';

    $request = WebToPay::redirectToPayment(array(
	    'projectid'     => $fl['config']['paysera_project_id'],
	    'sign_password' => $fl['config']['paysera_sign_password'],
	    'orderid'       => rand(111111,999999),
	    'amount'        => $amount,
	    'currency'      => 'USD',
	    'country'       => 'LT',
	    'accepturl'     => $callback_url,
	    'cancelurl'     => $fl['config']['site_url'],
	    'callbackurl'   => $fl['config']['site_url'],
	    'test'          => $fl['config']['paysera_mode'],
	));
	$data = array('status' => 200,
                  'url' => $request);

}
if ($s == 'paid') {
	try {
        $response = WebToPay::checkResponse($_GET, array(
            'projectid'     => $fl['config']['paysera_project_id'],
            'sign_password' => $fl['config']['paysera_sign_password'],
        ));
 
        // if ($response['test'] !== '0') {
        //     throw new Exception('Testing, real payment was not made');
        // }
        if ($response['type'] !== 'macro') {
        	header("Location: " . FL_Link(''));
            exit();
            //throw new Exception('Only macro payment callbacks are accepted');
        }
        $orderId = $response['orderid'];
        $amount = $response['amount'];
        $currency = $response['currency'];

        if ($amount1 != $amount || $currency != $fl['config']['currency']) {
        	header("Location: " . FL_Link(''));
            exit();
        }
        $is_pro = 1;
	} catch (Exception $e) {
	    header("Location: " . FL_Link(''));
        exit();
	}
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
header("Content-type: application/json");
echo json_encode($data);
exit();