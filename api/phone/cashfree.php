<?php
if ($fl['loggedin'] == true) {
	$requests = array('initialize','pro_paid');
	$types = array('pro');
	if (!empty($_POST['request']) && in_array($_POST['request'], $requests)) {
		if ($_POST['request'] == 'initialize') {
			if (!empty($_POST['type']) && in_array($_POST['type'], $types) && !empty($_POST['phone']) && !empty($_POST['name']) && !empty($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
				$amount = 0;
				if ($_POST['type'] == 'pro') {
					$amount = intval($fl['config']['pro_pkg_price']);
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
				$response_data  = array(
	                'api_status' => '200',
	                'api_text' => 'success',
	                'api_version' => $api_version,
	                'form' => $form,
	                'cashfree_link' => $cashfree_link,
	                'appId' => $fl['config']['cashfree_client_key'],
	                'orderId' => $order_id,
	                'orderAmount' => $amount,
	                'orderCurrency' => 'INR',
	                'orderNote' => '',
	                'customerName' => $name,
	                'customerEmail' => $email,
	                'customerPhone' => $phone,
	                'returnUrl' => $callback_url,
	                'notifyUrl' => $callback_url,
	                'signature' => $signature
	            );
	            header("Content-type: application/json");
	            echo json_encode($response_data);
	            exit();
			}
			else{
				$response_data       = array(
			        'api_status'     => '404',
			        'errors'         => array(
			            'error_id'   => '5',
			            'error_text' => 'type , phone , name , email can not be empty'
			        )
			    );
			    
			    header("Content-type: application/json");
			    echo json_encode($response_data);
			    exit();
			}
		}
		elseif ($_POST['request'] == 'pro_paid') {
			if (!empty($_POST['type']) && in_array($_POST['type'], $types) && !empty($_POST['txStatus']) && $_POST['txStatus'] == 'SUCCESS' && !empty($_POST["orderId"]) && !empty($_POST["orderAmount"]) && !empty($_POST["referenceId"]) && !empty($_POST["paymentMode"]) && !empty($_POST["txMsg"]) && !empty($_POST["txTime"]) && !empty($_POST["signature"])) {
				$amount = 0;
				if ($_POST['type'] == 'pro') {
					$amount = intval($fl['config']['pro_pkg_price']);
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

					    	$response_data  = array(
				                'api_status' => '200',
				                'api_text' => 'success',
				                'api_version' => $api_version,
				                'message' => 'you are now a pro user'
				            );
				            header("Content-type: application/json");
				            echo json_encode($response_data);
				            exit();
					    }
					    else{
					    	$response_data       = array(
						        'api_status'     => '404',
						        'errors'         => array(
						            'error_id'   => '4',
						            'error_text' => 'something went wrong'
						        )
						    );
						    
						    header("Content-type: application/json");
						    echo json_encode($response_data);
						    exit();
					    }
			    	}
				}
				else{
					$response_data       = array(
				        'api_status'     => '404',
				        'errors'         => array(
				            'error_id'   => '3',
				            'error_text' => 'something went wrong'
				        )
				    );
				    
				    header("Content-type: application/json");
				    echo json_encode($response_data);
				    exit();
				}
			}
			else{
				$response_data       = array(
			        'api_status'     => '404',
			        'errors'         => array(
			            'error_id'   => '2',
			            'error_text' => 'type , txStatus != SUCCESS , orderId , orderAmount , referenceId , paymentMode , txMsg , txTime , signature  can not be empty'
			        )
			    );
			    
			    header("Content-type: application/json");
			    echo json_encode($response_data);
			    exit();
			}
		}
	}
	else{
		$response_data       = array(
	        'api_status'     => '404',
	        'errors'         => array(
	            'error_id'   => '4',
	            'error_text' => 'request can not be empty'
	        )
	    );
	    
	    header("Content-type: application/json");
	    echo json_encode($response_data);
	    exit();
	}	
}
else{
	$response_data       = array(
        'api_status'     => '404',
        'errors'         => array(
            'error_id'   => '1',
            'error_text' => 'not loggedin'
        )
    );
    
    header("Content-type: application/json");
    echo json_encode($response_data);
    exit();
}