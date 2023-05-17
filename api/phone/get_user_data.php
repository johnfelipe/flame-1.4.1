<?php
if ($fl['loggedin'] == true) {
	$limit = (!empty($_POST['limit']) && is_numeric($_POST['limit']) && $_POST['limit'] > 0 && $_POST['limit'] <= 50) ? FL_Secure($_POST['limit']) : 20;
	$offset = (!empty($_POST['offset']) && is_numeric($_POST['offset']) && $_POST['offset'] > 0) ? FL_Secure($_POST['offset']) : 0;
	$user_data = $fl['user'];
	$type = 'news';
	if (!empty($_POST['user_id']) && is_numeric($_POST['user_id']) && $_POST['user_id'] > 0) {
		$user_data = FL_UserData(FL_Secure($_POST['user_id']));
	}
	if (!empty($_POST['type']) && in_array($_POST['type'], array('news','lists','polls','videos','music','quiz'))) {
		$type = FL_Secure($_POST['type']);
	}
	unset($user_data['password']);
	unset($user_data['email_code']);
	$data = $user_data;
	$function_name           = 'FL_GetNews';
    $table                   = T_NEWS;
    switch ($type) {
        case 'news':
            $function_name   = 'FL_GetNews';
            break;
        case 'lists':
            $function_name   = 'FL_GetLists';
            $table           = T_LISTS;
            break;
        case 'polls':
            $function_name   = 'FL_GetPolls';
            $table           = T_POLLS_PAGES;
            break;
        case 'videos':
            $function_name   = 'FL_GetVideos';
            $table           = T_VIDEOS;
            break;
        case 'music':
            $function_name   = 'FL_GetMusic';
            $table           = T_MUSIC;
            break;
        case 'quiz':
            $function_name   = 'FL_GetQuizzes';
            $table           = T_QUIZZES;
            break;
    }
    

    $fetch_latest_data_array = array(
        'table'              => $table,
        'column'             => 'id',
        'limit'              => $limit,
        'user_data'          => 'public',
        'order'              => array(
            'type'           => 'desc',
            'column'         => 'id'
        ),
        'where'              => array(
            array(
                'column'     => 'active',
                'value'      => '1',
                'mark'       => '='
            ),
            array(
                'column'     => 'user_id',
                'value'      => $user_data['user_id'],
                'mark'       => '='
            ),
        ),
        'final_data'         => array(
            array(
             'function_name' => $function_name,
             'column'        => 'id'
            )
        )
    );
    if ($offset > 0) {
    	$fetch_latest_data_array['where'][] =   array(
									                'column'     => 'id',
									                'value'      => $offset,
									                'mark'       => '<'
									            );
    }
    $posts                   = FL_FetchDataFromDB($fetch_latest_data_array);
    $data['posts'] = array();
    foreach ($posts as $key => $value) {
    	unset($value['publisher']['password']); 
    	unset($value['publisher']['email_code']); 
        $value['is_favorited'] = $db->where('post_id',$value['id'])->where('user_id',$user_data['user_id'])->where('page',$type)->getValue(T_FAV,"COUNT(*)");
    	$data['posts'][] = $value;
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
            'error_id'   => '1',
            'error_text' => 'not loggedin'
        )
    );
    
    header("Content-type: application/json");
    echo json_encode($response_data);
    exit();
}