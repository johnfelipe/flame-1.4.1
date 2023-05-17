<?php
// +------------------------------------------------------------------------+
// | @author Deen Doughouz (DoughouzForest)
// | @author_url 1: http://www.phpflame.com
// | @author_url 2: http://codecanyon.net/user/doughouzforest
// | @author_email: phpflamesocial@gmail.com   
// +------------------------------------------------------------------------+
// | FLAME - The Ultimate PHP Viral Media Platform
// | Copyright (c) 2017 phpflame. All rights reserved.
// +------------------------------------------------------------------------+
require_once('assets/init.php');
header_remove('Server');
$api_version  = '1.0';
$type         = '';
$applications = array('phone');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$application = 'phone';
if (!empty($_GET['application'])) {
    if (in_array($_GET['application'], $applications)) {
        $application = FL_Secure($_GET['application']);
    }
}
if (!empty($_GET['type'])) {
    $type = FL_Secure($_GET['type']);
} 
if (empty($_POST['server_key']) || $_POST['server_key'] != $fl['config']['apps_api_key']) {
    $response_data       = array(
        'api_status'     => '404',
        'errors'         => array(
            'error_id'   => '1',
            'error_text' => 'Wrong server key'
        )
    );
    
    header("Content-type: application/json");
    echo json_encode($response_data);
    exit();
}

if ($application == 'phone') {
    
    switch ($type) {
        case 'user_login':
            include "api/$application/login.php";
            break;
        case 'user_registration':
            include "api/$application/register_user.php";
            break;
        case 'reset_passwd':
            include "api/$application/reset_passwd.php";
            break;
        case 'get_cats':
            include "api/$application/get_categories.php";
            break;
        case 'post_data':
            include "api/$application/post_data.php";
            break;
        case 'latest_posts':
            include "api/$application/latest_posts.php";
            break;
        case 'posts_by_category':
            include "api/$application/posts_by_category.php";
            break;
        case 'search_posts':
            include "api/$application/search_posts.php";
            break;
        case 'get_settings':
            include "api/$application/get_settings.php";
            break;
        case 'logout':
            include "api/$application/logout.php";
            break;
        case 'delete_account':
            include "api/$application/delete_account.php";
            break;
        case 'social-login':
            include "api/$application/social-login.php";
            break;
        case 'settings':
            include "api/$application/settings.php";
            break;
        case 'verification':
            include "api/$application/verification.php";
            break;
        case 'get_user_data':
            include "api/$application/get_user_data.php";
            break;
        case 'trending':
            include "api/$application/trending.php";
            break;
        case 'top':
            include "api/$application/top.php";
            break;
        case 'delete_post':
            include "api/$application/delete_post.php";
            break;
        case 'report':
            include "api/$application/report.php";
            break;
        case 'comments':
            include "api/$application/comments.php";
            break;
        case 'fav':
            include "api/$application/fav.php";
            break;
        case 'get_post_by_id':
            include "api/$application/get_post_by_id.php";
            break;
        case 'bank':
            include "api/$application/bank.php";
            break;
        case 'checkout':
            include "api/$application/checkout.php";
            break;
        case 'cashfree':
            include "api/$application/cashfree.php";
            break;
        case 'iyzipay':
            include "api/$application/iyzipay.php";
            break;
        case 'paysera':
            include "api/$application/paysera.php";
            break;
        case 'paystack':
            include "api/$application/paystack.php";
            break;
        case 'razorpay':
            include "api/$application/razorpay.php";
            break;
        case 'stripe':
            include "api/$application/stripe.php";
            break;
    }
}
mysqli_close($sqlConnect);
unset($fl);
?>