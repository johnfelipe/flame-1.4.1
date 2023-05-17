<?php
if ($fl['loggedin'] == true) {
	$Userdata = $fl['user'];
	if (!empty($_POST['email'])) {
		if ($_POST['email'] != $Userdata['email']) {
            if (FL_EmailExists($_POST['email'])) {
                $error = 'email exists';
            }
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $error = 'email invalid characters';
            }
        }
	}
	if (!empty($_POST['username'])) {
		if ($_POST['username'] != $Userdata['username']) {
            $is_exist = FL_UserExists($_POST['username']);
            if ($is_exist) {
                $error = 'username exists';
            }
        }
        if (in_array($_POST['username'], $fl['site_pages'])) {
            $error = 'username invalid characters';
        }
        if (strlen($_POST['username']) < 5 || strlen($_POST['username']) > 32) {
            $error = 'username characters length';
        }
        if (!preg_match('/^[\w]+$/', $_POST['username'])) {
            $error = 'username invalid characters';
        }
	}
	if (!empty($_POST['current_password']) || !empty($_POST['new_password']) || !empty($_POST['confirm_new_password'])) {
		if (empty($_POST['current_password']) || empty($_POST['new_password']) || empty($_POST['confirm_new_password'])) {
            $error = 'current_password , new_password , confirm_new_password can not be empty';
        }
        if ($Userdata['password'] != sha1($_POST['current_password'])) {
            $error = 'current password dont match';
        }
        if ($_POST['new_password'] != $_POST['confirm_new_password']) {
            $error = 'new password dont match';
        }

	}
	$field_data = array();
    $fields = FL_GetProfileFields('general');
    foreach ($fields as $key => $field) {
        $name = $field['fid'];
        if (isset($_POST[$name])) {
            if (mb_strlen($_POST[$name]) > $field['length']) {
                $error = $field['name'] . ' field max characters is ' . $field['length'];
            }
            $field_data[] = array(
                $name => $_POST[$name]
            );
        }
    }

	if (empty($error)) {
		$Update_data = array();
        $gender_array = array(
            'male',
            'female'
        );
        if (!empty($_POST['gender'])) {
            if (in_array($_POST['gender'], $gender_array)) {
                $Update_data['gender'] = FL_Secure($_POST['gender']);
            }
        }
        if (!empty($_POST['username'])) {
        	$Update_data['username'] = FL_Secure($_POST['username']);
        }
        if (!empty($_POST['email'])) {
        	$Update_data['email'] = FL_Secure($_POST['email']);
        }
        if (!empty($_POST['country_id'])) {
        	$Update_data['country_id'] = FL_Secure($_POST['country_id']);
        }
        if (!empty($_POST['first_name'])) {
        	$Update_data['first_name'] = FL_Secure($_POST['first_name']);
        }
        if (!empty($_POST['last_name'])) {
        	$Update_data['last_name'] = FL_Secure($_POST['last_name']);
        }
        if (!empty($_POST['about'])) {
        	$Update_data['about'] = FL_Secure($_POST['about']);
        }
        if (!empty($_POST['facebook'])) {
        	$Update_data['facebook'] = FL_Secure($_POST['facebook']);
        }
        if (!empty($_POST['google'])) {
        	$Update_data['google'] = FL_Secure($_POST['google']);
        }
        if (!empty($_POST['twitter'])) {
        	$Update_data['twitter'] = FL_Secure($_POST['twitter']);
        }
        if (!empty($_POST['new_password'])) {
        	$Update_data['password'] = sha1($_POST['new_password']);
        }
        if (!empty($_FILES['avatar']['tmp_name'])) {
            $file_info   = array(
                'file' => $_FILES['avatar']['tmp_name'],
                'size' => $_FILES['avatar']['size'],
                'name' => $_FILES['avatar']['name'],
                'type' => $_FILES['avatar']['type'],
                'crop' => array(
                    'width' => 400,
                    'height' => 400
                )
            );
            $file_upload = FL_ShareFile($file_info);
            if (!empty($file_upload['filename'])) {
                $Update_data['avatar'] = $file_upload['filename'];
            }
        }
        if (!empty($_FILES['cover']['tmp_name'])) {
            $file_info   = array(
                'file' => $_FILES['cover']['tmp_name'],
                'size' => $_FILES['cover']['size'],
                'name' => $_FILES['cover']['name'],
                'type' => $_FILES['cover']['type'],
                'crop' => array(
                    'width' => 900,
                    'height' => 300
                )
            );
            $file_upload = FL_ShareFile($file_info);
            if (!empty($file_upload['filename'])) {
                $Update_data['cover'] = $file_upload['filename'];
            }
        }
        if (!empty($Update_data)) {
        	if (FL_UpdateUserData($Userdata['user_id'], $Update_data)) {
	        	if (!empty($field_data)) {
		        	FL_UpdateUserCustomData($Userdata['user_id'], $field_data);
		        }
	        	$response_data  = array(
	                'api_status' => '200',
	                'api_text' => 'success',
	                'api_version' => $api_version,
	                'message' => 'Setting successfully updated !'
	            );
	            header("Content-type: application/json");
	            echo json_encode($response_data);
	            exit();
	        }
	        else{
	        	$response_data       = array(
			        'api_status'     => '404',
			        'errors'         => array(
			            'error_id'   => '2',
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
		            'error_text' => 'no data to update'
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
	            'error_id'   => '3',
	            'error_text' => $error
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