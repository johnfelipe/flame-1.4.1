<?php
$types = array('pro','wallet');
$data['status'] = 400;
if (!empty($_POST['type']) && in_array($_POST['type'], $types) && !empty($_POST['stripeToken'])) {
	require_once('assets/import/stripe-php-3.20.0/vendor/autoload.php');
	$stripe = array(
	  "secret_key"      =>  $fl['config']['stripe_secret'],
	  "publishable_key" =>  $fl['config']['stripe_id']
	);

	\Stripe\Stripe::setApiKey($stripe['secret_key']);
	$amount = 0;
	if ($_POST['type'] == 'pro') {
		$amount = intval($fl['config']['pro_pkg_price']) * 100;
	}
	elseif ($_POST['type'] == 'wallet' && !empty($_POST['amount']) && is_numeric($_POST['amount']) && $_POST['amount'] > 0) {
		$amount = FL_Secure($_POST['amount']);
	}
	$token = $_POST['stripeToken'];
	$customer = \Stripe\Customer::create(array(
        'source' => $token
    ));
    $charge   = \Stripe\Charge::create(array(
        'customer' => $customer->id,
        'amount' => $amount,
        'currency' => 'USD'
    ));
    if (!empty($charge)) {
    	$chargeJson = $charge->jsonSerialize(); 
        if($chargeJson['status'] == 'succeeded'){
        	if ($_POST['type'] == 'pro') {
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
        	elseif ($_POST['type'] == 'wallet' && !empty($_POST['amount']) && is_numeric($_POST['amount']) && $_POST['amount'] > 0) {
        		$amount  = $amount / 100;
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
    	$data['message'] = $fl['lang']['something_went_wrong'];
    }
}
header("Content-type: application/json");
echo json_encode($data);
exit();