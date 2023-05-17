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
				require_once 'assets/import/iyzipay/samples/config.php';
				$callback_url = $fl['config']['site_url'] . "/ajax_requests.php?f=iyzipay&s=paid&type=".$_POST['type'].'&amount='.$amount;

				
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
					$response_data  = array(
		                'api_status' => '200',
		                'api_text' => 'success',
		                'api_version' => $api_version,
		                'form' => $content
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
				            'error_text' => 'something went wrong'
				        )
				    );
				    
				    header("Content-type: application/json");
				    echo json_encode($response_data);
				    exit();
				}
			}
			elseif ($_POST['request'] == 'pro_paid') {
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
					else{
						$response_data       = array(
					        'api_status'     => '404',
					        'errors'         => array(
					            'error_id'   => '7',
					            'error_text' => 'payment not approved'
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
				            'error_id'   => '6',
				            'error_text' => 'token can not be empty'
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