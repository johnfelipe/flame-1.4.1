<?php
if (empty($_GET['page_name'])) {
	header("Location: " . $site_url);
	exit();
}

$page_data = $fl['page_data'] = GetCustomPage($_GET['page_name']);
if (empty($page_data)) {
	header("Location: " . $site_url);
	exit();
}

$fl['title']       = $page_data['page_title'] . ' | ' . $fl['config']['title'];
$fl['description'] = $fl['config']['description'];
$fl['keywords']    = $fl['config']['keywords'];
$fl['page']        = 'custom_page';
$fl['content']     = FL_LoadPage('terms/custom-page');