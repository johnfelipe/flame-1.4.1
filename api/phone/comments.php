<?php
if ($fl['loggedin'] == true) {
	$types = array('insert','insert_reply','delete_comment','delete_reply','get_post_comments','get_comment_reply');
	$post_types = array('news', 'lists', 'music', 'videos', 'polls','quiz');
	$limit = (!empty($_POST['limit']) && is_numeric($_POST['limit']) && $_POST['limit'] > 0 && $_POST['limit'] <= 50) ? FL_Secure($_POST['limit']) : 20;
	$offset = (!empty($_POST['offset']) && is_numeric($_POST['offset']) && $_POST['offset'] > 0) ? FL_Secure($_POST['offset']) : 0;
	if (!empty($_POST['type']) && in_array($_POST['type'], $types)) {
		if ($_POST['type'] == 'insert') {
			if (!empty($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0 && !empty($_POST['post_type']) && in_array($_POST['post_type'], $post_types) && !empty($_POST['text'])) {
				if (strlen($_POST['text']) <= 600) {
					$id      = FL_Secure($_POST['id']);
		            $text    = FL_Secure($_POST['text']);
		            $page    = FL_Secure($_POST['post_type']);
		            $re_data = array(
		                'user_id' => $fl['user']['user_id'],
		                'news_id' => $id,
		                'page' => $page,
		                'text' => $text,
		                'time' => time()
		            );
		            $comm_id = FL_RegisterComment($re_data);
		            if ($comm_id && is_numeric($comm_id)) {
		                $comment = FL_CommentData($comm_id);
		                unset($comment['user_data']['password']);
		                unset($comment['user_data']['email_code']);
		                $response_data  = array(
					        'api_status' => '200',
					        'api_text' => 'success',
					        'api_version' => $api_version,
					        'data' => $comment
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
				            'error_text' => 'text length can not be more than 600'
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
			            'error_text' => 'id , text , post_type can not be empty'
			        )
			    );
			    
			    header("Content-type: application/json");
			    echo json_encode($response_data);
			    exit();
			}
		}
		elseif ($_POST['type'] == 'insert_reply') {
			if (!empty($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0 && !empty($_POST['post_id']) && is_numeric($_POST['post_id']) && $_POST['post_id'] > 0 && !empty($_POST['post_type']) && in_array($_POST['post_type'], $post_types) && !empty($_POST['text'])) {
				if (strlen($_POST['text']) <= 600) {
					$id      = FL_Secure($_POST['id']);
		            $text    = FL_Secure($_POST['text']);
		            $page    = FL_Secure($_POST['post_type']);
		            $news_id    = FL_Secure($_POST['post_id']);

		            $re_data = array(
		                'user_id' => $fl['user']['user_id'],
		                'comment' => $id,
		                'news_id' => $news_id,
		                'page' => $page,
		                'text' => $text,
		                'time' => time()
		            );
		            $comm_id = FL_RegisterReply($re_data);
		            if ($comm_id && is_numeric($comm_id)) {
		            	$comment = FL_CommentReplyData($comm_id);
		                unset($comment['user_data']['password']);
		                unset($comment['user_data']['email_code']);
		                $response_data  = array(
					        'api_status' => '200',
					        'api_text' => 'success',
					        'api_version' => $api_version,
					        'data' => $comment
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
				            'error_text' => 'text length can not be more than 600'
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
			            'error_text' => 'id , text , post_id , post_type can not be empty'
			        )
			    );
			    
			    header("Content-type: application/json");
			    echo json_encode($response_data);
			    exit();
			}
		}
		elseif ($_POST['type'] == 'delete_comment') {
			if (!empty($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0 && !empty($_POST['post_type']) && in_array($_POST['post_type'], $post_types)) {
				$id   = FL_Secure($_POST['id']);
	            $page = FL_Secure($_POST['post_type']);
	            if (FL_DeleteComment($id, $page)) {
	                $response_data  = array(
		                'api_status' => '200',
		                'api_text' => 'success',
		                'api_version' => $api_version,
		                'message' => 'your comment was deleted'
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
			            'error_text' => 'id , post_type can not be empty'
			        )
			    );
			    
			    header("Content-type: application/json");
			    echo json_encode($response_data);
			    exit();
			}
		}
		elseif ($_POST['type'] == 'delete_reply') {
			if (!empty($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0) {
				$id   = FL_Secure($_POST['id']);
				if (FL_DeleteReply($id)) {
		            $response_data  = array(
		                'api_status' => '200',
		                'api_text' => 'success',
		                'api_version' => $api_version,
		                'message' => 'your comment was deleted'
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
			            'error_text' => 'id can not be empty'
			        )
			    );
			    
			    header("Content-type: application/json");
			    echo json_encode($response_data);
			    exit();
			}
		}
		elseif ($_POST['type'] == 'get_post_comments') {
			if (!empty($_POST['post_id']) && is_numeric($_POST['post_id']) && $_POST['post_id'] > 0 && !empty($_POST['post_type']) && in_array($_POST['post_type'], $post_types)) {
				$id   = FL_Secure($_POST['post_id']);
	            $page = FL_Secure($_POST['post_type']);
	            $data = array();
	            $comments = FL_GetStoryComments(array('post_id' => $id,
	                                                  'page' => $page,
	                                                  'offset' => $offset,
	                                                  'limit' => $limit));
	            foreach ($comments as $key => $value) {
	            	unset($value['user_data']['password']);
	            	unset($value['user_data']['email_code']);
	            	$value['replies_count'] = 0;
	            	if (!empty($value['replies'])) {
	            		$value['replies_count'] = count($value['replies']);
	            	}
	            	unset($value['replies']);
	            	$data[] = $value;
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
	        else{
	        	$response_data       = array(
			        'api_status'     => '404',
			        'errors'         => array(
			            'error_id'   => '3',
			            'error_text' => 'post_id , post_type can not be empty'
			        )
			    );
			    
			    header("Content-type: application/json");
			    echo json_encode($response_data);
			    exit();
	        }
			
		}
		elseif ($_POST['type'] == 'get_comment_reply') {
			if (!empty($_POST['comment_id']) && is_numeric($_POST['comment_id']) && $_POST['comment_id'] > 0) {
				$id   = FL_Secure($_POST['comment_id']);
				$data = array();
				$comments = FL_GetCommReplies($id,$limit,$offset);
				foreach ($comments as $key => $value) {
	            	unset($value['user_data']['password']);
	            	unset($value['user_data']['email_code']);
	            	$data[] = $value;
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
			else{
				$response_data       = array(
			        'api_status'     => '404',
			        'errors'         => array(
			            'error_id'   => '3',
			            'error_text' => 'comment_id can not be empty'
			        )
			    );
			    
			    header("Content-type: application/json");
			    echo json_encode($response_data);
			    exit();
			}
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