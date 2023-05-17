<?php
if ($s == 'create') {
	if (empty($_POST['stream_name'])) {
		$data['message'] = $lang['please_check_details'];
	}
	else{
		$slug               = FL_SlugPost('live video '.$fl['user']['name']);
		$post_id = $db->insert(T_VIDEOS,array('user_id' => $fl['user']['id'],
                                             'type' => 'live',
                                             'title' => 'live video '.$fl['user']['name'],
                                             'short_title' => 'live video '.$fl['user']['name'],
                                             'stream_name' => FL_Secure($_POST['stream_name']),
                                             'registered' => date('Y') . '/' . intval(date('m')),
                                             'active' => '1',
                                             'viewable' => '1',
                                             'category' => '9',
                                             'slug' => $slug,
                                             'time' => time(),
                                             'last_update' => time()));
		$index_id = time();
		$data_inputs = array('entry_title' => 'live video '.$fl['user']['name'],
			                 'entry_video_url' => '',
			                 'entry_video_type' => 'live',
			                 'entry_video_id' => 0,
			                 'entry_video_src' => '');
		$array          = array(
	                    'index_id' => $index_id,
	                    'data_inputs' => $data_inputs,
	                    'type' => 'video',
	                    'entry_page' => 'videos',
	                    'category' => 9,
	                );
		FL_InsertEntries($index_id, $post_id, $array);
        // PT_RunInBackground(array('status' => 200,
        //                          'post_id' => $post_id));

        if ($fl['config']['agora_live_video'] == 1 && !empty($fl['config']['agora_app_id']) && !empty($fl['config']['agora_customer_id']) && !empty($fl['config']['agora_customer_certificate']) && $fl['config']['live_video_save'] == 1) {

            if ($fl['config']['amazone_s3_2'] == 1 && !empty($fl['config']['bucket_name_2']) && !empty($fl['config']['amazone_s3_key_2']) && !empty($fl['config']['amazone_s3_s_key_2']) && !empty($fl['config']['region_2'])) {

                $region_array = array('us-east-1' => 0,'us-east-2' => 1,'us-west-1' => 2,'us-west-2' => 3,'eu-west-1' => 4,'eu-west-2' => 5,'eu-west-3' => 6,'eu-central-1' => 7,'ap-southeast-1' => 8,'ap-southeast-2' => 9,'ap-northeast-1' => 10,'ap-northeast-2' => 11,'sa-east-1' => 12,'ca-central-1' => 13,'ap-south-1' => 14,'cn-north-1' => 15,'us-gov-west-1' => 17);

                if (in_array(strtolower($fl['config']['region_2']),array_keys($region_array) )) {

                    StartCloudRecording(1,$region_array[strtolower($fl['config']['region_2'])],$fl['config']['bucket_name_2'],$fl['config']['amazone_s3_key_2'],$fl['config']['amazone_s3_s_key_2'],$_POST['stream_name'],explode('_', $_POST['stream_name'])[2],$post_id);
                }
                
            }
        }
        //pt_push_channel_notifiations($post_id,'started_live_video');
        $data['status'] = 200;
        $data['post_id'] = $post_id;

	}
	header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($s == 'delete') {
    if (!empty($_POST['post_id']) && is_numeric($_POST['post_id']) && $_POST['post_id'] > 0) {
        $db->where('id',FL_Secure($_POST['post_id']))->where('user_id',$fl['user']['id'])->update(T_VIDEOS,array('live_ended' => 1));
        if ($fl['config']['live_video_save'] == 0) {
            FL_DeletePost(FL_Secure($_POST['post_id']), 'videos');
        }
        else{
            if ($fl['config']['agora_live_video'] == 1 && !empty($fl['config']['agora_app_id']) && !empty($fl['config']['agora_customer_id']) && !empty($fl['config']['agora_customer_certificate']) && $fl['config']['live_video_save'] == 1) {
                $post = $db->where('id',FL_Secure($_POST['post_id']))->getOne(T_VIDEOS);
                if (!empty($post)) {
                    StopCloudRecording(array('resourceId' => $post->agora_resource_id,
                                             'sid' => $post->agora_sid,
                                             'cname' => $post->stream_name,
                                             'post_id' => $post->id,
                                             'uid' => explode('_', $post->stream_name)[2]));
                }
            }
            if ($fl['config']['agora_live_video'] == 1 && $fl['config']['amazone_s3_2'] != 1) {
                try {
                    FL_DeletePost(FL_Secure($_POST['post_id']), 'videos');
                } catch (Exception $e) {
                    
                }
            }
        }
    }
}
if ($s == 'create_thumb') {
    if (!empty($_POST['post_id']) && is_numeric($_POST['post_id']) && $_POST['post_id'] > 0 && !empty($_FILES['thumb'])) {
        $is_post = $db->where('id',FL_Secure($_POST['post_id']))->where('user_id',$fl['user']['id'])->getValue(T_VIDEOS,'COUNT(*)');
        if ($is_post > 0) {
            $fileInfo = array(
                'file' => $_FILES["thumb"]["tmp_name"],
                'name' => $_FILES['thumb']['name'],
                'size' => $_FILES["thumb"]["size"],
                'type' => $_FILES["thumb"]["type"],
                'types' => 'jpeg,png,jpg,gif',
                'crop' => array(
                    'width' => 1076,
                    'height' => 604
                )
            );
            $media    = FL_ShareFile($fileInfo);
            if (!empty($media)) {
                $thumb = $media['filename'];
                if (!empty($thumb)) {
                    $db->where('id',FL_Secure($_POST['post_id']))->where('user_id',$fl['user']['id'])->update(T_VIDEOS,array('image' => $thumb));
                    $data['status'] = 200;
                    header("Content-type: application/json");
                    echo json_encode($data);
                    exit();
                }
            }
        }
    }
}
if ($s == 'check_comments') {
	if (!empty($_POST['post_id']) && is_numeric($_POST['post_id']) && $_POST['post_id'] > 0) {
		$post_id = FL_Secure($_POST['post_id']);
		$post_data = $video_data = $fl['get_video'] = $db->where('id',$post_id)->getOne(T_VIDEOS);
		if (!empty($post_data)) {
            if ($post_data->live_ended == 0) {
                //if ($_POST['page'] == 'story') {
                    $user_comment = $db->where('news_id',$post_id)->where('user_id',$fl['user']['id'])->getOne(T_COMMENTS);
                    if (!empty($user_comment)) {
                        $db->where('id',$user_comment->id,'>');
                    }
                //}
                if (!empty($_POST['ids'])) {
                    $ids = array();
                    foreach ($_POST['ids'] as $key => $one_id) {
                        $ids[] = FL_Secure($one_id);
                    }
                    $db->where('id',$ids,'NOT IN')->where('id',end($ids),'>');
                }
                //if ($_POST['page'] == 'story') {
                    $db->where('user_id',$fl['user']['id'],'!=');
                //}
				$comments = $db->where('news_id',$post_id)->where('text','','!=')->get(T_COMMENTS);
				$html = '';
                $count = 0;
				foreach ($comments as $key => $get_comment) {
					if (!empty($get_comment->text)) {
                        $user_data   = FL_UserData($get_comment->user_id);
                        // $pt->is_comment_owner = false;
                        // $pt->is_verified      = ($user_data->verified == 1) ? true : false;
                        // $pt->video_owner      = false;

                        // if ($user->id == $get_comment->user_id) {
                        //     $pt->is_comment_owner = true;
                        // }

                        // if ($video_data->user_id == $user->id) {
                        //     $pt->video_owner = true;
                        // }
                        // $get_comment->text = PT_Duration($get_comment->text);

                        $html     .= FL_LoadPage('comment/live_comment', array(
                            'ID' => $get_comment->id,
                            'TEXT' => $get_comment->text,
                            'TIME' => FL_Time_Elapsed_String($get_comment->time),
                            'USER_DATA' => $user_data,
                            'LIKES' => 0,
                            'DIS_LIKES' => 0,
                            'LIKED' => '',
                            'DIS_LIKED' => '',
                            'LIKED_ATTR' => '',
                            'COMM_REPLIES' => '',
                            'VID_ID' => $get_comment->id,
                            'USER_VERIFIED'     => ($user_data['verified'] == 1) ? '<span class="verified-icon"><i class="fa fa-check-circle"></i></span>' : '',
                        ));
           //              $html     .= FL_LoadPage("comment/live_comment", array(
							    // 'COMM_ID'           => $get_comment->id,
							    // 'POST_ID'           => $get_comment->id,
							    // 'STORY_PAGE'        => '',
							    // 'COMM_TEXT'         => $get_comment->text,
							    // 'COMM_TIME'         => FL_Time_Elapsed_String($get_comment->time),
							    // 'COMM_USER_NAME'    => $user_data['name'],
							    // 'USER_VERIFIED'     => ($user_data['verified'] == 1) ? '<span class="verified-icon"><i class="fa fa-check-circle"></i></span>' : '',
							    // 'COMM_USER_URL'     => $user_data['url'],
							    // 'COMM_USER_AVATAR'  => $user_data['avatar'],
							    // 'COMM_REPLIES'      => '',
							    // ));
						$count = $count + 1;
						if ($count == 4) {
	                      break;
	                    }
					}
				}


                
                $word = $fl['lang']['offline'];
                if (!empty($post_data->live_time) && $post_data->live_time >= (time() - 10)) {
                    //$db->where('post_id',$post_id)->where('time',time()-6,'<')->update(T_LIVE_SUB,array('is_watching' => 0));
                    $word = $fl['lang']['live'];
                    $count = $db->where('post_id',$post_id)->where('time',time()-6,'>=')->getValue(T_LIVE_SUB,'COUNT(*)');

                    if ($fl['user']['id'] == $post_data->user_id) {
                        $joined_users = $db->where('post_id',$post_id)->where('time',time()-6,'>=')->where('is_watching',0)->get(T_LIVE_SUB);
                        $joined_ids = array();
                        if (!empty($joined_users)) {
                            foreach ($joined_users as $key => $value) {
                                $joined_ids[] = $value->user_id;
                                $user_data   = FL_UserData($value->user_id);
                                $html     .= FL_LoadPage('comment/live_comment', array(
                                    'ID' => '',
                                    'TEXT' => $fl['lang']['joined_live_video'],
                                    'TIME' => '',
                                    'USER_DATA' => $user_data,
                                    'LIKES' => 0,
                                    'DIS_LIKES' => 0,
                                    'LIKED' => '',
                                    'DIS_LIKED' => '',
                                    'LIKED_ATTR' => '',
                                    'COMM_REPLIES' => '',
                                    'VID_ID' => '',
                                    'USER_VERIFIED'     => ($user_data['verified'] == 1) ? '<span class="verified-icon"><i class="fa fa-check-circle"></i></span>' : '',
                                ));
           //                      $html     .= FL_LoadPage("comment/live_comment", array(
							    // 'COMM_ID'           => '',
							    // 'POST_ID'           => '',
							    // 'STORY_PAGE'        => '',
							    // 'COMM_TEXT'         => $fl['lang']['joined_live_video'],
							    // 'COMM_TIME'         => '',
							    // 'COMM_USER_NAME'    => $user_data['name'],
							    // 'USER_VERIFIED'     => ($user_data['verified'] == 1) ? '<span class="verified-icon"><i class="fa fa-check-circle"></i></span>' : '',
							    // 'COMM_USER_URL'     => $user_data['url'],
							    // 'COMM_USER_AVATAR'  => $user_data['avatar'],
							    // 'COMM_REPLIES'      => '',
							    // ));


                                // $wo['comment'] = array('id' => '',
                                //                        'text' => 'joined live video');
                                // $user_data = Wo_UserData($value->user_id);
                                // if (!empty($user_data)) {
                                //     $wo['comment']['publisher'] = $user_data;
                                //     $html .= Wo_LoadPage('story/includes/live_comment');
                                // }
                            }
                            if (!empty($joined_ids)) {
                                $db->where('post_id',$post_id)->where('user_id',$joined_ids,'IN')->update(T_LIVE_SUB,array('is_watching' => 1));
                            }
                        }

                        $left_users = $db->where('post_id',$post_id)->where('time',time()-6,'<')->where('is_watching',1)->get(T_LIVE_SUB);
                        $left_ids = array();
                        if (!empty($left_users)) {
                            foreach ($left_users as $key => $value) {
                                $left_ids[] = $value->user_id;
                                $user_data   = FL_UserData($value->user_id);
                                $fl['is_verified']      = ($user_data['verified'] == 1) ? true : false;
                                $html     .= FL_LoadPage('comment/live_comment', array(
                                    'ID' => '',
                                    'TEXT' => $fl['lang']['left_live_video'],
                                    'TIME' => '',
                                    'USER_DATA' => $user_data,
                                    'LIKES' => 0,
                                    'DIS_LIKES' => 0,
                                    'LIKED' => '',
                                    'DIS_LIKED' => '',
                                    'LIKED_ATTR' => '',
                                    'COMM_REPLIES' => '',
                                    'VID_ID' => '',
                                    'USER_VERIFIED'     => ($user_data['verified'] == 1) ? '<span class="verified-icon"><i class="fa fa-check-circle"></i></span>' : '',
                                ));
           //                      $html     .= FL_LoadPage("comment/live_comment", array(
							    // 'COMM_ID'           => '',
							    // 'POST_ID'           => '',
							    // 'STORY_PAGE'        => '',
							    // 'COMM_TEXT'         => $fl['lang']['left_live_video'],
							    // 'COMM_TIME'         => '',
							    // 'COMM_USER_NAME'    => $user_data['name'],
							    // 'USER_VERIFIED'     => ($user_data['verified'] == 1) ? '<span class="verified-icon"><i class="fa fa-check-circle"></i></span>' : '',
							    // 'COMM_USER_URL'     => $user_data['url'],
							    // 'COMM_USER_AVATAR'  => $user_data['avatar'],
							    // 'COMM_REPLIES'      => '',
							    // ));


                                // $wo['comment'] = array('id' => '',
                                //                        'text' => 'left live video');
                                // $user_data = Wo_UserData($value->user_id);
                                // if (!empty($user_data)) {
                                //     $wo['comment']['publisher'] = $user_data;
                                //     $html .= Wo_LoadPage('story/includes/live_comment');
                                // }
                            }
                            if (!empty($left_ids)) {
                                $db->where('post_id',$post_id)->where('user_id',$left_ids,'IN')->delete(T_LIVE_SUB);
                            }
                        }
                    }
                }
                $still_live = 'offline';
                if (!empty($post_data) && $post_data->live_time >= (time() - 10)){
                    $still_live = 'live';
                }
                $data = array(
                    'status' => 200,
                    'html' => $html,
                    'count' => $count,
                    'word' => $word,
                    'still_live' => $still_live
                );
                
                // Wo_RunInBackground(array(
                //     'status' => 200,
                //     'html' => $html,
                //     'count' => $count,
                //     'word' => $word,
                //     'still_live' => $still_live
                // ));
                
                if ($fl['user']['id'] == $post_data->user_id) {
                    if ($_POST['page'] == 'live') {
                        $time = time();
                        $db->where('id',$post_id)->update(T_VIDEOS,array('live_time' => $time));
                    }
                }
                else{
                    if (!empty($post_data->live_time) && $post_data->live_time >= (time() - 10) && $_POST['page'] == 'videos') {
                        $is_watching = $db->where('user_id',$fl['user']['id'])->where('post_id',$post_id)->getValue(T_LIVE_SUB,'COUNT(*)');
                        if ($is_watching > 0) {
                            $db->where('user_id',$fl['user']['id'])->where('post_id',$post_id)->update(T_LIVE_SUB,array('time' => time()));
                        }
                        else{
                            $db->insert(T_LIVE_SUB,array('user_id' => $fl['user']['id'],
                                                         'post_id' => $post_id,
                                                         'time' => time(),
                                                         'is_watching' => 0));
                        }
                    }
                }
            }
            else{
                $data['message'] = $fl['lang']['please_check_details'];
            }
            
		}
		else{
			$data['message'] = $fl['lang']['please_check_details'];
            $data['removed'] = 'yes';
		}
	}
	else{
		$data['message'] = $fl['lang']['please_check_details'];
	}
	header("Content-type: application/json");
    echo json_encode($data);
    exit();
}