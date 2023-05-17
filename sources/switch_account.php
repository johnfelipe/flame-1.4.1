<?php
if (!$fl['loggedin'] || empty($_GET['session'])) {
	header("Location:" . $site_url);
	exit();
}
if ($fl['config']['switch_account'] != '1') {
	header("Location:" . $site_url);
	exit();
}
$session_id = FL_Secure($_GET['session']);
$fl['user_session'] = FL_GetUserFromSessionID($session_id);
if (!empty($fl['user_session']) && is_numeric($fl['user_session']) && $fl['user_session'] > 0) {
	$user         = FL_UserData($fl['user_session']);
	if (!empty($user)) {
		$add = true;
		if (!empty($fl['switched_accounts'])) {
		    foreach ($fl['switched_accounts'] as $key => $value) {
		        if ($value['user_id'] == $fl['user']['user_id']) {
		            $add = false;
		            unset($fl['switched_accounts'][$key]);
		        }
		        if ($user['user_id'] == $value['user_id']) {
		            unset($fl['switched_accounts'][$key]);
		        }
		    }
		}
		if ($add == true) {
		    $session_user = '';
		    if (!empty($_SESSION['user_id'])) {
		        $session_user = $_SESSION['user_id'];
		    }
		    if (!empty($_COOKIE['user_id'])) {
		        $session_user = $_COOKIE['user_id'];
		    }
		    $info = array('email' => $fl['user']['email'],
		                  'name'  => $fl['user']['name'],
		                  'avatar' => $fl['user']['avatar'],
		                  'session' => $session_user,
		                  'user_id' => $fl['user']['user_id']);
		    $fl['switched_accounts'][] = $info;
		}
		setcookie("switched_accounts", json_encode($fl['switched_accounts']), time() + (10 * 365 * 24 * 60 * 60));
		session_unset();
		$_SESSION['user_id'] = '';
		session_destroy();
		$_SESSION = array();
		unset($_SESSION);
		$_COOKIE['user_id'] = '';
		unset($_COOKIE['user_id']);
		setcookie('user_id', null, -1);
		setcookie('user_id', null, -1,'/');
		$session             = FL_CreateLoginSession($user['user_id']);
        $_SESSION['user_id'] = $session;
        setcookie("user_id", $session, time() + (10 * 365 * 24 * 60 * 60));
	}
}
header("Location:" . $site_url);
exit();

	