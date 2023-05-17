<?php
$types = array('pro');
$data['status'] = 400;
if (!empty($_POST['type']) && in_array($_POST['type'], $types) && $fl['user']['wallet'] >= intval($fl['config']['pro_pkg_price'])) {
	$amount  = intval($fl['config']['pro_pkg_price']);
	$update  = array('wallet' => ($fl['user']['wallet'] -= $amount));

	$db->where('user_id',$fl['user']['id'])->update(T_USERS,$update);
	$update = array('is_pro' => 1,'verified' => 1);
    $go_pro = $db->where('user_id',$fl['user']['id'])->update(T_USERS,$update);

    if ($go_pro === true) {
    	$payment_data = array(
    		'user_id' => $fl['user']['id'],
    		'type'    => 'pro',
    		'amount'  => intval($fl['config']['pro_pkg_price']),
    		'date'    => date('n') . '/' . date('Y'),
    		'expire'  => strtotime("+30 days"),
                    'time' => time()
    	);

    	$db->insert(T_PAYMENTS,$payment_data);

    	$db->where('user_id',$fl['user']['id'])->update(T_LISTS,array('featured' => 1));
    	$db->where('user_id',$fl['user']['id'])->update(T_QUIZZES,array('featured' => 1));
    	$db->where('user_id',$fl['user']['id'])->update(T_VIDEOS,array('featured' => 1));
    	$db->where('user_id',$fl['user']['id'])->update(T_MUSIC,array('featured' => 1));
    	$db->where('user_id',$fl['user']['id'])->update(T_POLLS_PAGES,array('featured' => 1));
    	$db->where('user_id',$fl['user']['id'])->update(T_NEWS,array('featured' => 1));

    	$data['status'] = 200;
    	$data['url'] = FL_Link('go_pro');
	}
}
header("Content-type: application/json");
echo json_encode($data);
exit();