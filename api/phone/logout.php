<?php
if ($fl['loggedin'] == true) {
	if (!empty($_POST['access_token'])) {
		$db->where('user_id',$fl['user']['user_id'])->where('session_id',$_POST['access_token'])->delete(T_SESSIONS);
	}
    
    $response_data  = array(
        'api_status' => '200',
        'api_text' => 'success',
        'api_version' => $api_version,
        'message' => 'Successfully logged out'
    );
    header("Content-type: application/json");
    echo json_encode($response_data);
    exit();
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
