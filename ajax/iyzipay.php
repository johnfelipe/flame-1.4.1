<?php
$types = array('pro','wallet');
$data['status'] = 400;
if ($s == 'pay' && !empty($_GET['type']) && in_array($_GET['type'], $types)) {
	$amount = 0;
	if ($_GET['type'] == 'pro') {
		$amount = intval($fl['config']['pro_pkg_price']);
	}
	elseif ($_GET['type'] == 'wallet' && !empty($_POST['amount']) && is_numeric($_POST['amount']) && $_POST['amount'] > 0) {
		$amount = FL_Secure($_POST['amount'] / 100);
	}
	require_once 'assets/import/iyzipay/samples/config.php';
	$callback_url = $fl['config']['site_url'] . "/ajax_requests.php?f=iyzipay&s=paid&type=".$_GET['type'].'&amount='.$amount;

	
	$request->setPrice($amount);
	$request->setPaidPrice($amount);
	$request->setCallbackUrl($callback_url);
	

	$basketItems = array();
	$firstBasketItem = new \Iyzipay\Model\BasketItem();
	$firstBasketItem->setId("BI".rand(11111111,99999999));
	$firstBasketItem->setName("subscribe");
	$firstBasketItem->setCategory1("subscribe");
	$firstBasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
	$firstBasketItem->setPrice($amount);
	$basketItems[0] = $firstBasketItem;
	$request->setBasketItems($basketItems);
	$checkoutFormInitialize = \Iyzipay\Model\CheckoutFormInitialize::create($request, Config::options());
	$content = $checkoutFormInitialize->getCheckoutFormContent();
	if (!empty($content)) {
		$db->where('user_id',$fl['user']['user_id'])->update(T_USERS,array('ConversationId' => $ConversationId));
		$data['html'] = $content;
		$data['status'] = 200;
	}
	else{
		$data['message'] = $lang->please_check_details;
	}
}
if ($s == 'paid') {
	if (!empty($_POST['token']) && !empty($fl['user']['ConversationId'])) {
		require_once('assets/import/iyzipay/samples/config.php');

		# create request class
		$request = new \Iyzipay\Request\RetrieveCheckoutFormRequest();
		$request->setLocale(\Iyzipay\Model\Locale::TR);
		$request->setConversationId($fl['user']['ConversationId']);
		$request->setToken($_POST['token']);

		# make request
		$checkoutForm = \Iyzipay\Model\CheckoutForm::retrieve($request, Config::options());

		# print result
		if ($checkoutForm->getPaymentStatus() == 'SUCCESS') {
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
			header('Location: ' . FL_Link(''));
	        exit();
		}
	}
	else{
		header('Location: ' . FL_Link(''));
	    exit();
	}
}
header("Content-type: application/json");
echo json_encode($data);
exit();