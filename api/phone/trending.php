<?php
if ($fl['loggedin'] == true) {
	$data = array('trending_list' => array(),
                  'trending_news' => array(),
                  'trending_poll' => array(),
                  'trending_quiz' => array());
	$fetch_latest_lists_data_array = array(
	    'table' => T_LISTS,
	    'column' => 'id',
	    'limit' => 3,
	    'order' => array(
	        'type' => 'desc',
	        'column' => 'views'
	    ),
	    'where' => array(
	        array(
	            'column' => 'active',
	            'value' => '1',
	            'mark' => '='
	        ),
	        array(
	            'column' => 'time',
	            'value' => time() - 604800,
	            'mark' => '>'
	        )
	    ),
	    'final_data' => array(
	        array(
	            'function_name' => 'FL_GetLists',
	            'column' => 'id',
	            'name' => 'list'
	        )
	    )
	);
	$fl['trending_list'] = FL_FetchDataFromDB($fetch_latest_lists_data_array);
	if (!empty($fl['trending_list'])) {
		foreach ($fl['trending_list'] as $key => $value) {
			unset($value['list']['publisher']['password']);
			unset($value['list']['publisher']['email_code']);
			$value['list']['is_favorited'] = $db->where('post_id',$value['list']['id'])->where('user_id',$fl['user']['user_id'])->where('page','videos')->getValue(T_FAV,"COUNT(*)");
			$data['trending_list'][] = $value;
		}
	}

	$fetch_latest_news_data_array = array(
	    'table' => T_NEWS,
	    'column' => 'id',
	    'limit' => 3,
	    'order' => array(
	        'type' => 'desc',
	        'column' => 'views'
	    ),
	    'where' => array(
	        array(
	            'column' => 'active',
	            'value' => '1',
	            'mark' => '='
	        ),
	        array(
	            'column' => 'time',
	            'value' => time() - 604800,
	            'mark' => '<'
	        )
	    ),
	    'final_data' => array(
	        array(
	            'function_name' => 'FL_GetNews',
	            'column' => 'id',
	            'name' => 'news'
	        )
	    )
	);
	$fl['trending_news'] = FL_FetchDataFromDB($fetch_latest_news_data_array);
	if (!empty($fl['trending_news'])) {
		foreach ($fl['trending_news'] as $key => $value) {
			unset($value['news']['publisher']['password']);
			unset($value['news']['publisher']['email_code']);
			$value['news']['is_favorited'] = $db->where('post_id',$value['news']['id'])->where('user_id',$fl['user']['user_id'])->where('page','news')->getValue(T_FAV,"COUNT(*)");
			$data['trending_news'][] = $value;
		}
	}

	$fetch_latest_polls_data_array = array(
	    'table' => T_POLLS_PAGES,
	    'column' => 'id',
	    'limit' => 3,
	    'order' => array(
	        'type' => 'desc',
	        'column' => 'views'
	    ),
	    'where' => array(
	        array(
	            'column' => 'active',
	            'value' => '1',
	            'mark' => '='
	        ),
	        array(
	            'column' => 'time',
	            'value' => time() - 604800,
	            'mark' => '>'
	        )
	    ),
	    'final_data' => array(
	        array(
	            'function_name' => 'FL_GetPolls',
	            'column' => 'id',
	            'name' => 'poll'
	        )
	    )
	);
	$fl['trending_poll'] = FL_FetchDataFromDB($fetch_latest_polls_data_array);
	if (!empty($fl['trending_poll'])) {
		foreach ($fl['trending_poll'] as $key => $value) {
			unset($value['poll']['publisher']['password']);
			unset($value['poll']['publisher']['email_code']);
			$value['poll']['is_favorited'] = $db->where('post_id',$value['poll']['id'])->where('user_id',$fl['user']['user_id'])->where('page','polls')->getValue(T_FAV,"COUNT(*)");
			$data['trending_poll'][] = $value;
		}
	}

	$fetch_latest_quizzes_data_array = array(
	    'table' => T_QUIZZES,
	    'column' => 'id',
	    'limit' => 3,
	    'order' => array(
	        'type' => 'desc',
	        'column' => 'views'
	    ),
	    'where' => array(
	        array(
	            'column' => 'active',
	            'value' => '1',
	            'mark' => '='
	        ),
	        array(
	            'column' => 'time',
	            'value' => time() - 604800,
	            'mark' => '>'
	        )
	    ),
	    'final_data' => array(
	        array(
	            'function_name' => 'FL_GetQuizzes',
	            'column' => 'id',
	            'name' => 'quiz'
	        )
	    )
	);

	$fl['trending_quiz'] = FL_FetchDataFromDB($fetch_latest_quizzes_data_array);
	if (!empty($fl['trending_quiz'])) {
		foreach ($fl['trending_quiz'] as $key => $value) {
			unset($value['quiz']['publisher']['password']);
			unset($value['quiz']['publisher']['email_code']);
			$value['quiz']['is_favorited'] = $db->where('post_id',$value['quiz']['id'])->where('user_id',$fl['user']['user_id'])->where('page','quiz')->getValue(T_FAV,"COUNT(*)");
			$data['trending_quiz'][] = $value;
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