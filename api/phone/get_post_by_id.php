<?php
if ($fl['loggedin'] == true) {
	$post_types = array('news', 'lists', 'music', 'videos', 'polls','quiz');
	if (!empty($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0 && !empty($_POST['type']) && in_array($_POST['type'], $post_types)) {
		$post_type = FL_Secure($_POST['type']);
		$post_id = FL_Secure($_POST['id']);
		$post_data   = FL_GetPost($post_id, 1, $post_type);
		if (!empty($post_data)) {
			unset($post_data['publisher']['password']);
			unset($post_data['publisher']['email_code']);
			$post_data['is_favorited'] = $db->where('post_id',$post_data['id'])->where('user_id',$post_data['publisher']['user_id'])->where('page',$post_type)->getValue(T_FAV,"COUNT(*)");
			if (!empty($_POST['view']) && $_POST['view'] == 1) {
				FL_UpdateViews($post_type, $post_data['id']);
			}
			$response_data  = array(
		        'api_status' => '200',
		        'api_text' => 'success',
		        'api_version' => $api_version,
		        'data' => $post_data
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
		            'error_text' => 'post not found'
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
	            'error_text' => 'type , id can not be empty'
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