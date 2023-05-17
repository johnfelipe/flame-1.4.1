<?php
if ($fl['loggedin'] == false) {
  header("Location: " . FL_Link(''));
  exit();
}
if ($fl['config']['live_video'] != 1) {
	header("Location: " . FL_Link(''));
    exit();
}
if ($fl['config']['agora_live_video'] != 1) {
	header("Location: " . FL_Link(''));
    exit();
}
// $if_live = $db->where('user_id',$fl['user']['id'])->where('stream_name','','!=')->where('live_time',time() - 5,'>=')->getValue(T_POSTS,'COUNT(*)');
// if ($if_live > 0) {
// 	header("Location: " . FL_Link(''));
//     exit();
// }
$db->where('time',time()-60,'<')->delete(T_LIVE_SUB);
$fl['title']          = $lang['live'] . ' | ' . $fl['config']['title'];
$fl['description']    = $fl['config']['description'];
$fl['page']           = 'live';
$fl['keywords']       = $fl['config']['keywords'];
$fl['content']     = FL_LoadPage('live/content');
$fl['page_url']            = $fl['config']['site_url'].'/live';