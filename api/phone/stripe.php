<?php
if ($fl['loggedin'] == true) {
	$types = array('pro');
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
	            'error_id'   => '5',
	            'error_text' => 'type , stripeToken can not be empty'
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