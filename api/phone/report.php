<?php
if ($fl['loggedin'] == true) {
	$fl_pages = array(
	    'news',
	    'polls',
	    'videos',
	    'lists',
	    'music',
	    'quiz'
	);

	if (!empty($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0 && !empty($_POST['type']) && in_array($_POST['type'], $fl_pages)) {

		$id          = $_POST['id'];
        $table       = T_REPORTS;
        $page        = $_POST['type']; 
        $user_id     = $fl['user']['user_id'];
        $where       = array();
        $query_cols  = array('`post_id`' => $id,'`user_id`' => $user_id,'`type`' => $page);
        foreach ($query_cols as $col => $col_val) {
            $where[] = array(
                'column' => $col,
                'value'  => $col_val,
                'mark'   => '=',
            );
        }
        $response_data  = array(
            'api_status' => '200',
            'api_text' => 'success',
            'api_version' => $api_version
        );
        
        $user_reports = FL_CountData($where,$table);
        if (is_numeric($user_reports) && $user_reports > 0) {
            FL_DeleteData($where,$table);
            $response_data['code'] = 0;
            $response_data['message'] = 'report removed';
        }
        else{
            $re_data = array(
                'post_id' => $id,
                'user_id' => $user_id,
                'type'    => $page,
                'time'    => time()
            );
            FL_InsertData($re_data,$table);
            $response_data['code'] = 1;
            $response_data['message'] = 'reported';
        }
        header("Content-type: application/json");
        echo json_encode($response_data);
        exit();
	}
	else{
		$response_data       = array(
	        'api_status'     => '404',
	        'errors'         => array(
	            'error_id'   => '2',
	            'error_text' => 'id , type can not be empty'
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