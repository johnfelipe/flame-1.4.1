<?php

if (empty($_GET['id'])) {
    header("Location:" . FL_Link('404'));
    exit();
}


$page = (isset($_GET['page'])) ? (int) $_GET['page'] : 1;
if (!empty($_GET['publish'])) {
    if ($_GET['publish'] == 'true') {
         $id = @end(explode('-', $_GET['id']));
        $form_data_array = array('viewable' => 1);
        if (FL_IsAdmin() == true || $fl['config']['review_posts'] == 0) {
            $form_data_array['active'] = 1;
        }
        $update = FL_UpdatePost($id, $form_data_array, 'poll');
    }
}
$polls = $fl['polls'] = FL_GetPost($_GET['id'], $page, 'polls');

if (empty($polls)) {
    header("Location:" . FL_Link('404'));
    exit();
}
$fl['is_post_owner'] = FL_IsPostOwner($polls['id'], 'polls');
if ($polls['viewable'] == 0) {
    if ($fl['loggedin'] == false) {
        header("Location:" . FL_Link('404'));
        exit();
    } 
    else if ($fl['is_post_owner'] === false) {
        header("Location:" . FL_Link('404'));
        exit();
    }
}
if ($polls['active'] == 0) {
    if ($fl['loggedin'] == false) {
        header("Location:" . FL_Link('404'));
        exit();
    } else if ($fl['is_post_owner'] === false) {
        header("Location:" . FL_Link('404'));
        exit();
    }
}
if (empty($polls['entries'][0])) {
    header("Location:" . FL_Link('404'));
    exit();
}

$update_views = FL_UpdateViews('polls', $polls['id']);

$next_link = "";
$back_link = "";

if ($polls['entries_per_page'] > 0) {
    if ($page != $polls['entries']['totalPages'] && $polls['entries']['totalPages'] != 0) {
        $next_link = "<a class='btn btn-main' href='?page=" . ($page + 1) . "'><i class='fa fa-arrow-right'></i> " . $lang['next_page'] . "</a>";
    }
    if (($page != 1) || ($page == $polls['entries']['totalPages'] && $page > 1)) {
        $back_link = "<a class='btn btn-main' href='?page=" . ($page - 1) . "'><i class='fa fa-arrow-left'></i> " . $lang['previous_page'] . "</a>";
    }
}

$fl['next_link']   = $next_link;
$fl['back_link']   = $back_link;
$fl['title']       = $polls['title'];
$fl['description'] = $polls['description'];
$fl['page']        = 'polls';
$fl['keywords']    = $polls['tags'];

$create_session    = FL_CreateSession();

$lists_admin_option = '';
$publish_button = '';

if ($fl['is_post_owner'] == true) {
	$lists_admin_option = FL_LoadPage("story/admin-options");
	if ($fl['polls']['active'] == 0 && $fl['polls']['viewable'] == 1) {
		$publish_button = '&nbsp; |<span class="btn approve"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22C6.47,22 2,17.5 2,12A10,10 0 0,1 12,2M12.5,7V12.25L17,14.92L16.25,16.15L11,13V7H12.5Z"></path></svg> ' . $lang['waiting_for_approval'] . '</span>';
	} else if ($fl['polls']['viewable'] == 0 && $fl['polls']['active'] == 0) {
        $publish_button = '&nbsp; |<a class="btn publish" href="?publish=true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M5,4V6H19V4H5M5,14H9V20H15V14H19L12,7L5,14Z"></path></svg> ' . $lang['publish'] . '</a>';
	}
}

$share_onclick = "FL_ShareLink(this.href, event,"  . $fl['polls']['id'] . ", 'polls');";
$share_twitter_onclick = "FL_ShareLink('none', event,"  . $fl['polls']['id'] . ", 'polls');";
$tags = '';
foreach (FL_GetPostTags($fl['polls']['tags']) as $key => $tag) {
    $tags .= '<a class="tags polls" href="{{LINK tags/' . $tag . '}}" data-ajax="?link1=tags&tag='.$tag.'">' . $tag . '</a> ';
}
$fl['story']['is_active'] = 0;
if ($fl['polls']['active'] == 1) {
   $fl['story']['is_active'] = 1;
}

if ($fl['loggedin'] === true) {
    $fl['reported'] = false;
    $id             = @end(explode('-', $_GET['id']));

    if (!is_numeric($id)) {
        preg_match('/^(?:.+)\-(?P<id>[0-9]+)(?:\.html|)$/',$id,$matches);
        if (!empty($matches['id']) && is_numeric($matches['id'])) {
            $id = $matches['id'];
        }
    }

    $table       = T_REPORTS;
    $page        = $fl['page']; 
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
    $user_reports       = FL_CountData($where,$table);
    if (is_numeric($user_reports) && $user_reports > 0) {
        $fl['reported'] = true;
    }
}

