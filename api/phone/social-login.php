<?php
// +------------------------------------------------------------------------+
// | @author Deen Doughouz (DoughouzForest)
// | @author_url 1: http://www.wowonder.com
// | @author_url 2: http://codecanyon.net/user/doughouzforest
// | @author_email: wowondersocial@gmail.com   
// +------------------------------------------------------------------------+
// | WoWonder - The Ultimate Social Networking Platform
// | Copyright (c) 2018 WoWonder. All rights reserved.
// +------------------------------------------------------------------------+

use AppleSignIn\ASDecoder;
$response_data   = array(
    'api_status' => 400
);
$required_fields = array(
    'access_token',
    'provider'
);
foreach ($required_fields as $key => $value) {
    if (empty($_POST[$value]) && empty($error_code)) {
        $response_data       = array(
            'api_status'     => '404',
            'errors'         => array(
                'error_id'   => '3',
                'error_text' => $value . ' (POST) is missing'
            )
        );
        
        header("Content-type: application/json");
        echo json_encode($response_data);
        exit();
    }
}
if (empty($error_code)) {
	$social_id          = 0;
    $access_token       = $_POST['access_token'];
    $provider           = $_POST['provider'];
    if ($provider == 'facebook') {
    	$get_user_details = fetchDataFromURL("https://graph.facebook.com/me?fields=email,id,name,age_range&access_token={$access_token}");
    	$json_data = json_decode($get_user_details);
    	if (!empty($json_data->error)) {
            $response_data       = array(
                'api_status'     => '404',
                'errors'         => array(
                    'error_id'   => '4',
                    'error_text' => $json_data->error->message
                )
            );
            
            header("Content-type: application/json");
            echo json_encode($response_data);
            exit();
    	} else if (!empty($json_data->id)) {
    		$social_id = $json_data->id;
    		$social_email = $json_data->email;
    		$social_name = $json_data->name;
    		if (empty($social_email)) {
    			$social_email = 'fb_' . $social_id . '@facebook.com';
    		}
    	}
    } else if ($provider == 'google') {
        
		$get_user_details = fetchDataFromURL("https://oauth2.googleapis.com/tokeninfo?id_token={$access_token}");
		$json_data = json_decode($get_user_details);
		if (!empty($json_data->error)) {
            $response_data       = array(
                'api_status'     => '404',
                'errors'         => array(
                    'error_id'   => '4',
                    'error_text' => $json_data->error
                )
            );
            
            header("Content-type: application/json");
            echo json_encode($response_data);
            exit();
    	} else if (!empty($json_data->kid)) {
    		$social_id = $json_data->kid;
    		$social_email = $json_data->email;
    		$social_name = $json_data->name;
    		if (empty($social_email)) {
    			$social_email = 'go_' . $social_id . '@google.com';
    		}
    	}
    }
    elseif ($provider == 'apple') {
        include_once('assets/import/apple/vendor/autoload.php');
        try{
            $appleSignInPayload = ASDecoder::getAppleSignInPayload($access_token);
            $social_email = $appleSignInPayload->getEmail();
            $social_id = $social_name = $appleSignInPayload->getUser();
        }
        catch(exception $e){
            $response_data       = array(
                'api_status'     => '404',
                'errors'         => array(
                    'error_id'   => '4',
                    'error_text' => $e
                )
            );
            
            header("Content-type: application/json");
            echo json_encode($response_data);
            exit();
        }
    }
    if (!empty($social_id)) {
    	$create_session = false;
    	if (FL_EmailExists($social_email) === true) {
    		$create_session = true;
    	} else {
    		$str          = md5(microtime());
            $id           = substr($str, 0, 9);
            $user_uniq_id = (FL_UserExists($id) === false) ? $id : 'u_' . $id;
            $password = rand(1111, 9999);
            $re_data                    = array(
                'email'                 => FL_Secure($social_email),
                'username'              => FL_Secure($user_uniq_id),
                'password'              => FL_Secure(md5($password)),
                'email_code'            => FL_Secure(md5(time())),
                'src'                   => 'Phone',
                'timezone'              => 'UTC',
                'active'                => 1,
                'time' => time()
            );
            if (FL_RegisterUser($re_data) === true) {
            	$create_session = true;
            }
    	}

    	if ($create_session == true) {
            $json_success_data  = array(
                'api_status'    => '200',
                'api_text'      => 'success',
                'api_version'   => $api_version,
                'message'       => 'Successfully joined, Please wait..',
                'success_type'  => 'joined',
                'session_id'    => 0,
                'cookie'        => FL_CreateLoginSession(FL_UserIdForLogin($username)),
                'user_id'       => 0
            );
            $device_id                  = '';
            if (!empty($_POST['device_id'])) {
                $device_id              = Fl_Secure($_POST['device_id']);
            }
    		$user_id        = FL_UserIdForLogin($social_email);
    		$s_md5   = sha1(rand(111111111, 999999999)) . md5(microtime()) . rand(11111111, 99999999) . md5(rand(5555, 9999));
            $time    = time();
            $add_session = mysqli_query($sqlConnect, "INSERT INTO " . T_APP_SESSIONS . " (`user_id`, `session_id`, `platform`, `time`) VALUES ('{$user_id}', '{$s_md5}', 'phone', '{$time}')");
            
            if (!empty($device_id)) {
                $update  = mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `device_id` = '{$device_id}' WHERE `user_id` = '{$user_id}'");
            }
            if ($add_session) {
                $json_success_data['cookie'] = $s_md5;
                $json_success_data['session_id'] = $s_md5;
                $json_success_data['user_id']    = $user_id;
            }
    	}
    }
}