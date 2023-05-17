<?php
if ($fl['loggedin'] == true) {
	$types = array('insert','get');
	$post_types = array('news', 'lists', 'music', 'videos', 'polls','quiz');
	$limit = (!empty($_POST['limit']) && is_numeric($_POST['limit']) && $_POST['limit'] > 0 && $_POST['limit'] <= 50) ? FL_Secure($_POST['limit']) : 20;
	$offset = (!empty($_POST['offset']) && is_numeric($_POST['offset']) && $_POST['offset'] > 0) ? FL_Secure($_POST['offset']) : 0;
	if (!empty($_POST['type']) && in_array($_POST['type'], $types)) {
		if ($_POST['type'] == 'insert') {
			if (!empty($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0 && !empty($_POST['post_type']) && in_array($_POST['post_type'], $post_types)) {
				$id = FL_Secure($_POST['id']);
				$page = FL_Secure($_POST['post_type']);
				$table = T_NEWS;
				switch ($page) {
			        case 'news':
			            $table = T_NEWS;
			            break;
			        case 'lists':
			            $table = T_LISTS;
			            break;
			        case 'polls':
			            $table = T_POLLS_PAGES;
			            break;
			        case 'videos':
			            $table = T_VIDEOS;
			            break;
			        case 'music':
			            $table = T_MUSIC;
			            break;
			        case 'quiz':
			            $table = T_QUIZZES;
			            break;
			    }
				$post = $db->where('id',$id)->getOne($table);
				if (!empty($post)) {
					$is_fav = $db->where('post_id',$id)->where('user_id',$fl['user']['user_id'])->where('page',$page)->getValue(T_FAV,"COUNT(*)");
					if ($is_fav > 0) {
						$db->where('post_id',$id)->where('user_id',$fl['user']['user_id'])->where('page',$page)->delete(T_FAV);
						$response_data  = array(
			                'api_status' => '200',
			                'api_text' => 'success',
			                'api_version' => $api_version,
			                'message' => 'removed from favorite',
			                'code' => 0
			            );
			            header("Content-type: application/json");
			            echo json_encode($response_data);
			            exit();
					}
					else{
						$db->insert(T_FAV,array('post_id' => $id,
							                    'user_id' => $fl['user']['user_id'],
							                    'page' => $page,
							                    'time' => time()));
						$response_data  = array(
			                'api_status' => '200',
			                'api_text' => 'success',
			                'api_version' => $api_version,
			                'message' => 'added to favorite',
			                'code' => 1
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
			            'error_text' => 'id , post_type can not be empty'
			        )
			    );
			    
			    header("Content-type: application/json");
			    echo json_encode($response_data);
			    exit();
			}
		}
		elseif ($_POST['type'] == 'get') {
			$data = array();
			$fav = $db->where('user_id',$fl['user']['user_id'])->get(T_FAV);
			if (!empty($fav)) {
				foreach ($fav as $key => $value) {
					$post_data   = FL_GetPost($value->post_id, 1, $value->page);
					unset($post_data['publisher']['password']);
					unset($post_data['publisher']['email_code']);
					$data[] = $post_data;
				}
			}
			$response_data  = array(
		        'api_status' => '200',
		        'api_text' => 'success',
		        'api_version' => $api_version,
		        'data' => $data
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
            'error_id'   => '1',
            'error_text' => 'not loggedin'
        )
    );
    
    header("Content-type: application/json");
    echo json_encode($response_data);
    exit();
}