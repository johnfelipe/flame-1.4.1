<?php
if ($fl['loggedin'] == true) {
    if (empty($_POST['current_password'])) {
        $error = 'current_password can not be empty';
    }
    else if ($fl['user']['password'] != sha1($_POST['current_password'])) {
        $error = 'password dont match';
    }
    if (empty($error)) {
        $delete = FL_DeleteUser($fl['user']['user_id']);
        if ($delete) {
            $response_data  = array(
                'api_status' => '200',
                'api_text' => 'success',
                'api_version' => $api_version,
                'message' => 'your account was deleted'
            );
            header("Content-type: application/json");
            echo json_encode($response_data);
            exit();
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