$comments = '';
foreach (FL_GetStoryComments(array('post_id' => $fl['polls']['id'],'page' => $fl['page'])) as $key => $fl['comment']) {
    $replies            = '';
    foreach ($fl['comment']['replies'] as $fl['reply']) {
        $replies           .=  FL_LoadPage("comment/comment-reply", array(
        'REPLY_ID'           => $fl['reply']['id'],
        'COMM_ID'            => $fl['reply']['comment'],
        'POST_ID'            => $fl['reply']['news_id'],
        'REPLY_TEXT'         => $fl['reply']['text'],
        'REPLY_TIME'         => FL_Time_Elapsed_String($fl['reply']['time']),
        'REPLY_USER_NAME'    => $fl['reply']['user_data']['name'],
        'USER_VERIFIED'      => ($fl['reply']['user_data']['verified'] == 1) ? '<span class="verified-icon"><i class="fa fa-check-circle"></i></span>' : '',
        'REPLY_USER_URL'     => $fl['reply']['user_data']['url'],
        'REPLY_USER_AVATAR'  => $fl['reply']['user_data']['avatar'],
        ));
    }

    $comments          .=  FL_LoadPage("comment/comment-content", array(
    'COMM_ID'           => $fl['comment']['id'],
    'POST_ID'           => $fl['polls']['id'],
    'STORY_PAGE'        => $fl['page'],
    'COMM_TEXT'         => $fl['comment']['text'],
    'COMM_TIME'         => FL_Time_Elapsed_String($fl['comment']['time']),
    'COMM_USER_NAME'    => $fl['comment']['user_data']['name'],
    'USER_VERIFIED'     => ($fl['comment']['user_data']['verified'] == 1) ? '<span class="verified-icon"><i class="fa fa-check-circle"></i></span>' : '',
    'COMM_USER_URL'     => $fl['comment']['user_data']['url'],
    'COMM_USER_AVATAR'  => $fl['comment']['user_data']['avatar'],
    'COMM_REPLIES'      => $replies,
    ));
}

$fl['user_reactions']  = FL_GetPercentageOfReactions($fl['polls']['id'],$fl['page']);
$user_reactions        = FL_LoadPage('story/user-reactions',array(
    'STORY_ID' => $fl['polls']['id'],
    'STORY_PAGE' => $fl['page']
));

$page_context = array(
    'STORY_ID' => $fl['polls']['id'],
    'STORY_TITLE' => $fl['polls']['title'],
    'STORY_THUMB' => FL_GetMedia($fl['polls']['image']),
    'STORY_DESC' => $fl['polls']['description'],
    'STORY_ADMIN_OPTIONS' => $lists_admin_option,
    'STORY_VIEWS' => $fl['polls']['views'],
    'STORY_ENTRIES' => FL_ForEachEntries($fl['polls']['entries']),
    'STORY_AD' => FL_GetAd('between', false),
    'CATEGORY_NAME' => (!empty($fl['poll_categories'][$fl['polls']['category']])) ? $fl['poll_categories'][$fl['polls']['category']]: '',
    'CATEGORY_LINK' => '{{LINK latest-polls/' . $fl['polls']['category'] . '}}',
    'STORY_POSTED_TIME' => $fl['polls']['posted'],
    'STORY_UPDATED_TIME' => $fl['polls']['updated'],
    'STORY_LINK' => $fl['polls']['url'],
    'STORY_AJAX_LINK' => '?link1=polls&id='.$fl['polls']['slug'].'-'.$fl['polls']['id'],
    'STORY_CATEGORY_LINK' => '?link1=latest-polls&c_id='.$fl['polls']['category'],
    'STORY_USER_LINK' => '?link1=profile&u='.$fl['polls']['publisher']['username'],
    'STORY_ENCODED_URL' => urlencode($fl['polls']['url']),
    'STORY_SHARES' => $fl['polls']['shares'],

    'NEXT_LINK' => $next_link,
    'BACK_LINK' => $back_link,
    
    'EDIT_BUTTON' => FL_Link('edit-post/' . $fl['polls']['id'] . '?type=polls'),
    'DELETE_BUTTON' => FL_Link('delete-post/' . $fl['polls']['id'] . '?type=polls'),
    'PUBISH_BUTTON' => $publish_button,
    
    'PUBLISHER_NAME' => $fl['polls']['publisher']['name'],
    'PUBLISHER_AVATAR' => $fl['polls']['publisher']['avatar'],
    'PUBLISHER_VERIFIED' => ($fl['polls']['publisher']['verified'] == 1) ? '<span class="verified-icon"><i class="fa fa-check-circle"></i></span>' : '',
    'PUBLISHER_URL' => $fl['polls']['publisher']['url'],
    'PUBLISHER_GENDER' => ($fl['polls']['publisher']['gender'] == 'male') ? $fl['lang']['male'] : $fl['lang']['female'],
    'PUBLISHER_COUNTRY' => (!empty($fl['polls']['publisher']['country_id'])) ? ', Lives in ' . $fl['countries_name'][$fl['polls']['publisher']['country_id']] : '',

    'NEW_SESSION_HASH' => $create_session,
    'SIDEBAR_HTML' => FL_LoadPage('sidebar/content', array('SIDEBAR_AD' => FL_GetAd('sidebar', false))),
    'MY_ID' => $fl['my_id'],
    'SHARE_BUTTON_ONE' => $share_onclick,
    'SHARE_BUTTON_TWITTER' => $share_twitter_onclick,

    'STORY_TAGS' => $tags,
    'STORY_COMMENT_TOTAL' => FL_CountPostComments($fl['polls']['id'],$fl['page']),
    'STORY_COMMENTS' => $comments,
    'STORY_PAGE' => $fl['page'],
    'USER_REACTIONS' => $user_reactions,
    
    'SP_UAD_1' => '',
    'SP_UAD_2' => '',

    'SB_UAD_1' => '',
    'SB_UAD_2' => '',
    'SB_UAD_3' => '',
);

require_once('sources/ads/load_sp_ads.php');

$fl['content'] = FL_LoadPage("story/content",$page_context);
$fl['page_url']            = $fl['config']['site_url'].'/polls/'.$_GET['id'];
