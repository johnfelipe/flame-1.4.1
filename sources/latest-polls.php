<?php 
$fl['title']       = $lang['latest_polls'] . ' | ' .$fl['config']['title'];
$fl['description'] = $fl['config']['description'];
$fl['keywords']    = $fl['config']['keywords'];
$fl['page']        = 'latest-polls';
$fetch_latest_news_data_array = array(
    'table' => T_POLLS_PAGES,
    'column' => 'id',
    'limit' => 4,
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
        ),
    ),
    'final_data' => array(
        array(
            'function_name' => 'FL_GetPolls',
            'column' => 'id',
            'name' => 'news'
        )
    )
);
$big_top_news_html = '';
$latest_news  = $fl['latest_news'] = (!empty($_GET['c_id'])) ? array() : FL_FetchDataFromDB($fetch_latest_news_data_array);
$top_news_html = '';
foreach ($latest_news as $key => $fl['top_news_data']) {
    $fl['top_news_data']['news']['page_type'] = 'polls';
	$top_news_html1 = FL_Loadpage('latest/lists/top_4', array(
        'TOP_NEWS_URL' => $fl['top_news_data']['news']['url'],
        'TOP_NEWS_IMAGE' => $fl['top_news_data']['news']['small_image'],
        'TOP_NEWS_TITLE' => $fl['top_news_data']['news']['title'],
        'TOP_NEWS_SHORT_TITLE' => $fl['top_news_data']['news']['short_title'],
        'TOP_NEWS_DESC' => $fl['top_news_data']['news']['description'],
        'TOP_NEWS_POSTED' => $fl['top_news_data']['news']['posted'],
        'TOP_NEWS_PUBLISHER__NAME' => $fl['top_news_data']['news']['publisher']['name']
    ));
	if ($key < 3) {
		$top_news_html  .= $top_news_html1;
	} else {
		$top_news_html2 = FL_Loadpage('latest/lists/top_1', array(
	        'TOP_NEWS_URL' => $fl['top_news_data']['news']['url'],
	        'TOP_NEWS_IMAGE' => $fl['top_news_data']['news']['small_image'],
	        'TOP_NEWS_TITLE' => $fl['top_news_data']['news']['title'],
	        'TOP_NEWS_SHORT_TITLE' => $fl['top_news_data']['news']['short_title'],
	        'TOP_NEWS_DESC' => $fl['top_news_data']['news']['description'],
	        'TOP_NEWS_POSTED' => $fl['top_news_data']['news']['posted'],
	        'TOP_NEWS_PUBLISHER__NAME' => $fl['top_news_data']['news']['publisher']['name']
	    ));
		$big_top_news_html = $top_news_html2;
	}
}

$fetch_latest_news_page_data_array = array(
    'table' => T_POLLS_PAGES,
    'column' => 'id',
    'limit' => 20,
    'order' => array(
        'type' => 'desc',
        'column' => 'id'
    ),
    'where' => array(
        array(
            'column' => 'active',
            'value' => '1',
            'mark' => '='
        ),
    ),
    'final_data' => array(
        array(
            'function_name' => 'FL_GetPolls',
            'column' => 'id',
            'name' => 'news'
        )
    )
);
$text = '';
if (!empty($_GET['c_id'])) {
	if (in_array($_GET['c_id'], array_keys($fl['poll_categories']))) {
		$fetch_latest_news_page_data_array['where'][] = array(
            'column' => 'category',
            'value' => $_GET['c_id'],
            'mark' => '='
        );
        $text = '/'.$_GET['c_id'];
	}
}
$latest_page_polls = $fl['latest_page_polls'] = FL_FetchDataFromDB($fetch_latest_news_page_data_array);

$fl['content'] = FL_LoadPage('latest/polls', array('TOP_4' => $top_news_html, 'TOP_BIG' => $big_top_news_html));
$fl['page_url']            = $fl['config']['site_url'].'/latest-polls'.$text;
?>