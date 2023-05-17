<?php
require_once('assets/init.php');
if (!empty($_GET)) {
    foreach ($_GET as $key => $value) {
        $_GET[$key] = strip_tags($value);
    }
}
if (!empty($_REQUEST)) {
    foreach ($_REQUEST as $key => $value) {
        $_REQUEST[$key] = strip_tags($value);
    }
}
if (!empty($_POST)) {
    foreach ($_POST as $key => $value) {
        $_POST[$key] = strip_tags($value);
    }
}
if ($fl['loggedin'] == true) {
    $update = FL_UpdateUserData($fl['user']['user_id'], array('last_active' => time()));
} 
$page = '';
if (isset($_GET['link1'])) {
    $page = $_GET['link1'];
} 
else {
    $page = 'home';
}

switch ($page) {
    case 'home':
        include('sources/home.php');
        break;
    case 'activate':
        include('sources/activate.php');
        break;
    case 'login':
        include('sources/login.php');
        break;
    case 'register':
        include('sources/register.php');
        break;
    case 'timeline':
        include('sources/timeline.php');
        break;
    case 'logout':
        include('sources/logout.php');
        break;
    case 'forgot_password':
        include('sources/forgot_password.php');
        break;
    case 'reset-password':
        include('sources/reset_password.php');
        break;
    case 'profile':
        include('sources/profile.php');
        break;
    case 'settings':
        include('sources/settings.php');
        break;
    case 'create-new':
        include('sources/create_new.php');
        break;
    case 'news':
        include('sources/news.php');
        break;
    case 'lists':
        include('sources/lists.php');
        break;    
    case 'videos':
        include('sources/videos.php');
        break;   
    case 'music':
        include('sources/music.php');
        break;    
    case 'polls':
        include('sources/polls.php');
        break;
    case 'quiz':
        include('sources/quiz.php');
        break; 
    case 'delete-post':
        include('sources/delete_post.php');
        break;
    case 'edit-post':
        include('sources/edit_post.php');
        break;
    case 'admincp':
        header("Location: " . FL_Link('admin-cp'));
        exit();
        break;
    case 'search':
        include('sources/search.php');
        break;
    case 'latest-news':
        include('sources/latest-news.php');
        break;   
    case 'latest-lists':
        include('sources/latest-lists.php');
        break; 
    case 'latest-videos':
        include('sources/latest-videos.php');
        break; 
    case 'latest-music':
        include('sources/latest-music.php');
        break; 
    case 'latest-quizzes':
        include('sources/latest-quizzes.php');
        break;
    case 'latest-polls':
        include('sources/latest-polls.php');
        break; 
    case 'saved-drafts':
        include('sources/saved-drafts.php');
        break; 
    case 'create-new-mobile':
        include('sources/create-new-mobile.php');
        break;
    case 'terms':
        include('sources/terms.php');
        break;
    case 'tags':
        include('sources/tags.php');
        break;
    case 'feeds':
        include('sources/feeds.php');
        break;
    case 'rss':
        include('sources/rss.php');
        break;
    case 'post_data':
        include('sources/post_data.php');
        break;
    case 'go_pro':
        include('sources/go_pro/content.php');
        break;
    case 'ads':
        include('sources/ads/ads.php');
        break;
    case 'create_ad':
        include('sources/ads/create.php');
        break;
    case 'edit_ad':
        include('sources/ads/edit_ad.php');
        break;
    case 'switch_account':
        include('sources/switch_account.php');
        break;
    case 'live':
        include('sources/live.php');
        break;
    case 'site-pages':
        include('sources/site_pages.php');
        break;
        
}
if (empty($fl['content'])) {
    include('sources/404.php');
}

$data = array();
if (empty($fl['title'])) {
    $data['title'] = $fl['config']['title'];
}
$data['title'] = stripslashes(FL_Secure($fl['title']));
$data['page'] = $fl['page'];

$data['page_url'] = $fl['page_url'];
?>
<input type="hidden" id="json-data" value='<?php echo htmlspecialchars(json_encode($data));?>'>
<?php
echo $fl['content'];
?>