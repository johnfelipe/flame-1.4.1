<?php
if ($fl['loggedin'] == true) {
	if (empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['text']) || empty($_FILES['passport']) || empty($_FILES['image'])) {
        $error = 'first_name , last_name , text , passport , image can not be empty';
    }
    else{
    	if (!empty($_FILES["image"]["error"]) || !empty($_FILES["passport"]["error"])) {
            $error = 'file is big';
        }
        if (strlen($_POST['first_name']) < 4 || strlen($_POST['first_name']) > 32) {
            $error = 'enter valid name';
        }
        if (strlen($_POST['last_name']) > 32) {
            $error = 'invalid last name';
        }
        if (!file_exists($_FILES['passport']['tmp_name'])) {
            $error = 'id file invalid';
        }
        if (!file_exists($_FILES['image']['tmp_name'])) {
            $error = 'img file invalid';
        }
        if (file_exists($_FILES["passport"]["tmp_name"])) {
            $image = getimagesize($_FILES["passport"]["tmp_name"]);
            if (!in_array($image[2], array(
                IMAGETYPE_GIF,
                IMAGETYPE_JPEG,
                IMAGETYPE_PNG,
                IMAGETYPE_BMP
            ))) {
                $error = 'id file mustbe img';
            }
        }
        if (file_exists($_FILES["image"]["tmp_name"])) {
            $image = getimagesize($_FILES["image"]["tmp_name"]);
            if (!in_array($image[2], array(
                IMAGETYPE_GIF,
                IMAGETYPE_JPEG,
                IMAGETYPE_PNG,
                IMAGETYPE_BMP
            ))) {
                $error = 'user file mustbe img';
            }
        }
        if (!empty($_FILES["image"]["error"]) || !empty($_FILES["passport"]["error"])) {
            $error = 'file is big';
        }
    }
    if (empty($error)) {
    	$re_data    = array(
            'user_id' => $fl['user']['user_id'],
            'name' => FL_Secure($_POST['first_name']) . ' ' . FL_Secure($_POST['last_name']),
            'message' => FL_Secure($_POST['text']),
            'time' => time()
        );
        $request_id = FL_RegisterVerificationRequest($re_data);
        if ($request_id && is_numeric($request_id)) {
            $up_data              = array();
            $image_file_info      = array(
                'file' => $_FILES['image']['tmp_name'],
                'size' => $_FILES['image']['size'],
                'name' => $_FILES['image']['name'],
                'type' => $_FILES['image']['type']
            );
            $image_file_upload    = FL_ShareFile($image_file_info);
            $passport_file_info   = array(
                'file' => $_FILES['passport']['tmp_name'],
                'size' => $_FILES['passport']['size'],
                'name' => $_FILES['passport']['name'],
                'type' => $_FILES['passport']['type']
            );
            $passport_file_upload = FL_ShareFile($passport_file_info);
            if (!empty($image_file_upload) && $passport_file_upload) {
                $up_data['photo']    = $image_file_upload['filename'];
                $up_data['passport'] = $passport_file_upload['filename'];
                if (FL_UpdateVerificationRequest($request_id, $up_data)) {
                	$response_data  = array(
		                'api_status' => '200',
		                'api_text' => 'success',
		                'api_version' => $api_version,
		                'message' => 'verif request sent'
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
				            'error_text' => 'something went worng'
				        )
				    );
				    
				    header("Content-type: application/json");
				    echo json_encode($response_data);
				    exit();
                }
            } else {
                $response_data       = array(
			        'api_status'     => '404',
			        'errors'         => array(
			            'error_id'   => '4',
			            'error_text' => 'invalid verif request'
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