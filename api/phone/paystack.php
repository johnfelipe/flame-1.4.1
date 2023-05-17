<?php
if ($fl['loggedin'] == true) {
	$requests = array('initialize','pro_paid');
	$types = array('pro');
	if (!empty($_POST['request']) && in_array($_POST['request'], $requests)) {
		if (!empty($_POST['type']) && in_array($_POST['type'], $types)) {
			if ($_POST['request'] == 'initialize') {
				if (!empty($_POST['email'])) {
					$amount = 0;
					if ($_POST['type'] == 'pro') {
						$amount = intval($fl['config']['pro_pkg_price']) * 100;
					}

					$callback_url = $fl['config']['site_url'] . "/ajax_requests.php?f=paystack&s=paid&type=".$_POST['type'].'&amount='.$amount;

					$result = array();
				    $reference = uniqid();

					//Set other parameters as keys in the $postdata array
					$postdata =  array('email' => $_POST['email'], 'amount' => $amount,"reference" => $reference,'callback_url' => $callback_url);
					$url = "https://api.paystack.co/transaction/initialize";

					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL,$url);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($postdata));  //Post Fields
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

					$headers = [
					  'Authorization: Bearer '.$fl['config']['paystack_secret_key'],
					  'Content-Type: application/json',

					];
					curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

					$request = curl_exec ($ch);

					curl_close ($ch);

					if ($request) {
					    $result = json_decode($request, true);
					    if (!empty($result)) {
							if (!empty($result['status']) && $result['status'] == 1 && !empty($result['data']) && !empty($result['data']['authorization_url']) && !empty($result['data']['access_code'])) {
							 	$db->where('user_id',$fl['user']['user_id'])->update(T_USERS,array('paystack_ref' => $reference));
							  	$response_data  = array(
					                'api_status' => '200',
					                'api_text' => 'success',
					                'api_version' => $api_version,
					                'url' => $result['data']['authorization_url']
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
							            'error_text' => $result['message']
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
				            'error_text' => 'email can not be empty'
				        )
				    );
				    
				    header("Content-type: application/json");
				    echo json_encode($response_data);
				    exit();
				}
			}
			elseif ($_POST['request'] == 'pro_paid') {
				if (!empty($_POST['reference'])) {
					$payment  = CheckPaystackPayment($_POST['reference']);
					if ($payment) {
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
				            'error_id'   => '4',
				            'error_text' => 'reference can not be empty'
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