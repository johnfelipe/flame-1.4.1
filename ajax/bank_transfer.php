<?php
$types = array('pro','wallet');
$data['status'] = 400;
if (!empty($_POST['type']) && in_array($_POST['type'], $types) && !empty($_FILES["thumbnail"])) {
	$amount = 0;
	if ($_POST['type'] == 'pro') {
		$amount = intval($fl['config']['pro_pkg_price']);
	}
	elseif ($_POST['type'] == 'wallet' && !empty($_POST['amount']) && is_numeric($_POST['amount']) && $_POST['amount'] > 0) {
		$amount = FL_Secure($_POST['amount'] / 100);
	}
	$type = FL_Secure($_POST['type']);
    $fileInfo      = array(
        'file' => $_FILES["thumbnail"]["tmp_name"],
        'name' => $_FILES['thumbnail']['name'],
        'size' => $_FILES["thumbnail"]["size"],
        'type' => $_FILES["thumbnail"]["type"],
        'types' => 'jpeg,jpg,png,bmp,gif'
    );
    $media         = FL_ShareFile($fileInfo);
    $mediaFilename = $media['filename'];
    if (!empty($mediaFilename)) {
        $insert_id = InsertBankTrnsfer(array('user_id' => $fl['user']['user_id'],
                                               'description' => $type,
                                               'price'       => $amount,
                                               'receipt_file' => $mediaFilename,
                                               'mode'         => $type));
        if (!empty($insert_id)) {
            $data = array(
                'message' => $fl['lang']['bank_transfer_request'],
                'status' => 200
            );
        }
    }
    else{
        $error = $fl['lang']['file_not_supported'];
        $data = array(
            'status' => 500,
            'message' => $error
        );
    }
}