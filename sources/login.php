<?php
if ($fl['loggedin'] == true) {
	if ($fl['config']['switch_account'] != '1') {
		header("Location:" . $site_url);
		exit();
	}
	else{
		if (empty($_GET['type']) || (!empty($_GET['type']) && $_GET['type'] != 'add_account')) {
			header("Location:" . $site_url);
			exit();
		}
	}
}
$fl['title'] = $lang['login']  . ' | ' . $fl['config']['title'];
$fl['description'] = $fl['config']['description'];
$fl['page'] = 'login';
$fl['keywords'] = $fl['config']['keywords'];
$fl['content'] = FL_LoadPage('login/content');