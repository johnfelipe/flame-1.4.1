<?php
if ($fl['loggedin'] == true) {
	$data = array('lists' => array(),
                  'music' => array(),
                  'polls' => array(),
                  'news' => array(),
                  'videos' => array());

	$fetch_top_lists_data_array    = array(
	    'table' => T_LISTS,
	    'column' => 'id',
	    'limit' => 1,
	    'order' => array(
	        'type' => 'rand',
	        'column' => 'id'
	    ),
	    'where' => array(
	        array(
	            'column' => 'active',
	            'value' => '1',
	            'mark' => '='
	        ),
	        array(
	            'column' => 'featured',
	            'value' => '1',
	            'mark' => '='
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

	$top_lists = FL_FetchDataFromDB($fetch_top_lists_data_array);

	if (empty($top_lists)) {
	    unset($fetch_top_lists_data_array['where'][1]);
	    $top_lists = FL_FetchDataFromDB($fetch_top_lists_data_array);

	}
	if (!empty($top_lists) && !empty($top_lists[0])) {
		unset($top_lists[0]['list']['publisher']['password']);
		unset($top_lists[0]['list']['publisher']['email_code']);
		$top_lists[0]['list']['is_favorited'] = $db->where('post_id',$top_lists[0]['list']['id'])->where('user_id',$fl['user']['user_id'])->where('page','lists')->getValue(T_FAV,"COUNT(*)");
		$data['lists'] = $top_lists;
	}
	

	$fetch_top_music_data_array    = array(
	    'table' => T_MUSIC,
	    'column' => 'id',
	    'limit' => 1,
	    'order' => array(
	        'type' => 'rand',
	        'column' => 'id'
	    ),
	    'where' => array(
	        array(
	            'column' => 'active',
	            'value' => '1',
	            'mark' => '='
	        ),
	        array(
	            'column' => 'featured',
	            'value' => '1',
	            'mark' => '='
	        )
	    ),
	    'final_data' => array(
	        array(
	            'function_name' => 'FL_GetMusic',
	            'column' => 'id',
	            'name' => 'music'
	        )
	    )
	);



	$top_music = FL_FetchDataFromDB($fetch_top_music_data_array);

	if (empty($top_music)) {
	    unset($fetch_top_music_data_array['where'][1]);
	    $top_music = FL_FetchDataFromDB($fetch_top_music_data_array);
	}
	if (!empty($top_music) && !empty($top_music[0])) {
		unset($top_music[0]['music']['publisher']['password']);
		unset($top_music[0]['music']['publisher']['email_code']);
		$top_music[0]['music']['is_favorited'] = $db->where('post_id',$top_music[0]['music']['id'])->where('user_id',$fl['user']['user_id'])->where('page','music')->getValue(T_FAV,"COUNT(*)");
		$data['music'] = $top_music;
	}

	$fetch_top_news_data_array    = array(
	    'table' => T_NEWS,
	    'column' => 'id',
	    'limit' => 1,
	    'order' => array(
	        'type' => 'rand',
	        'column' => 'id'
	    ),
	    'where' => array(
	        array(
	            'column' => 'active',
	            'value' => '1',
	            'mark' => '='
	        ),
	        array(
	            'column' => 'featured',
	            'value' => '1',
	            'mark' => '='
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

	$top_news = FL_FetchDataFromDB($fetch_top_news_data_array);

	if (empty($top_news)) {
	    unset($fetch_top_news_data_array['where'][1]);
	    $top_news = FL_FetchDataFromDB($fetch_top_news_data_array);
	}
	if (!empty($top_news) && !empty($top_news[0])) {
		unset($top_news[0]['news']['publisher']['password']);
		unset($top_news[0]['news']['publisher']['email_code']);
		$top_news[0]['news']['is_favorited'] = $db->where('post_id',$top_news[0]['news']['id'])->where('user_id',$fl['user']['user_id'])->where('page','news')->getValue(T_FAV,"COUNT(*)");
		$data['news'] = $top_news;
	}

	$fetch_top_polls_data_array    = array(
	    'table' => T_POLLS_PAGES,
	    'column' => 'id',
	    'limit' => 1,
	    'order' => array(
	        'type' => 'rand',
	        'column' => 'id'
	    ),
	    'where' => array(
	        array(
	            'column' => 'active',
	            'value' => '1',
	            'mark' => '='
	        ),
	        array(
	            'column' => 'featured',
	            'value' => '1',
	            'mark' => '='
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

	$top_polls = FL_FetchDataFromDB($fetch_top_polls_data_array);

	if (empty($top_polls)) {
	    unset($fetch_top_polls_data_array['where'][1]);
	    $top_polls = FL_FetchDataFromDB($fetch_top_polls_data_array);
	}
	if (!empty($top_polls) && !empty($top_polls[0])) {
		unset($top_polls[0]['poll']['publisher']['password']);
		unset($top_polls[0]['poll']['publisher']['email_code']);
		$top_polls[0]['poll']['is_favorited'] = $db->where('post_id',$top_polls[0]['poll']['id'])->where('user_id',$fl['user']['user_id'])->where('page','polls')->getValue(T_FAV,"COUNT(*)");
		$data['polls'] = $top_polls;
	}

	$fetch_top_videos_data_array    = array(
	    'table' => T_VIDEOS,
	    'column' => 'id',
	    'limit' => 1,
	    'order' => array(
	        'type' => 'rand',
	        'column' => 'id'
	    ),
	    'where' => array(
	        array(
	            'column' => 'active',
	            'value' => '1',
	            'mark' => '='
	        ),
	        array(
	            'column' => 'featured',
	            'value' => '1',
	            'mark' => '='
	        )
	    ),
	    'final_data' => array(
	        array(
	            'function_name' => 'FL_GetVideos',
	            'column' => 'id',
	            'name' => 'video'
	        )
	    )
	);

	$top_videos = FL_FetchDataFromDB($fetch_top_videos_data_array);

	if (empty($top_videos)) {
	    unset($fetch_top_videos_data_array['where'][1]);
	    $top_videos = FL_FetchDataFromDB($fetch_top_videos_data_array);
	}
	if (!empty($top_videos) && !empty($top_videos[0])) {
		unset($top_videos[0]['video']['publisher']['password']);
		unset($top_videos[0]['video']['publisher']['email_code']);
		$top_videos[0]['video']['is_favorited'] = $db->where('post_id',$top_videos[0]['video']['id'])->where('user_id',$fl['user']['user_id'])->where('page','videos')->getValue(T_FAV,"COUNT(*)");
		$data['videos'] = $top_videos;
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