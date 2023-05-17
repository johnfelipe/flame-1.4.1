<?php
error_reporting(0);
require_once('config.php');
require_once('assets/includes/phpMailer_config.php');
require 'assets/import/DB/vendor/autoload.php';
require_once("assets/import/php-rss/vendor/autoload.php");

$fl           = array();
// Connect to MySQL Server
$sqlConnect   = $fl['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name);

$db = new MysqliDb($sqlConnect);

// Handling Server Errors
$ServerErrors = array();
if (mysqli_connect_errno()) {
    $ServerErrors[] = "Failed to connect to MySQL: " . mysqli_connect_error();
}
if (!function_exists('curl_init')) {
    $ServerErrors[] = "PHP CURL is NOT installed on your web server !";
}
if (!extension_loaded('gd') && !function_exists('gd_info')) {
    $ServerErrors[] = "PHP GD library is NOT installed on your web server !";
}
if (!extension_loaded('zip')) {
    $ServerErrors[] = "ZipArchive extension is NOT installed on your web server !";
}
if (!version_compare(PHP_VERSION, '5.4.0', '>=')) {
    $ServerErrors[] = "Required PHP_VERSION >= 5.4.0 , Your PHP_VERSION is : " . PHP_VERSION . "\n";
}
$query = mysqli_query($sqlConnect, "SET NAMES utf8mb4");
if (isset($ServerErrors) && !empty($ServerErrors)) {
    foreach ($ServerErrors as $Error) {
        echo "<h3>" . $Error . "</h3>";
    }
    die();
}


$fetch_banned_ip_data_array = array(
    'table'  => T_BANNED_IPS,
    'column' => '`ip_address`',
    'order'  => array(
        'type'   => 'DESC',
        'column' => 'id'
    )
);
    


$config                   = FL_GetConfig();
$all_langs           = Fl_LangsNamesFromDB();

foreach ($all_langs as $key => $value) {
    $insert = false;
    if (!in_array($value, array_keys($config))) {
        $db->insert(T_CONFIG,array('name' => $value, 'value' => 1));
        $insert = true;
    }
}
if ($insert == true) {
    $config = FL_GetConfig();
}
$time                     = $fl['time'] = time();
$fl['script_version']     = $config['script_version'];
if( ISSET( $_GET['theme'] ) && in_array($_GET['theme'], ['default', 'flame'])){
    $_SESSION['theme'] = $_GET['theme'];
}

if( ISSET( $_SESSION['theme'] )  && !empty($_SESSION['theme']) && in_array($_SESSION['theme'], ['default', 'flame'])){
    $config['theme'] = $_SESSION['theme'];
}
$config['theme_url']      = $site_url . '/themes/' . $config['theme'];
$config['site_url']       = $site_url;
$config['script_version'] = $fl['script_version'];
$fl['config']             = $config;
$fl['site_pages']         = array(
    'home'
);

$fl['banned_ips']         = FL_FetchDataFromDB($fetch_banned_ip_data_array);
$banned_ips               = array(); 
foreach ($fl['banned_ips']as $key => $ip) {
    $banned_ips[]         = $ip['ip_address'];
}

if (in_array($_SERVER["REMOTE_ADDR"], array_values($banned_ips))) {
    $banpage = FL_LoadPage('terms/ban');
    echo $banpage;
    exit();
}

$http_header              = 'http://';

if (!empty($_SERVER['HTTPS'])) {
    $http_header = 'https://';
}
$fl['phone_api']        = 'no';
if (!empty($_GET['cookie'])) {
    $session_id            = $_GET['cookie'];
    $fl['user_session']      = FL_GetUserFromSessionID($session_id,'phone');
    if (!empty($fl['user_session']) && is_numeric($fl['user_session'])) {
        $fl['phone_api']        = 'yes';
        $session             = FL_CreateLoginSession($fl['user_session']);
        $_SESSION['user_id'] = $session;
        setcookie("user_id", $session, time() + (10 * 365 * 24 * 60 * 60));
        $fl['user']         = FL_UserData($fl['user_session']);
        $fl['loggedin'] = true;
    }
}
$fl['actual_link']       = $http_header . $_SERVER['HTTP_HOST'] . urlencode($_SERVER['REQUEST_URI']);
// Login With Url
$fl['facebookLoginUrl']  = $config['site_url'] . '/social_login.php?provider=Facebook';
$fl['twitterLoginUrl']   = $config['site_url'] . '/social_login.php?provider=Twitter';
$fl['googleLoginUrl']    = $config['site_url'] . '/social_login.php?provider=Google';
$fl['vkLoginUrl']        = $config['site_url'] . '/social_login.php?provider=Vkontakte';
$fl['wowonderLoginUrl']  = $config['wownder_domain_uri'] . '/oauth?app_id=' . $fl['config']['wowonder_app_ID'];
$fl['login_with']        = ($fl['config']['facebook'] == 1 || $fl['config']['twitter'] == 1 || $fl['config']['google'] == 1 || $fl['config']['vkontakte'] == 1 || $fl['config']['wowonder'] == 1);
// Defualt User Pictures 
$fl['userDefaultAvatar'] = 'upload/photos/avatar.jpg';
$fl['userDefaultCover']  = 'upload/photos/cover.jpg';
// Get LoggedIn User Data
$fl['loggedin']          = false;
$fl['page_home']         = false;
$fl['pages_home_array']  = array(
    'home',
    'create_new',
    'edit-post'
);
$langs = LangsNamesFromDB();
if (FL_IsLogged() == true) {
    if (isset($_POST['access_token'])) {
        $session_id = $_POST['access_token'];
        $fl['user_session'] = FL_GetUserFromSessionID($session_id,'phone');
    }
    else{
        $session_id         = (!empty($_SESSION['user_id'])) ? $_SESSION['user_id'] : $_COOKIE['user_id'];
        $fl['user_session'] = FL_GetUserFromSessionID($session_id);
    }
    
    $fl['user']         = FL_UserData($fl['user_session']);
    if (!empty($fl['user']['language']) && in_array($fl['user']['language'], $langs)) {
        // if (file_exists('assets/languages/' . $fl['user']['language'] . '.php')) {
        //     $_SESSION['lang'] = $fl['user']['language'];
        // }
        $_SESSION['lang'] = $fl['user']['language'];
    }
    if ($fl['user']['user_id'] < 0 || empty($fl['user']['user_id']) || !is_numeric($fl['user']['user_id']) || FL_UserActive($fl['user']['username']) === false) {
        header("Location: " . FL_Link('logout'));
    }
    $fl['loggedin'] = true;
}
if (!empty($_POST['user_id']) && !empty($_POST['s'])) {
    $application = 'phone';
    if (!empty($_GET['application'])) {
        if ($_GET['application'] == 'windows') {
            $application = FL_Secure($_GET['application']);
        }
    }
    if ($application == 'windows') {
        $_POST['s'] = md5($_POST['s']);
    }
    $s                = FL_Secure($_POST['s']);
    $user_id          = FL_Secure($_POST['user_id']);
    $check_if_session = FL_CheckUserSessionID($user_id, $s, $application);
    if ($check_if_session === true) {
        $fl['user']   = FL_UserData($user_id);

        if ($fl['user']['user_id'] < 0 || empty($fl['user']['user_id']) || !is_numeric($fl['user']['user_id']) || FL_UserActive($fl['user']['username']) === false) {
            $json_error_data = array(
                'api_status' => '400',
                'api_text' => 'failed',
                'errors' => array(
                    'error_id' => '7',
                    'error_text' => 'User id is wrong.'
                )
            );
            header("Content-type: application/json");
            echo json_encode($json_error_data, JSON_PRETTY_PRINT);
            exit();
        }
        $fl['loggedin'] = true;
    } else {
        $json_error_data = array(
            'api_status' => '400',
            'api_text' => 'failed',
            'errors' => array(
                'error_id' => '6',
                'error_text' => 'Session id is wrong.'
            )
        );
        header("Content-type: application/json");
        echo json_encode($json_error_data, JSON_PRETTY_PRINT);
        exit();
    }
}
if (isset($_GET['lang']) AND !empty($_GET['lang'])) {
    $lang_name = FL_Secure(strtolower($_GET['lang']));
    //$lang_path = 'assets/lang/' . $lang_name . '.php';
    if (in_array($lang_name, $langs)) {
        $_SESSION['lang'] = $lang_name;
        if ($fl['loggedin'] == true) {
            mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `language` = '" . $lang_name . "' WHERE `user_id` = " . FL_Secure($fl['user']['user_id']));
        }
    }
}
if (empty($_SESSION['lang'])) {
    $_SESSION['lang'] = $fl['config']['language'];
}
$fl['language']      = $_SESSION['lang'];
$fl['language_type'] = 'ltr';
// Add rtl languages here.
$rtl_langs           = array(
    'arabic'
);
// checking if corrent language is rtl.
foreach ($rtl_langs as $lang) {
    if ($fl['language'] == strtolower($lang)) {
        $fl['language_type'] = 'rtl';
    }
}
// Icons Virables
$error_icon   = '<i class="fa fa-exclamation-circle"></i> ';
$success_icon = '<i class="fa fa-check"></i> ';
// Include Language File
$lang_file    = 'assets/lang/' . $fl['language'] . '.php';
if (file_exists($lang_file)) {
    require($lang_file);
}
$fl['lang'] = $lang   = LangsFromDB($fl['language']);
// print_r($fl['language']);
// print_r($fl['lang']);

$ccode               = FL_CustomCode('g');
$ccode               = (is_array($ccode))  ? $ccode    : array();
$config['header_cc'] = (!empty($ccode[0])) ? $ccode[0] : 0;
$config['footer_cc'] = (!empty($ccode[1])) ? $ccode[1] : 0;
$config['styles_cc'] = (!empty($ccode[2])) ? $ccode[2] : 0;

$fl['img_mime_types'] = array(
    'image/webp',
    'image/tiff',
    'image/svg+xml',
    'image/png',
    'image/jpeg',
    'image/jpg',
    'image/gif'
);

$fl['vid_mime_types'] = array(
    'video/mp4',
    'video/mov',
    'video/mpeg',
    'video/flv',
    'video/avi',
    'video/webm'
);

$fl['months'] = array(
    '1'  => 'January',
    '2'  => 'February',
    '3'  =>'March',
    '4'  =>'April',
    '5'  =>'May',
    '6'  =>'June',
    '7'  =>'July',
    '8'  =>'August',
    '9'  =>'September',
    '10' =>'October',
    '11' =>'November',
    '12' =>'December'
);

$fl['my_id']          = get_ip_address();
if ($fl['loggedin']) {
    $fl['my_id'] = $fl['user']['user_id'];
    if (empty($_SESSION['uploads'])) {
        $_SESSION['uploads'] = array();
    }
}

if ($fl['config']['usr_ads'] == 1) {

    if (!isset($_COOKIE['_uads'])) {
        setcookie('_uads', htmlentities(serialize(array(
            'date' => strtotime('+1 day'),
            'uaid_' => array()
        ))), time() + (10 * 365 * 24 * 60 * 60),'/');
    }

    $fl['user_ad_cons'] = array(
        'date' => strtotime('+1 day'),
        'uaid_' => array()
    );

    if (!empty($_COOKIE['_uads'])) {
        $fl['user_ad_cons'] = unserialize(html_entity_decode($_COOKIE['_uads']));
    }

    if (!is_array($fl['user_ad_cons']) || !isset($fl['user_ad_cons']['date']) || !isset($fl['user_ad_cons']['uaid_'])) {
        setcookie('_uads', htmlentities(serialize(array(
            'date' => strtotime('+1 day'),
            'uaid_' => array()
        ))), time() + (10 * 365 * 24 * 60 * 60),'/');
    }

    if (is_array($fl['user_ad_cons']) && isset($fl['user_ad_cons']['date']) && $fl['user_ad_cons']['date'] < time()) {
        setcookie('_uads', htmlentities(serialize(array(
            'date' => strtotime('+1 day'),
            'uaid_' => array()
        ))),time() + (10 * 365 * 24 * 60 * 60),'/');
    }
}
if (empty($_COOKIE['mode'])) {
    setcookie("mode", 'day', time() + (10 * 365 * 24 * 60 * 60), '/');
    $_COOKIE['mode'] = 'day';
    $fl['mode_link'] = 'night';
    $fl['mode_text'] = $fl['lang']['night_mode'];
} else {
    if ($_COOKIE['mode'] == 'day') {
        $fl['mode_link'] = 'night';
        $fl['mode_text'] = $fl['lang']['night_mode'];
    }
    if ($_COOKIE['mode'] == 'night') {
        $fl['mode_link'] = 'day';
        $fl['mode_text'] = $fl['lang']['day_mode'];
    }
}
if (!empty($_GET['mode'])) {
    if ($_GET['mode'] == 'day') {
        setcookie("mode", 'day', time() + (10 * 365 * 24 * 60 * 60), '/');
        $_COOKIE['mode'] = 'day';
        $fl['mode_link'] = 'night';
        $fl['mode_text'] = $fl['lang']['night_mode'];
    } else if ($_GET['mode'] == 'night') {
        setcookie("mode", 'night', time() + (10 * 365 * 24 * 60 * 60), '/');
        $_COOKIE['mode'] = 'night';
        $fl['mode_link'] = 'day';
        $fl['mode_text'] = $fl['lang']['day_mode'];
    }
}

// setcookie('_uads', htmlentities(serialize(array(
//     'date' => strtotime('+1 day'),
//     'uaid_' => array()
// ))),time() + (10 * 365 * 24 * 60 * 60),'/');

// print_r($fl['user_ad_cons']);
// exit();
$fl['switched_accounts'] = array();
if (!empty($_COOKIE['switched_accounts'])) {
    $fl['switched_accounts'] = json_decode($_COOKIE['switched_accounts'],true);
}
require_once('assets/import/s3/aws-autoloader.php');
require_once('assets/includes/paypal_conf.php');
?>