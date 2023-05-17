<?php
$type = array('pro','wallet');
if (!empty($_GET['type']) && in_array($_GET['type'], $type)) {
	$array = array();
	$array['TYPE'] = FL_Secure($_GET['type']);
	$array['AMOUNT'] = 0;
	$fl['wallet'] = false;
	if ($_GET['type'] == 'pro') {
		if ($fl['user']['wallet'] >= intval($fl['config']['pro_pkg_price'])) {
			$fl['wallet'] = true;
		}
		$array['AMOUNT'] = intval($fl['config']['pro_pkg_price']) * 100;
	}
	elseif ($_GET['type'] == 'wallet' && !empty($_GET['amount']) && is_numeric($_GET['amount']) && $_GET['amount'] > 0) {
		$array['AMOUNT'] = FL_Secure($_GET['amount']) * 100;
	}
	$data['status'] = 200;
	$data['html'] = FL_LoadPage('modals/payments',$array);
}
header("Content-type: application/json");
echo json_encode($data);
exit();