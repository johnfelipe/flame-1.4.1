<?php
if ($fl['loggedin'] == true) {
	$types = array('news', 'lists', 'music', 'videos', 'polls','quiz');

	if (!empty($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0 && !empty($_POST['type']) && in_array($_POST['type'], $types)) {
		$type = FL_Secure($_POST['type']);
		$id = FL_Secure($_POST['id']);
		$is_post_owner = FL_IsPostOwner($id, $type);
		if ($is_post_owner) {
			$post = FL_GetPost($id, 0, $type);
			if (!empty($post)) {
				$delete_post = FL_DeletePost($id, $type);
		        if ($delete_post) {
		            $response_data  = array(
		                'api_status' => '200',
		                'api_text' => 'success',
		                'api_version' => $api_version,
		                'message' => 'your post was deleted'
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
			else{
				$response_data       = array(
			        'api_status'     => '404',
			        'errors'         => array(
			            'error_id'   => '4',
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
		            'error_id'   => '3',
		            'error_text' => 'you are not the post owner'
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