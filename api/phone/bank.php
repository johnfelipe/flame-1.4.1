<?php
if ($fl['loggedin'] == true) {
	$types = array('pro');
	if (!empty($_POST['type']) && in_array($_POST['type'], $types) && !empty($_FILES["thumbnail"])) {
		$amount = 0;
		if ($_POST['type'] == 'pro') {
			$amount = intval($fl['config']['pro_pkg_price']);
		}
		$type = FL_Secure($_POST['type']);
	    $fileInfo      = array(
	        'file' => $_FILES["thumbnail"]["tmp_name"],
	        'name' => $_FILES['thumbnail']['name'],
	        'size' => $_FILES["thumbnail"]["size"],
	        'type' => $_FILES["thumbnail"]["type"],
	        'types' => 'jpeg,jpg,png,bmp,gif'
	    );
	    $media         = FL_ShareFile($fileInfo);
	    $mediaFilename = $media['filename'];
	    if (!empty($mediaFilename)) {
	        $insert_id = InsertBankTrnsfer(array('user_id' => $fl['user']['user_id'],
	                                               'description' => $type,
	                                               'price'       => $amount,
	                                               'receipt_file' => $mediaFilename,
	                                               'mode'         => $type));
	        if (!empty($insert_id)) {
	            $response_data  = array(
	                'api_status' => '200',
	                'api_text' => 'success',
	                'api_version' => $api_version,
	                'message' => 'Your request has been successfully sent, we will notify you once it&#039;s approved'
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
	    else{
	        $response_data       = array(
		        'api_status'     => '404',
		        'errors'         => array(
		            'error_id'   => '3',
		            'error_text' => 'file not supported'
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
	            'error_text' => 'type , thumbnail can not be empty'
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