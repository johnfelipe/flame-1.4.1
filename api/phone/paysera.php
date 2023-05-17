<?php
if ($fl['loggedin'] == true) {
	$requests = array('initialize','pro_paid');
	$types = array('pro');
	if (!empty($_POST['request']) && in_array($_POST['request'], $requests)) {
		if (!empty($_POST['type']) && in_array($_POST['type'], $types)) {
			if ($_POST['request'] == 'initialize') {
				$amount = 0;
				if ($_POST['type'] == 'pro') {
					$amount = intval($fl['config']['pro_pkg_price']);
				}
				$callback_url = $fl['config']['site_url'] . "/ajax_requests.php?f=paysera&s=paid&type=".$_POST['type'].'&amount='.$amount;
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
				$response_data  = array(
	                'api_status' => '200',
	                'api_text' => 'success',
	                'api_version' => $api_version,
	                'url' => $request
	            );
	            header("Content-type: application/json");
	            echo json_encode($response_data);
	            exit();
			}
			elseif ($_POST['request'] == 'pro_paid') {
				try {
					require_once 'assets/import/Paysera.php';
			        $response = WebToPay::checkResponse($_GET, array(
			            'projectid'     => $fl['config']['paysera_project_id'],
			            'sign_password' => $fl['config']['paysera_sign_password'],
			        ));
			        if ($response['type'] !== 'macro') {
			        	$response_data       = array(
					        'api_status'     => '404',
					        'errors'         => array(
					            'error_id'   => '8',
					            'error_text' => 'something went wrong'
					        )
					    );
					    
					    header("Content-type: application/json");
					    echo json_encode($response_data);
					    exit();
			        }
			        $orderId = $response['orderid'];
			        $amount = $response['amount'];
			        $currency = $response['currency'];
			        $is_pro = 1;
				} catch (Exception $e) {
				    $response_data       = array(
				        'api_status'     => '404',
				        'errors'         => array(
				            'error_id'   => '9',
				            'error_text' => 'something went wrong'
				        )
				    );
				    
				    header("Content-type: application/json");
				    echo json_encode($response_data);
				    exit();
				}
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
					            'error_id'   => '8',
					            'error_text' => 'something went wrong'
					        )
					    );
					    
					    header("Content-type: application/json");
					    echo json_encode($response_data);
					    exit();
				    }
				}
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