<?php
if ($fl['loggedin'] == true) {
	$types = array('pro');
	if (!empty($_POST['type']) && in_array($_POST['type'], $types)) {
		if (!empty($_POST['payment_id']) && !empty($_POST['order_id']) && !empty($_POST['merchant_amount'])) {
			$amount = 0;
			if ($_POST['type'] == 'pro') {
				$amount = intval($fl['config']['pro_pkg_price']);
			}
			$currency_code = "INR";
		    $check = array(
			    'amount' => $_POST['merchant_amount'],
			    'currency' => $currency_code,
			);
			$json = CheckRazorpayPayment($_POST['payment_id'],$check);
		    if (!empty($json) && empty($json->error_code) && empty($json->error)) {
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
					            'error_id'   => '5',
					            'error_text' => 'payment declined'
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
			            'error_id'   => '5',
			            'error_text' => 'payment declined'
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
		            'error_id'   => '4',
		            'error_text' => 'payment_id , order_id , merchant_amount can not be empty'
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
	            'error_id'   => '4',
	            'error_text' => 'type can not be empty'
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