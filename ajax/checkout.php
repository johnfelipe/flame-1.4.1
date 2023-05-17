<?php
$types = array('pro','wallet');
$data['status'] = 400;
if (!empty($_POST['type']) && in_array($_POST['type'], $types) && !empty($_POST['card_number']) && !empty($_POST['card_cvc']) && !empty($_POST['card_month']) && !empty($_POST['card_year']) && !empty($_POST['token']) && !empty($_POST['card_name']) && !empty($_POST['card_address']) && !empty($_POST['card_city']) && !empty($_POST['card_state']) && !empty($_POST['card_zip']) && !empty($_POST['card_country']) && !empty($_POST['card_email']) && !empty($_POST['card_phone'])) {
	require_once 'assets/import/2checkout/Twocheckout.php';
    Twocheckout::privateKey($fl['config']['checkout_private_key']);
    Twocheckout::sellerId($fl['config']['checkout_seller_id']);
    if ($fl['config']['checkout_mode'] == 'sandbox') {
        Twocheckout::sandbox(true);
    } else {
        Twocheckout::sandbox(false);
    }
    $amount = 0;
	if ($_POST['type'] == 'pro') {
		$amount = intval($fl['config']['pro_pkg_price']);
	}
	elseif ($_POST['type'] == 'wallet' && !empty($_POST['amount']) && is_numeric($_POST['amount']) && $_POST['amount'] > 0) {
		$amount = FL_Secure($_POST['amount']);
	}

    try {
    	$charge  = Twocheckout_Charge::auth(array(
            "merchantOrderId" => "123",
            "token" => $_POST['token'],
            "currency" => $fl['config']['2checkout_currency'],
            "total" => $amount,
            "billingAddr" => array(
                "name" => $_POST['card_name'],
                "addrLine1" => $_POST['card_address'],
                "city" => $_POST['card_city'],
                "state" => $_POST['card_state'],
                "zipCode" => $_POST['card_zip'],
                "country" => $fl['countries_name'][$_POST['card_country']],
                "email" => $_POST['card_email'],
                "phoneNumber" => $_POST['card_phone']
            )
        ));
        if ($charge['response']['responseCode'] == 'APPROVED') {
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
    catch (Twocheckout_Error $e) {
        $data['message'] = $e->getMessage();
    }

}
else{
	$data['message'] = $fl['lang']['please_check_details'];
}
header("Content-type: application/json");
echo json_encode($data);
exit();