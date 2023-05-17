<?php
// +------------------------------------------------------------------------+
// | @author Deen Doughouz (DoughouzForest)
// | @author_url 1: http://www.phpflame.com
// | @author_url 2: http://codecanyon.net/user/doughouzforest
// | @author_email: phpflamesocial@gmail.com   
// +------------------------------------------------------------------------+
// | FLAME - The Ultimate PHP Viral Media Platform
// | Copyright (c) 2016 phpflame. All rights reserved.
// +------------------------------------------------------------------------+
require_once('assets/init.php');
use Aws\S3\S3Client;

$f = '';
$s = '';

if (isset($_GET['f'])) {
    $f = FL_Secure($_GET['f'], 0);
}

elseif (isset($_POST['f'])) {
    $f = FL_Secure($_POST['f'], 0);
}

if (isset($_GET['s'])) {
    $s = FL_Secure($_GET['s'], 0);
}

elseif (isset($_POST['s'])) {
    $s = FL_Secure($_POST['s'], 0);
}

$hash_id = '';

if (!empty($_POST['hash_id'])) {
    $hash_id = $_POST['hash_id'];
} 

else if (!empty($_GET['hash_id'])) {
    $hash_id = $_GET['hash_id'];
}


$data            = array();
$request_handler = "./ajax/$f.php";

if (file_exists($request_handler)) {
    require_once($request_handler);

    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}

$fl_pages = array(
    'news',
    'polls',
    'videos',
    'lists',
    'music',
    'quiz'
);
if ($f == 'login') {
    if (isset($_POST['username']) && isset($_POST['password']) && FL_CheckSession($hash_id) === true) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $result   = FL_Login($username, $password);
        if ($result === false) {
            $errors[] = $error_icon . $lang['incorrect_username_or_password'];
        } else if (FL_UserInactive($_POST['username']) === true) {
            $errors[] = $error_icon . $lang['your_account_is_disabled'];
        }
        else if ($fl['loggedin'] == true && $username == $fl['user']['username']) {
            $errors[] = $error_icon . $lang['your_already_loggedin_account'];
        }
        if (empty($errors)) {
            $user_id             = FL_UserIdForLogin($username);
            if ($fl['loggedin'] == true) {
                $add = true;
                if (!empty($fl['switched_accounts'])) {
                    foreach ($fl['switched_accounts'] as $key => $value) {
                        if ($value['user_id'] == $fl['user']['user_id']) {
                            $add = false;
                        }
                        if ($user_id == $value['user_id']) {
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
                    setcookie("switched_accounts", json_encode($fl['switched_accounts']), time() + (10 * 365 * 24 * 60 * 60));
                }
                session_unset();
                $_SESSION['user_id'] = '';
                session_destroy();
                $_SESSION = array();
                unset($_SESSION);
                $_COOKIE['user_id'] = '';
                unset($_COOKIE['user_id']);
                setcookie('user_id', null, -1);
                setcookie('user_id', null, -1,'/');
            }
            $session             = FL_CreateLoginSession($user_id);
            $_SESSION['user_id'] = $session;
            setcookie("user_id", $session, time() + (10 * 365 * 24 * 60 * 60));
            $data = array(
                'status' => 200
            );
            if (!empty($_POST['last_url'])) {
                $data['location'] = $_POST['last_url'];
            } else {
                $data['location'] = $fl['config']['site_url'];
            }
        }
    }
    header("Content-type: application/json");
    if (!empty($errors)) {
        echo json_encode(array(
            'status' => 400,
            'errors' => $errors
        ));
    } else {
        echo json_encode($data);
    }
    exit();
}
if ($f == 'create_account') {
    if (empty($_POST['email']) || empty($_POST['username']) || empty($_POST['password']) || empty($_POST['c_password']) || FL_CheckSession($hash_id) === false) {
        $errors[] = $error_icon . $lang['please_check_details'];
    } else {
        $is_exist = FL_UserExists($_POST['username']);
        if ($is_exist) {
            $errors[] = $error_icon . $lang['username_already_taken'];
        } else if (in_array($_POST['username'], $fl['site_pages'])) {
            $errors[] = $error_icon . $lang['username_invalid_characters'];
        } else if (strlen($_POST['username']) < 5 OR strlen($_POST['username']) > 32) {
            $errors[] = $error_icon . $lang['username_characters_length'];
        } else if (!preg_match('/^[\w]+$/', $_POST['username'])) {
            $errors[] = $error_icon . $lang['username_invalid_characters'];
        } else if (FL_EmailExists($_POST['email']) === true) {
            $errors[] = $error_icon . $lang['email_exists'];
        } else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = $error_icon . $lang['email_invalid_characters'];
        } else if (strlen($_POST['password']) < 6) {
            $errors[] = $error_icon . $lang['password_is_too_short'];
        } else if ($_POST['password'] != $_POST['c_password']) {
            $errors[] = $error_icon . $lang['password_not_match'];
        } else if ($config['reCaptcha'] == 1) {
            if (!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response'])) {
                $errors[] = $error_icon . $fl['lang']['reCaptcha_error'];
            }
        }
        $gender = 'male';
        $fields = FL_GetProfileFields('registration');
        if (!empty($_POST['gender'])) {
            if ($_POST['gender'] != 'male' && $_POST['gender'] != 'female') {
                $gender = 'male';
            } else {
                $gender = $_POST['gender'];
            }
        }
        if (!empty($fields) && count($fields) > 0) {
            foreach ($fields as $key => $field) {
                if (empty($_POST[$field['fid']])) {
                    $errors[] = $error_icon . $field['name'] . ' is required';
                }
                if (mb_strlen($_POST[$field['fid']]) > $field['length']) {
                    $errors[] = $error_icon . $field['name'] . ' field max characters is ' . $field['length'];
                }
            }
        }
    }
    if (empty($errors)) {
        $activate    = ($fl['config']['validation'] == '1') ? '0' : '1';
        $hashed_time = FL_Secure(md5(time()), 0);
        $re_data     = array(
            'email' => FL_Secure($_POST['email'], 0),
            'username' => FL_Secure($_POST['username'], 0),
            'password' => FL_Secure($_POST['password'], 0),
            'email_code' => $hashed_time,
            'src' => 'site',
            'gender' => FL_Secure($gender),
            'active' => FL_Secure($activate),
            'time' => time()
        );
        $invited = false;
        if (!empty($_POST['invite']) && IsAdminInvitationExists($_POST['invite'])) {
            $invited = FL_Secure($_POST['invite']);
        }
        $register    = FL_RegisterUser($re_data,$invited);
        if ($register === true) {
            $field_data = array();
            if (!empty($fields) && count($fields) > 0) {
                foreach ($fields as $key => $field) {
                    if (!empty($_POST[$field['fid']])) {
                        $name = $field['fid'];
                        if (!empty($_POST[$name])) {
                            $field_data[] = array(
                                $name => $_POST[$name]
                            );
                        }
                    }
                }
            }

            if ($activate == 1) {
                $data  = array(
                    'status' => 200,
                    'message' => $success_icon . $lang['successfully_joined']
                );
                $login = FL_Login($_POST['username'], $_POST['password']);
                if ($login === true) {
                    $user_id             = FL_UserIdFromUsername($_POST['username']);
                    $session             = FL_CreateLoginSession($user_id);
                    $_SESSION['user_id'] = $session;
                    setcookie("user_id", $session, time() + (10 * 365 * 24 * 60 * 60));
                }
                $data['location'] = FL_Link('');
                FL_UpdateUserCustomData($user_id,$field_data,false);
            } else {
                $email             = FL_Secure($_POST['email'], 0);
                $username          = FL_Secure($_POST['username'], 0);
                $confirm_link      = $fl['config']['site_url'] . "/index.php?link1=activate&email={$email}&code={$hashed_time}";
                $body              = FL_LoadPage('emails/activate');
                $body              = str_replace('{username}', $username, $body);
                $body              = str_replace('{confirm_link}', $confirm_link, $body);
                $body              = str_replace('{site_name}', $fl['config']['site_name'], $body);
                $send_message_data = array(
                    'from_email' => $fl['config']['email'],
                    'from_name' => $fl['config']['site_name'],
                    'to_email' => $_POST['email'],
                    'to_name' => $_POST['username'],
                    'subject' => 'Account activation',
                    'charSet' => 'utf-8',
                    'message_body' => $body,
                    'is_html' => true
                );
                $send              = FL_SendMessage($send_message_data);
                $errors[]          = $success_icon . $lang['successfully_joined_desc'];
            }
        }
    }
    header("Content-type: application/json");
    if (isset($errors)) {
        echo json_encode(array(
            'errors' => $errors
        ));
    } else {
        echo json_encode($data);
    }
    exit();
}
if ($f == 'forgot_password') {
    if (empty($_POST['email'])) {
        $errors[] = $error_icon . $lang['please_fill_info'];
    } else {
        $email       = FL_Secure($_POST['email'], 0);
        $check_email = FL_EmailExists($email);
        if ($check_email === false) {
            $errors[] = $error_icon . $lang['email_not_exist'];
        }
        $user_id   = FL_UserIDFromEmail($email);
        $user_data = FL_UserData($user_id);
        if (empty($user_data) && $check_email !== false) {
            $errors[] = $error_icon . $lang['error_found_request'];
        } else {
            $email             = $user_data['email'];
            $username          = $user_data['username'];
            $user_id           = $user_data['user_id'];
            $password          = $user_data['password'];
            $reset_link        = $fl['config']['site_url'] . "/index.php?link1=reset-password&code={$user_id}_{$password}";
            $body              = FL_LoadPage('emails/forgot-password');
            $body              = str_replace('{username}', $username, $body);
            $body              = str_replace('{reset_link}', $reset_link, $body);
            $body              = str_replace('{site_name}', $fl['config']['name'], $body);
            $send_message_data = array(
                'from_email' => $fl['config']['email'],
                'from_name' => $fl['config']['name'],
                'to_email' => $email,
                'to_name' => $username,
                'subject' => 'Password reset request',
                'charSet' => 'utf-8',
                'message_body' => $body,
                'is_html' => true
            );
            $send              = FL_SendMessage($send_message_data);
            if ($send) {
                $data = array(
                    'status' => 200
                );
            }
        }
    }
    header("Content-type: application/json");
    if (isset($errors)) {
        echo json_encode(array(
            'errors' => $errors
        ));
    } else {
        echo json_encode($data);
    }
    exit();
}
if ($f == 'reset_password') {
    if (empty($_POST['password']) || empty($_POST['c_password']) || empty($_POST['code'])) {
        $errors[] = $error_icon . $lang['please_fill_info'];
    } else {
        $password   = FL_Secure($_POST['password'], 0);
        $c_password = FL_Secure($_POST['c_password'], 0);
        $check_code = FL_PasswordResetCode($_POST['code']);
        if ($c_password != $password) {
            $errors[] = $error_icon . $lang['password_not_match'];
        } else if (mb_strlen($password) < 6) {
            $errors[] = $error_icon . $lang['password_is_too_short'];
        } else if (!$check_code) {
            $errors[] = $error_icon . $lang['invalid_reset_code'];
        } else {
            $code      = @explode('_', $_POST['code']);
            $user_data = FL_UserData($code[0]);
            if (empty($user_data) || empty($code[0])) {
                $errors[] = $error_icon . $lang['invalid_reset_code'];
            } else {
                $update_data     = array(
                    'password' => sha1($password)
                );
                $user_id         = $user_data['user_id'];
                $update_password = mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET password = '" . sha1($password) . "' WHERE user_id = '{$user_id}'");
                if ($update_password) {
                    $session             = FL_CreateLoginSession($user_id);
                    $_SESSION['user_id'] = $session;
                    setcookie("user_id", $session, time() + (10 * 365 * 24 * 60 * 60));
                    $data = array(
                        'status' => 200,
                        'location' => FL_Link('')
                    );
                }
            }
        }
    }
    header("Content-type: application/json");
    if (isset($errors)) {
        echo json_encode(array(
            'errors' => $errors
        ));
    } else {
        echo json_encode($data);
    }
    exit();
}
if ($f == 'get_menu_list') {
    if (!empty($_GET['type'])) {
        $html_file = 'news-list';
        switch ($_GET['type']) {
            case 'news':
                $html_file = 'news-list';
                break;
            case 'lists':
                $html_file = 'lists-list';
                break;
            case 'quiz':
                $html_file = 'quiz-list';
                break;
        }
        $html = FL_LoadPage("header/{$html_file}");
        $data = array(
            'status' => 200,
            'html' => $html
        );
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($f == 'update_post' && !empty($_GET['post_type'])) {
    
    $form_data['hash_id'] = 0;
    if (!empty($_POST['data_entry']) && !empty($_POST['form_data'])) {
        parse_str($_POST['form_data'], $form_data);
    }
    $post_type  = FL_Secure($_GET['post_type']);
    $post_types = array(
        'news',
        'list',
        'poll',
        'video',
        'music',
        'quiz'
    );

    if (!in_array($post_type, $post_types)) {
        $data_errors = array(
            'status' => 400,
            'error' => $error_icon . $lang['error_found_please_try_again_later']
        );
    }
    if (empty($form_data)) {
        $data_errors = array(
            'status' => 400,
            'error' => $error_icon . $lang['error_found_please_try_again_later']
        );
    }
    if (empty($form_data['title'])) {
        $data_errors = array(
            'status' => 410,
            'error' => $error_icon . $lang['title_is_required']
        );
    } else if (empty($form_data['short_title'])) {
        $data_errors = array(
            'status' => 480,
            'error' => $error_icon . $lang['short_title_is_required']
        );
    } else if (mb_strlen($form_data['short_title']) > 100) {
        $data_errors = array(
            'status' => 460,
            'error' => $error_icon . $lang['max_allowed_ch_short_title']
        );
    } else if (mb_strlen($form_data['short_title']) < 2) {
        $data_errors = array(
            'status' => 470,
            'error' => $error_icon . $lang['min_allowed_ch_short_title']
        );
    } else if (empty($form_data['description'])) {
        $data_errors = array(
            'status' => 430,
            'error' => $error_icon . $lang['desc_is_required']
        );
    } else if (empty($form_data['tags'])) {
        $data_errors = array(
            'status' => 420,
            'error' => $error_icon . $lang['tage_are_required']
        );
    } else if (empty($form_data['category'])) {
        $data_errors = array(
            'status' => 440,
            'error' => $error_icon . $lang['category_is_required']
        );
    } else if (empty($form_data['post_preview_image'])) {
        $data_errors = array(
            'status' => 450,
            'error' => $error_icon . $lang['preview_image_is_required']
        );
    }
    $viewable         = 0;
    $entries_per_page = 0;
    if (!empty($_POST['submit_type'])) {
        $viewable = ($_POST['submit_type'] == 'publish') ? 1 : 0;
    }
    if (!empty($form_data['entries_per_page'])) {
        $per_page_array   = array(
            0,
            1,
            2,
            4,
            6,
            8,
            10
        );
        $entries_per_page = (in_array($form_data['entries_per_page'], $per_page_array)) ? $form_data['entries_per_page'] : 0;
    }
    $fl['show'] = true;
    $table_type = $post_type;
    switch ($post_type) {
        case 'news':
            $table_type = 'news';
            break;
        case 'list':
            $table_type = 'lists';
            break;
        case 'poll':
            $table_type = 'polls';
            break;
        case 'video':
            $table_type = 'videos';
            break;
        case 'music':
            $table_type = 'music';
            break;
        case 'quiz':
            $table_type = 'quiz';
            break;
    }
    $fl['post_data'] = FL_GetEditPost($form_data['post_id'], 0, $table_type);
    if (!empty($fl['post_data']['entries'])) {
        foreach ($fl['post_data']['entries'] as $fl['key'] => $fl['entry']) {
            $entry_type = $fl['entry']['entry_type'];
            if($entry_type == "question"){
                if (!empty($fl['entry']['question_answers'])) {
                    foreach ($fl['entry']['question_answers'] as $key => $value) {
                        if ($value['correct'] == 1) {
                            $fl['show'] = false;
                        }
                    }
                }
                if ($fl['entry']['price'] > 0) {
                    $fl['show'] = false;
                }
            }
        }
    }
        
    if ($post_type == 'quiz') {
        $result_total    = 0;
        $questions_total = 0;
        foreach ($_POST['data_entry'] as $first_key => $first_value) {
            if ($first_value['type'] == 'result') {
                $result_total++;
            } else {
                $questions_total++;
            }
        }
        if ($result_total < 2 && $fl['show']) {
            $data_errors = array(
                'status' => 451
            );
        }
        if ($questions_total < 1) {
            $data_errors = array(
                'status' => 452
            );
        }
    }
    if (!empty($data_errors)) {
        header("Content-type: application/json");
        echo json_encode($data_errors);
        exit();
    }
    $full_post_type = '';
    switch ($post_type) {
        case 'news':
            $full_post_type = 'news';
            break;
        case 'list':
            $full_post_type = 'lists';
            break;
        case 'poll':
            $full_post_type = 'polls';
            break;
        case 'video':
            $full_post_type = 'videos';
            break;
        case 'music':
            $full_post_type = 'music';
            break;
        case 'quiz':
            $full_post_type = 'quiz';
            break;
    }

    if (FL_CheckSession($form_data['hash_id']) === true) {
        foreach ($_POST['data_entry'] as $first_key => $first_value) {
            if (!empty($first_value['data_inputs'])) {
                $array    = array(
                    'data_inputs' => $first_value['data_inputs'],
                    'type' => $first_value['type']
                );
                $validate = FL_ValidateEntries($first_value['data_id'], $array);
                if (!empty($validate)) {
                    $errors[] = $validate;
                }
            }
        }
        if (empty($errors)) {
            $crop = false;
            if ($form_data['post_preview_image'] != $form_data['or_post_preview_image']) {
                $crop = true;
            }
           $post_preview_image = FL_GetMediaSource($form_data['post_preview_image'], $crop);
            $slug               = FL_SlugPost($form_data['title']);
            $form_data_array    = array(
                'title' => $form_data['title'],
                'short_title' => $form_data['short_title'],
                'description' => $form_data['description'],
                'tags' => $form_data['tags'],
                'category' => $form_data['category'],
                'image' => $post_preview_image,
                'last_update' => time(),
                'viewable' => $viewable,
                'entries_per_page' => $entries_per_page,
                'slug' => $slug
            );
            if ($crop == true) {
                $form_data_array['hd'] = 1;
            }
            $insert             = FL_UpdatePost($form_data['post_id'], $form_data_array, $post_type);
            if ($insert) {
                $post_id = $form_data['post_id'];
                foreach ($_POST['data_entry'] as $first_key => $first_value) {
                    $array            = array(
                        'index_id' => ($first_key + 1),
                        'data_inputs' => $first_value['data_inputs'],
                        'type' => $first_value['type'],
                        'entry_page' => $full_post_type
                    );
                    $fetch_data_array = array(
                        'table' => T_ENTRIES,
                        'column' => 'id',
                        'count' => true,
                        'where' => array(
                            array(
                                'column' => 'id',
                                'value' => $first_value['data_id'],
                                'mark' => '='
                            )
                        )
                    );
                    $entry_data       = FL_FetchDataFromDB($fetch_data_array);
                    foreach ($entry_data as $key => $count) {
                        if ($count['count'] > 0) {
                            $insert_entries = FL_UpdateEntries($first_value['data_id'], $post_id, $array);
                        } else {
                            $insert_entries = FL_InsertEntries($first_value['data_id'], $post_id, $array);
                        }
                    }
                }
                $data = array(
                    'status' => 200,
                    'href' => FL_Link($full_post_type . '/' . $slug . '-' . $post_id)
                );
            }
        }
    }
    if (!empty($errors)) {
        echo json_encode(array(
            'status' => 400,
            'error' => $errors
        ));
    } else {
        echo json_encode($data);
        $_SESSION['uploads'] = array();
    }
    exit();
}
if ($f == 'insert_post' && !empty($_GET['post_type'])) {
    $form_data            = array();
    $form_data['hash_id'] = 0;
    if (!empty($_POST['data_entry']) && !empty($_POST['form_data'])) {
        parse_str($_POST['form_data'], $form_data);
    }
    $post_type  = FL_Secure($_GET['post_type']);

    $post_types = array(
        'news',
        'list',
        'poll',
        'video',
        'music',
        'quiz',
        'trivia',
        'flip'
    );
    if (empty($form_data)) {
        $data_errors = array(
            'status' => 400,
            'error' => $error_icon . $lang['error_found_please_try_again_later']
        );
    }
    if (empty($form_data['title'])) {
        $data_errors = array(
            'status' => 410,
            'error' => $error_icon . $lang['title_is_required']
        );
    } else if (empty($form_data['short_title'])) {
        $data_errors = array(
            'status' => 480,
            'error' => $error_icon . $lang['short_title_is_required']
        );
    } else if (mb_strlen($form_data['short_title']) > 100) {
        $data_errors = array(
            'status' => 460,
            'error' => $error_icon . $lang['max_allowed_ch_short_title']
        );
    } else if (mb_strlen($form_data['short_title']) < 2) {
        $data_errors = array(
            'status' => 470,
            'error' => $error_icon . $lang['min_allowed_ch_short_title']
        );
    } else if (empty($form_data['description'])) {
        $data_errors = array(
            'status' => 430,
            'error' => $error_icon . $lang['desc_is_required']
        );
    } else if (empty($form_data['tags'])) {
        $data_errors = array(
            'status' => 420,
            'error' => $error_icon . $lang['tage_are_required']
        );
    } else if (empty($form_data['category'])) {
        $data_errors = array(
            'status' => 440,
            'error' => $error_icon . $lang['category_is_required']
        );
    } else if (empty($form_data['post_preview_image'])) {
        $data_errors = array(
            'status' => 450,
            'error' => $error_icon . $lang['preview_image_is_required']
        );
    }
    $viewable         = 0;
    $entries_per_page = 0;
    if (!empty($_POST['submit_type'])) {
        $viewable = ($_POST['submit_type'] == 'publish') ? 1 : 0;
    }
    if (!empty($form_data['entries_per_page'])) {
        $per_page_array   = array(
            0,
            1,
            2,
            4,
            6,
            8,
            10
        );
        $entries_per_page = (in_array($form_data['entries_per_page'], $per_page_array)) ? $form_data['entries_per_page'] : 0;
    }
    if ($post_type == 'quiz' || $post_type == 'trivia' || $post_type == 'flip') {
        $result_total    = 0;
        $questions_total = 0;
        foreach ($_POST['data_entry'] as $first_key => $first_value) {
            if ($first_value['type'] == 'result') {
                $result_total++;
            } else {
                $questions_total++;
            }
            if ($post_type == 'flip' && $first_value['type'] == 'question') {
                if (empty($first_value['data_inputs']['entry_question_price']) || !is_numeric($first_value['data_inputs']['entry_question_price']) || $first_value['data_inputs']['entry_question_price'] < 1) {
                    $data_errors = array(
                        'status' => 454
                    );
                }
            }
        }
        if ($post_type != 'flip') {
            if ($result_total < 2) {
                $data_errors = array(
                    'status' => 451
                );
            }
            if ($questions_total < 1) {
                $data_errors = array(
                    'status' => 452
                );
            }
            if ($post_type == 'trivia' && $result_total < $questions_total + 1) {
                $data_errors = array(
                    'status' => 453
                );
            }
        }
    }
    if (!empty($data_errors)) {
        header("Content-type: application/json");
        echo json_encode($data_errors);
        exit();
    }
    $full_post_type = '';
    switch ($post_type) {
        case 'news':
            $full_post_type = 'news';
            break;
        case 'list':
            $full_post_type = 'lists';
            break;
        case 'poll':
            $full_post_type = 'polls';
            break;
        case 'video':
            $full_post_type = 'videos';
            break;
        case 'music':
            $full_post_type = 'music';
            break;
        case 'quiz':
            $full_post_type = 'quiz';
            break;
        case 'trivia':
            $full_post_type = 'quiz';
            break;
        case 'flip':
            $full_post_type = 'quiz';
            break;
    }
    if (FL_CheckSession($form_data['hash_id']) === true) {
        foreach ($_POST['data_entry'] as $first_key => $first_value) {
            if (!empty($first_value['data_inputs'])) {
                $array    = array(
                    'data_inputs' => $first_value['data_inputs'],
                    'type' => $first_value['type']
                );
                $validate = FL_ValidateEntries($first_value['data_id'], $array);
                if (!empty($validate)) {
                    $errors[] = $validate;
                }
            }
        }
        if (empty($errors)) {
            $active_post = 0;
            if ($viewable == 1) {
                if (FL_IsAdmin() == true || $fl['config']['review_posts'] == 0 || FL_IsModerator()) {
                    $active_post = 1;
                }
            }
            $post_preview_image = FL_GetMediaSource($form_data['post_preview_image'], true);
            $slug               = FL_SlugPost($form_data['title']);
            $form_data_array    = array(
                'user_id' => FL_Secure($fl['user']['user_id']),
                'title' => FL_Secure($form_data['title']),
                'short_title' => FL_Secure($form_data['short_title']),
                'description' => FL_Secure($form_data['description']),
                'tags' => FL_Secure($form_data['tags']),
                'category' => FL_Secure($form_data['category']),
                'image' => FL_Secure($post_preview_image),
                'time' => FL_Secure(time()),
                'last_update' => FL_Secure(time()),
                'viewable' => FL_Secure($viewable),
                'entries_per_page' => FL_Secure($entries_per_page),
                'slug' => FL_Secure($slug),
                'active' => $active_post,
                'hd' => 1
            );

            $insert_news        = FL_InsertPost($form_data_array, $post_type);
            if (is_numeric($insert_news)) {
                $post_id = $insert_news;
                foreach ($_POST['data_entry'] as $first_key => $first_value) {
                    $array          = array(
                        'index_id' => ($first_key + 1),
                        'data_inputs' => $first_value['data_inputs'],
                        'type' => $first_value['type'],
                        'entry_page' => $full_post_type
                    );

                    $insert_entries = FL_InsertEntries($first_value['data_id'], $post_id, $array);
                }
                $data = array(
                    'status' => 200,
                    'href' => FL_Link($full_post_type . '/' . $slug . '-' . $post_id)
                );
            }
        }
    }
    if (!empty($errors)) {
        echo json_encode(array(
            'status' => 400,
            'error' => $errors
        ));
    } else {
        echo json_encode($data);
        $_SESSION['uploads'] = array();
    }
    exit();
}
if ($f == 'add_entry') {
    $html = '';
    $time = $fl['time'] = time() . rand(1111, 9999);
    if (!empty($_GET['type'])) {
        $types = array(
            'text',
            'video',
            'tweet',
            'image',
            'facebook',
            'soundcloud',
            'options',
            'instagram'
        );
        if (in_array($_GET['type'], $types)) {
            $type = FL_Secure($_GET['type']);
            $html = FL_LoadPage("create-new/entries/{$type}", array(
                'ENTRY_TIME' => $time
            ));
        }
    }
    $data = array(
        'status' => 200,
        'id' => $time,
        'html' => $html
    );
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($f == 'delete_entry') {
    if (!empty($_GET['id'])) {
        $delete = FL_DeleteEntry($_GET['id']);
        if ($delete) {
            $data = array(
                'status' => 200
            );
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($f == 'fetch_video') {
    $data        = array(
        'status' => 400
    );
    $fl['video'] = array();
    $video_html  = '';
    $body        = '';
    $type        = '';
    if (!empty($_POST['video_url']) && !empty($_POST['id']) && FL_CheckSession($hash_id) === true) {
        $video_url = $_POST['video_url'];
        $id        = $_POST['id'];
        $video_id  = 0;
        $type      = '';
        $img       = '';
        $error     = '';
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video_url, $match)) {
            $video_id = FL_Secure($match[1]);
            $type     = 'youtube';
            $img      = "https://img.youtube.com/vi/{$video_id}/maxresdefault.jpg";
        } else if (preg_match("#(?<=vine.co/v/)[0-9A-Za-z]+#", $video_url, $match)) {
            $video_id = FL_Secure($match[0]);
            $type     = 'vine';
        } else if (preg_match("#https?://vimeo.com/([0-9]+)#i", $video_url, $match)) {
            $video_id = FL_Secure($match[1]);
            $type     = 'vimeo';
        } else if (preg_match('#http://www.dailymotion.com/video/([A-Za-z0-9]+)#s', $video_url, $match)) {
            $video_id = FL_Secure($match[1]);
            $type     = 'dailymotion';
        } else if (preg_match('~/videos/(?:t\.\d+/)?(\d+)~i', $video_url, $match)) {
            $video_id = FL_Secure(urlencode($video_url));
            $type     = 'facebook';
        } else {
            $error = $lang['error_not_supported_video'];
        }
    }
    if (!empty($type) && !empty($video_id) && empty($error)) {
        $body       = FL_LoadPage("create-new/iframe/{$type}");
        $video_html = str_replace('{video_id}', $video_id, $body);
        $data       = array(
            'status' => 200,
            'id' => $id,
            'type' => $type,
            'html' => $video_html,
            'video_id' => $video_id
        );
        if (!empty($img)) {
            $data['img'] = $img;
        }
    } else if (!empty($error)) {
        $data = array(
            'status' => 400,
            'message' => $error
        );
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($f == 'fetch_image') {
    if (!empty($_POST['image_url']) && !empty($_POST['id']) && FL_CheckSession($hash_id) === true) {
        $image_url = $_POST['image_url'];
        $pattern   = '/[\w\-]+\.(jpg|png|gif|jpeg)/';
        if (!preg_match($pattern, $_POST['image_url'])) {
            $data = array(
                'status' => 400,
                'message' => $error_icon . $lang['wrong_image_url']
            );
        } else {
            $get_image = FL_ImportImageFromUrl($image_url, 0);
            if ($get_image) {
                $html = '<img src="' . FL_GetMedia($get_image) . '">';
                $data = array(
                    'status' => 200,
                    'img' => FL_GetMedia($get_image),
                    'html' => $html
                );
            }
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($f == 'upload_image') {
    if (!empty($_FILES['image']['name']) && !empty($_GET['id']) && FL_CheckSession($hash_id) === true) {
        $error      = false;
        $data       = array();
        $max_up     = $fl['config']['upload'];
        if ($_FILES['image']['size'] > $max_up) {
            $max_up          = FL_SizeUnits($max_up);
            $data['status']  = 401;
            $data['message'] = $lang['max_upload_size_is'] . " $max_up";
            $error           = true;
        }
        if (empty($error)) {
            $fileInfo  = array(
                'file' => $_FILES["image"]["tmp_name"],
                'name' => $_FILES['image']['name'],
                'size' => $_FILES["image"]["size"],
                'type' => $_FILES["image"]["type"],
                'data_id' => $_GET['id']
            );
            $get_image = FL_ShareFile($fileInfo);
            if (!empty($get_image)) {
                $media = $get_image['filename'];
                $html  = '<img src="' . FL_GetMedia($media) . '">';
                $data  = array(
                    'status' => 200,
                    'img' => FL_GetMedia($media),
                    'html' => $html,
                );
            }
            else {
                $data = array(
                    'status' => 400,
                    'message' => $lang['error_found_while_uploading']
                );
            }
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($f == 'fetch_tweet') {
    if (!empty($_POST['tweet_url']) && !empty($_POST['id']) && FL_CheckSession($hash_id) === true) {
        if (!preg_match("/(http|https):\/\/twitter\.com\/[a-zA-Z0-9_]+\/status\/[0-9]+/", $_POST['tweet_url'])) {
            $data = array(
                'status' => 400,
                'message' => $error_icon . $lang['wrong_tweet_url']
            );
        } else {
            $tweet_url = FL_FetchTweet($_POST['tweet_url']);
            if (!empty($tweet_url)) {
                $data = array(
                    'status' => 200,
                    'html' => $tweet_url['html']
                );
            } else {
                $data = array(
                    'status' => 400,
                    'message' => $error_icon . $lang['wrror_getting_tweet']
                );
            }
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($f == 'fetch_instagram') {
    if (!empty($_POST['instagram_url']) && !empty($_POST['id']) && FL_CheckSession($hash_id) === true) {
        if (!preg_match("/(http|https):\/\/((www\.)instagram\.com|instagr\.am)\/p\/(.*)+/", $_POST['instagram_url'])) {
            $data = array(
                'status' => 400,
                'message' => $error_icon . $lang['wrong_ig_url']
            );
        } else {
            $instagram_url = FL_FetchInestegramPost($_POST['instagram_url']);
            if (!empty($instagram_url)) {
                $data = array(
                    'status' => 200,
                    'html' => $instagram_url['html']
                );
            } else {
                $data = array(
                    'status' => 400,
                    'message' => $error_icon . $lang['error_getting_ig']
                );
            }
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($f == 'fetch_facebook') {
    if (!empty($_POST['facebook_url']) && !empty($_POST['id']) && FL_CheckSession($hash_id) === true) {
        if (!preg_match("/(http|https):\/\/(www\.)facebook\.com\/(.*)\/(posts|videos|photos)\/(.*)/", $_POST['facebook_url'])) {
            $data = array(
                'status' => 400,
                'message' => $error_icon . $lang['wrong_fb_url']
            );
        } else {
            $facebook = FL_LoadPage('create-new/iframe/facebook-post');
            $body     = str_replace('{data-href}', $_POST['facebook_url'], $facebook);
            if (!empty($body)) {
                $data = array(
                    'status' => 200,
                    'html' => $body
                );
            } else {
                $data = array(
                    'status' => 400,
                    'message' => $error_icon . $lang['error_getting_post']
                );
            }
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($f == 'fetch_soundcloud') {
    if (!empty($_POST['soundcloud_url']) && !empty($_POST['id']) && FL_CheckSession($hash_id) === true) {
        if (!preg_match("%(?:https?://)(?:www\.)?soundcloud\.com/([\-a-z0-9_]+/[\-a-z0-9_]+)%im", $_POST['soundcloud_url'])) {
            $data = array(
                'status' => 400,
                'message' => $error_icon . $lang['wrong_soundcloud_url']
            );
        } else {
            $soundcloud_url = FL_FetchSoundCloud($_POST['soundcloud_url']);
            if (!empty($soundcloud_url)) {
                $soundcloud = FL_LoadPage('create-new/iframe/soundcloud');
                $body       = str_replace('{track_id}', $soundcloud_url, $soundcloud);
                $data       = array(
                    'status' => 200,
                    'html' => $body,
                    'id' => $soundcloud_url
                );
            } else {
                $data = array(
                    'status' => 400,
                    'message' => $error_icon . $lang['error_getting_sound']
                );
            }
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($f == 'vote') {
    if (!empty($_GET['id']) && !empty($_GET['entry_id'])) /* && FL_CheckSession($hash_id) */ {
        if ($fl['loggedin'] == true) {
            $type = 'logged_user';
            $id   = $fl['user']['user_id'];
        } else {
            $type = 'non_logged_user';
            $id   = get_ip_address();
        }
        if (!empty($id) && !empty($type)) {
            $vote = FL_VoteOption($_GET['id'], $type, $id, $_GET['entry_id']);
            if ($vote) {
                $data = array(
                    'status' => 200,
                    'data_option' => FL_GetPercentageOfOptionEntry($_GET['entry_id'])
                );
            }
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($f == 'share_link') {
    if (!empty($_GET['id']) && !empty($_GET['type'])) /* && FL_CheckSession($hash_id) */ {
        $type          = $_GET['type'];
        $update_shares = FL_UpdateShares($_GET['id'], $type);
        if ($update_shares) {
            $data = array(
                'status' => 200
            );
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($f == 'share') {
    $data = array(
        'status' => 400,
        'message' => false
    );
    if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['url'])) {
        $data['message'] = $error_icon . $lang['please_fill_info'];
    } else {
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $data['message'] = $error_icon . $fl['lang']['email_invalid_characters'];
        } else if (strlen($_POST['name']) < 4 || strlen($_POST['name']) > 32) {
            $data['message'] = $error_icon . $lang['enter_valid_name'];
        } else if (!FL_IsUrl($_POST['url'])) {
            $data['message'] = $error_icon . $lang['please_check_details'];
        }
    }
    if (empty($data['message'])) {
        $name  = FL_Secure($_POST['name']);
        $email = $_POST['email'];
        $text  = "Hi, You just got invited to see this article:";
        if (!empty($_POST['text'])) {
            $text = FL_Secure($_POST['text']);
        }
        $send_message_data = array(
            'from_email' => $fl['config']['email'],
            'from_name' => $name,
            'to_email' => $email,
            'to_name' => $email,
            'subject' => "$name shared a link with you!",
            'charSet' => 'utf-8',
            'message_body' => "$text $email",
            'is_html' => true
        );
        $send              = FL_SendMessage($send_message_data);
        if ($send) {
            $data['status']  = 200;
            $data['message'] = $lang['email_sent'];
        } else {
            $data = array(
                'status' => 401,
                'message' => $lang['func_not_available']
            );
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($f == 'delete_post') {
    if (!empty($_GET['id']) && !empty($_GET['type'])) /* && FL_CheckSession($hash_id) */ {
        $type        = $_GET['type'];
        $delete_post = FL_DeletePost($_GET['id'], $type);
        if ($delete_post) {
            $data = array(
                'status' => 200
            );
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($f == 'settings') {
    if (empty($_POST['hash_id'])) {
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if (FL_CheckSession($_POST['hash_id']) == false) {
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'general') {
        if (empty($_POST['username']) OR empty($_POST['email'])) {
            $errors[] = $error_icon . $lang['please_check_details'];
        } else {
            $Userdata = FL_UserData($_POST['user_id']);
            if (!empty($Userdata['user_id'])) {
                if ($_POST['email'] != $Userdata['email']) {
                    if (FL_EmailExists($_POST['email'])) {
                        $errors[] = $error_icon . $fl['lang']['email_exists'];
                    }
                }
                if ($_POST['username'] != $Userdata['username']) {
                    $is_exist = FL_UserExists($_POST['username']);
                    if ($is_exist) {
                        $errors[] = $error_icon . $fl['lang']['username_exists'];
                    }
                }
                if (in_array($_POST['username'], $fl['site_pages'])) {
                    $errors[] = $error_icon . $fl['lang']['username_invalid_characters'];
                }
                if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = $error_icon . $fl['lang']['email_invalid_characters'];
                }
                if (strlen($_POST['username']) < 5 || strlen($_POST['username']) > 32) {
                    $errors[] = $error_icon . $fl['lang']['username_characters_length'];
                }
                if (!preg_match('/^[\w]+$/', $_POST['username'])) {
                    $errors[] = $error_icon . $fl['lang']['username_invalid_characters'];
                }
                $active = $Userdata['active'];
                if (!empty($_POST['activeation']) && (FL_IsAdmin() || FL_IsModerator())) {
                    if ($_POST['activeation'] == '1') {
                        $active = 1;
                    } else {
                        $active = 2;
                    }
                    if ($active == $Userdata['active']) {
                        $active = $Userdata['active'];
                    }
                }
                $type = $Userdata['admin'];
                if (!empty($_POST['type']) && FL_IsAdmin()) {
                    if ($_POST['type'] == '2') {
                        $type = 1;
                    } else if ($_POST['type'] == '1') {
                        $type = 0;
                    } else if ($_POST['type'] == '3') {
                        $type = 2;
                    }
                    if ($type == $Userdata['admin']) {
                        $type = $Userdata['admin'];
                    }
                }
                $verified = $Userdata['verified'];
                if (!empty($_POST['verified']) && (FL_IsAdmin() || FL_IsModerator())) {
                    if ($_POST['verified'] == '2') {
                        $verified = 1;
                    } else if ($_POST['verified'] == '1') {
                        $verified = 0;
                    }
                }
                $gender       = 'male';
                $gender_array = array(
                    'male',
                    'female'
                );
                if (!empty($_POST['gender'])) {
                    if (in_array($_POST['gender'], $gender_array)) {
                        $gender = $_POST['gender'];
                    }
                }
                if (empty($errors)) {
                    $Update_data = array(
                        'username' => $_POST['username'],
                        'email' => $_POST['email'],
                        'gender' => $gender,
                        'country_id' => $_POST['country'],
                        'active' => $active,
                        'admin' => $type,
                        'verified' => $verified
                    );
                    if (FL_UpdateUserData($_POST['user_id'], $Update_data)) {
                        $field_data = array();
                        if (!empty($_POST['cf'])) {
                            $fields = FL_GetProfileFields('general');
                            foreach ($fields as $key => $field) {
                                $name = $field['fid'];
                                if (isset($_POST[$name])) {
                                    if (mb_strlen($_POST[$name]) > $field['length']) {
                                        $errors[] = $error_icon . $field['name'] . ' field max characters is ' . $field['length'];
                                    }
                                    $field_data[] = array(
                                        $name => $_POST[$name]
                                    );
                                }
                            }
                        }
                        if (!empty($field_data)) {
                            $insert = FL_UpdateUserCustomData($_POST['user_id'], $field_data);
                        }
                        $data = array(
                            'status' => 200,
                            'message' => $success_icon . $fl['lang']['setting_updated']
                        );
                    }
                }
            }
        }
    }
    if ($s == 'profile') {
        $Userdata = FL_UserData($_POST['user_id']);
        if (!empty($Userdata['user_id'])) {
            if (empty($errors)) {
                $Update_data = array(
                    'first_name' => $_POST['first_name'],
                    'last_name' => $_POST['last_name'],
                    'about' => $_POST['about'],
                    'facebook' => $_POST['facebook'],
                    'google' => $_POST['google'],
                    'twitter' => $_POST['twitter']
                );
                if (FL_UpdateUserData($_POST['user_id'], $Update_data)) {
                    $field_data = array();
                    if (!empty($_POST['cf'])) {
                        $fields = FL_GetProfileFields('profile');
                        foreach ($fields as $key => $field) {
                            $name = $field['fid'];
                            if (isset($_POST[$name])) {
                                if (mb_strlen($_POST[$name]) > $field['length']) {
                                    $errors[] = $error_icon . $field['name'] . ' field max characters is ' . $field['length'];
                                }
                                $field_data[] = array(
                                    $name => $_POST[$name]
                                );
                            }
                        }
                    }
                    if (!empty($field_data)) {
                        $insert = FL_UpdateUserCustomData($_POST['user_id'], $field_data,true);
                    }
                    $data = array(
                        'status' => 200,
                        'message' => $success_icon . $fl['lang']['setting_updated']
                    );
                }
            }
        }
    }
    if ($s == 'password') {
        $Userdata = FL_UserData($_POST['user_id']);
        if (!empty($Userdata['user_id'])) {
            if (empty($_POST['current_password']) || empty($_POST['new_password']) || empty($_POST['confirm_new_password'])) {
                $errors[] = $error_icon . $lang['please_check_details'];
            } else {
                if ($Userdata['password'] != sha1($_POST['current_password'])) {
                    $errors[] = $error_icon . $lang['current_password_dont_match'];
                }
                if ($_POST['new_password'] != $_POST['confirm_new_password']) {
                    $errors[] = $error_icon . $lang['new_password_dont_match'];
                }
                if (empty($errors)) {
                    $Update_data = array(
                        'password' => sha1($_POST['new_password'])
                    );
                    if (FL_UpdateUserData($_POST['user_id'], $Update_data)) {
                        $data = array(
                            'status' => 200,
                            'message' => $success_icon . $fl['lang']['setting_updated']
                        );
                    }
                }
            }
        }
    }
    if ($s == 'avatar') {
        $Userdata    = FL_UserData($_POST['user_id']);
        $Update_data = array();
        if (!empty($Userdata['user_id'])) {
            if (!empty($_FILES['avatar']['tmp_name'])) {
                $file_info   = array(
                    'file' => $_FILES['avatar']['tmp_name'],
                    'size' => $_FILES['avatar']['size'],
                    'name' => $_FILES['avatar']['name'],
                    'type' => $_FILES['avatar']['type'],
                    'crop' => array(
                        'width' => 400,
                        'height' => 400
                    )
                );
                $file_upload = FL_ShareFile($file_info);
                if (!empty($file_upload['filename'])) {
                    $Update_data['avatar'] = $file_upload['filename'];
                }
            }
            if (!empty($_FILES['cover']['tmp_name'])) {
                $file_info   = array(
                    'file' => $_FILES['cover']['tmp_name'],
                    'size' => $_FILES['cover']['size'],
                    'name' => $_FILES['cover']['name'],
                    'type' => $_FILES['cover']['type'],
                    'crop' => array(
                        'width' => 900,
                        'height' => 300
                    )
                );
                $file_upload = FL_ShareFile($file_info);
                if (!empty($file_upload['filename'])) {
                    $Update_data['cover'] = $file_upload['filename'];
                }
            }
        }
        if (FL_UpdateUserData($_POST['user_id'], $Update_data)) {
            $data = array(
                'status' => 200,
                'message' => $success_icon . $fl['lang']['setting_updated']
            );
        }
    }
    if ($s == 'delete') {
        $Userdata = FL_UserData($_POST['user_id']);
        if (!empty($Userdata['user_id'])) {
            if ($Userdata['password'] != sha1($_POST['current_password'])) {
                $errors[] = $error_icon . $lang['current_password_dont_match'];
            }
            if (empty($errors)) {
                $delete = FL_DeleteUser($Userdata['user_id']);
                if ($delete) {
                    $data = array(
                        'status' => 200,
                        'message' => $success_icon . $lang['your_account_was_deleted'],
                        'url' => FL_Link('#')
                    );
                }
            }
        }
    }
    header("Content-type: application/json");
    if (isset($errors)) {
        echo json_encode(array(
            'errors' => $errors
        ));
    } else {
        echo json_encode($data);
    }
    exit();
}
if ($f == 'admincp') {
    if (empty($hash_id)) {
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if (FL_CheckSession($hash_id) == false) {
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if (FL_IsAdmin() == false && FL_IsModerator() == false) {
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'search_in_pages') {
        $keyword = FL_Secure($_POST['keyword']);
        $html = '';

        $files = scandir('./admin-panel/pages');
        $not_allowed_files = array('edit-custom-page','edit-lang','edit-article','edit-profile-field','edit-video-ad'); 
        foreach ($files as $key => $file) {
            if (file_exists('./admin-panel/pages/'.$file.'/content.html') && !in_array($file, $not_allowed_files)) {
                
                $string = file_get_contents('./admin-panel/pages/'.$file.'/content.html');
                preg_match_all("@(?s)<h2([^<]*)>([^<]*)<\/h2>@", $string, $matches1);

                if (!empty($matches1) && !empty($matches1[2])) {
                    foreach ($matches1[2] as $key => $title) {
                        if (strpos(strtolower($title), strtolower($keyword)) !== false) {
                            $page_title = '';
                            preg_match_all("@(?s)<h2([^<]*)>([^<]*)<\/h2>@", $string, $matches3);
                            if (!empty($matches3) && !empty($matches3[2])) {
                                foreach ($matches3[2] as $key => $title2) {
                                    $page_title = $title2;
                                    break;
                                }
                            }
                            $html .= '<a href="'.FL_LoadAdminLinkSettings($file).'?highlight='.$keyword.'"><div  style="padding: 5px 2px;">'.$page_title.'</div><div><small style="color: #333;">'.$title.'</small></div></a>';
                            break;
                        }
                    }
                }

                preg_match_all("@(?s)<label([^<]*)>([^<]*)<\/label>@", $string, $matches2);
                if (!empty($matches2) && !empty($matches2[2])) {
                    foreach ($matches2[2] as $key => $lable) {
                        if (strpos(strtolower($lable), strtolower($keyword)) !== false) {
                            $page_title = '';
                            preg_match_all("@(?s)<h2([^<]*)>([^<]*)<\/h2>@", $string, $matches3);
                            if (!empty($matches3) && !empty($matches3[2])) {
                                foreach ($matches3[2] as $key => $title2) {
                                    $page_title = $title2;
                                    break;
                                }
                            }

                            $html .= '<a href="'.FL_LoadAdminLinkSettings($file).'?highlight='.$keyword.'"><div  style="padding: 5px 2px;">'.$page_title.'</div><div><small style="color: #333;">'.$lable.'</small></div></a>';
                            break;
                        }
                    }
                }
            }
        }
        $data = array(
                    'status' => 200,
                    'html'   => $html
                );
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'delete_multi_ads') {
        if (!empty($_POST['ids'])) {
            foreach ($_POST['ids'] as $key => $value) {
                if (is_numeric($value)) {
                    FL_DeleteUserAD(FL_Secure($value));
                }
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'remove_multi_custom_page') {
        if (!empty($_POST['ids'])) {
            foreach ($_POST['ids'] as $key => $value) {
                DeleteCustomPage(FL_Secure($value));
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'remove_multi_fields') {
        if (!empty($_POST['ids'])) {
            foreach ($_POST['ids'] as $key => $value) {
                FL_DeleteField(FL_Secure($value));
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'remove_multi_code') {
        if (!empty($_POST['ids'])) {
            foreach ($_POST['ids'] as $key => $value) {
                DeleteAdminInvitation('id', FL_Secure($value));
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'remove_multi_ip') {
        if (!empty($_POST['ids'])) {
            foreach ($_POST['ids'] as $key => $value) {
                $table    = T_BANNED_IPS;
                $where    = array();
                $where[0] = array(
                    'column' => '`id`',
                    'value' => $value,
                    'mark' => '='
                );
                FL_DeleteData($where,$table);
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'remove_multi_report') {
        if (!empty($_POST['ids']) && !empty($_POST['type']) && in_array($_POST['type'], array('safe','delete'))) {
            foreach ($_POST['ids'] as $key => $value) {
                if (is_numeric($value) && $value > 0) {
                    $id     = $value;
                    $table  = T_REPORTS;
                    if ($_POST['type'] == 'delete') {
                        $report   = FL_GetDataById($id,$table);
                        if (!empty($report)) {
                            FL_DeletePost($report['post_id'],$report['type']);
                            $where    = array();
                            $where[0] = array(
                                'column' => '`post_id`',
                                'value' => $report['post_id'],
                                'mark' => '='
                            );
                            FL_DeleteData($where,$table);
                        }
                    }
                    elseif ($_POST['type'] == 'safe') {
                        $where    = array();
                        
                        $where[0] = array(
                            'column' => '`id`',
                            'value' => $id,
                            'mark' => '='
                        );
                        FL_DeleteData($where,$table);
                    }
                }
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'delete_multi_br') {
        if (!empty($_POST['ids']) && !empty($_POST['type']) && in_array($_POST['type'], array('activate','deactivate','delete'))) {
            foreach ($_POST['ids'] as $key => $value) {
                if (is_numeric($value) && $value > 0) {
                    if ($_POST['type'] == 'delete') {
                        $id       = FL_Secure($value);
                        $where    = array();
                        $table    = T_BR_NEWS;
                        $where[0] = array(
                            'column' => '`id`',
                            'value' => $id,
                            'mark' => '='
                        );
                        FL_DeleteData($where, T_BR_NEWS);
                    }
                    elseif ($_POST['type'] == 'activate') {
                        $id       = FL_Secure($value);
                        $db->where('id',$id)->update(T_BR_NEWS,array('active' => '1'));
                    }
                    elseif ($_POST['type'] == 'deactivate') {
                        $id       = FL_Secure($value);
                        $db->where('id',$id)->update(T_BR_NEWS,array('active' => 0));
                    }
                }
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'delete_multi_quiz') {
        if (!empty($_POST['ids']) && !empty($_POST['type']) && in_array($_POST['type'], array('activate','deactivate','featured','not_featured','delete'))) {
            foreach ($_POST['ids'] as $key => $value) {
                if (is_numeric($value) && $value > 0) {
                    if ($_POST['type'] == 'delete') {
                        FL_DeletePost(FL_Secure($value), 'quiz');
                    }
                    elseif ($_POST['type'] == 'activate') {
                        FL_ActivatePost(FL_Secure($value), 'quiz', 'activate');
                    }
                    elseif ($_POST['type'] == 'deactivate') {
                        FL_ActivatePost(FL_Secure($value), 'quiz', '');
                    }
                    elseif ($_POST['type'] == 'featured') {
                        FL_FeaturedPost(FL_Secure($value), 'quiz', 'featured');
                    }
                    elseif ($_POST['type'] == 'not_featured') {
                        FL_FeaturedPost(FL_Secure($value), 'quiz', '');
                    }
                }
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'delete_multi_polls') {
        if (!empty($_POST['ids']) && !empty($_POST['type']) && in_array($_POST['type'], array('activate','deactivate','featured','not_featured','delete'))) {
            foreach ($_POST['ids'] as $key => $value) {
                if (is_numeric($value) && $value > 0) {
                    if ($_POST['type'] == 'delete') {
                        FL_DeletePost(FL_Secure($value), 'polls');
                    }
                    elseif ($_POST['type'] == 'activate') {
                        FL_ActivatePost(FL_Secure($value), 'polls', 'activate');
                    }
                    elseif ($_POST['type'] == 'deactivate') {
                        FL_ActivatePost(FL_Secure($value), 'polls', '');
                    }
                    elseif ($_POST['type'] == 'featured') {
                        FL_FeaturedPost(FL_Secure($value), 'polls', 'featured');
                    }
                    elseif ($_POST['type'] == 'not_featured') {
                        FL_FeaturedPost(FL_Secure($value), 'polls', '');
                    }
                }
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'delete_multi_music') {
        if (!empty($_POST['ids']) && !empty($_POST['type']) && in_array($_POST['type'], array('activate','deactivate','featured','not_featured','delete'))) {
            foreach ($_POST['ids'] as $key => $value) {
                if (is_numeric($value) && $value > 0) {
                    if ($_POST['type'] == 'delete') {
                        FL_DeletePost(FL_Secure($value), 'music');
                    }
                    elseif ($_POST['type'] == 'activate') {
                        FL_ActivatePost(FL_Secure($value), 'music', 'activate');
                    }
                    elseif ($_POST['type'] == 'deactivate') {
                        FL_ActivatePost(FL_Secure($value), 'music', '');
                    }
                    elseif ($_POST['type'] == 'featured') {
                        FL_FeaturedPost(FL_Secure($value), 'music', 'featured');
                    }
                    elseif ($_POST['type'] == 'not_featured') {
                        FL_FeaturedPost(FL_Secure($value), 'music', '');
                    }
                }
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'delete_multi_videos') {
        if (!empty($_POST['ids']) && !empty($_POST['type']) && in_array($_POST['type'], array('activate','deactivate','featured','not_featured','delete'))) {
            foreach ($_POST['ids'] as $key => $value) {
                if (is_numeric($value) && $value > 0) {
                    if ($_POST['type'] == 'delete') {
                        FL_DeletePost(FL_Secure($value), 'videos');
                    }
                    elseif ($_POST['type'] == 'activate') {
                        FL_ActivatePost(FL_Secure($value), 'videos', 'activate');
                    }
                    elseif ($_POST['type'] == 'deactivate') {
                        FL_ActivatePost(FL_Secure($value), 'videos', '');
                    }
                    elseif ($_POST['type'] == 'featured') {
                        FL_FeaturedPost(FL_Secure($value), 'videos', 'featured');
                    }
                    elseif ($_POST['type'] == 'not_featured') {
                        FL_FeaturedPost(FL_Secure($value), 'videos', '');
                    }
                }
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'delete_multi_news') {
        if (!empty($_POST['ids']) && !empty($_POST['type']) && in_array($_POST['type'], array('activate','deactivate','featured','not_featured','delete'))) {
            foreach ($_POST['ids'] as $key => $value) {
                if (is_numeric($value) && $value > 0) {
                    if ($_POST['type'] == 'delete') {
                        FL_DeletePost(FL_Secure($value), 'news');
                    }
                    elseif ($_POST['type'] == 'activate') {
                        FL_ActivatePost(FL_Secure($value), 'news', 'activate');
                    }
                    elseif ($_POST['type'] == 'deactivate') {
                        FL_ActivatePost(FL_Secure($value), 'news', '');
                    }
                    elseif ($_POST['type'] == 'featured') {
                        FL_FeaturedPost(FL_Secure($value), 'news', 'featured');
                    }
                    elseif ($_POST['type'] == 'not_featured') {
                        FL_FeaturedPost(FL_Secure($value), 'news', '');
                    }
                }
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'delete_multi_lists') {
        if (!empty($_POST['ids']) && !empty($_POST['type']) && in_array($_POST['type'], array('activate','deactivate','featured','not_featured','delete'))) {
            foreach ($_POST['ids'] as $key => $value) {
                if (is_numeric($value) && $value > 0) {
                    if ($_POST['type'] == 'delete') {
                        FL_DeletePost(FL_Secure($value), 'lists');
                    }
                    elseif ($_POST['type'] == 'activate') {
                        FL_ActivatePost(FL_Secure($value), 'lists', 'activate');
                    }
                    elseif ($_POST['type'] == 'deactivate') {
                        FL_ActivatePost(FL_Secure($value), 'lists', '');
                    }
                    elseif ($_POST['type'] == 'featured') {
                        FL_FeaturedPost(FL_Secure($value), 'lists', 'featured');
                    }
                    elseif ($_POST['type'] == 'not_featured') {
                        FL_FeaturedPost(FL_Secure($value), 'lists', '');
                    }
                }
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'delete_multi_request') {
        if (!empty($_POST['ids']) && !empty($_POST['type']) && in_array($_POST['type'], array('verify','delete'))) {
            foreach ($_POST['ids'] as $key => $value) {
                if (is_numeric($value) && $value > 0) {
                    if ($_POST['type'] == 'delete') {
                        FL_DeleteVerificationRequest(FL_Secure($value));
                    }
                    elseif ($_POST['type'] == 'verify') {
                        FL_VerifyUser(FL_Secure($value));
                    }
                }
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'delete_multi_users') {
        if (!empty($_POST['ids']) && !empty($_POST['type']) && in_array($_POST['type'], array('activate','deactivate','delete'))) {
            foreach ($_POST['ids'] as $key => $value) {
                if (is_numeric($value) && $value > 0) {
                    if ($_POST['type'] == 'delete') {
                        FL_DeleteUser(FL_Secure($value));
                    }
                    elseif ($_POST['type'] == 'activate') {
                        $db->where('user_id', FL_Secure($value));

                        $update_data = array('active' => '1');
                        $update = $db->update(T_USERS, $update_data);
                    }
                    elseif ($_POST['type'] == 'deactivate') {
                        $db->where('user_id', FL_Secure($value));

                        $update_data = array('active' => 0);
                        $update = $db->update(T_USERS, $update_data);
                    }
                }
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'remove_multi_lang_key') {
        if (!empty($_POST['ids'])) {
            foreach ($_POST['ids'] as $key => $value) {
                $db->where('lang_key',FL_Secure($value))->delete(T_LANGS);
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if($s == 'delete_multi_lang'){
        if (!empty($_POST['ids']) && !empty($_POST['type']) && in_array($_POST['type'], array('enable','disable','delete'))) {
            foreach ($_POST['ids'] as $key => $value) {
                    if ($_POST['type'] == 'delete') {
                        $mysqli = Fl_LangsNamesFromDB();
                        if (in_array($value, $mysqli)) {
                            $lang_name = FL_Secure($value);
                            $query     = mysqli_query($sqlConnect, "ALTER TABLE " . T_LANGS . " DROP COLUMN `$lang_name`");
                            if ($query) {
                                @unlink("assets/languages/extra/$lang_name.php");
                            }
                        }
                    }
                    elseif ($_POST['type'] == 'enable') {
                        FL_SaveConfig($value,1);
                    }
                    elseif ($_POST['type'] == 'disable') {
                        FL_SaveConfig($value,0);
                    }
            }
            $data = ['status' => 200];
            header("Content-type: application/json");
            echo json_encode($data);
            exit();
        }
    }
    if ($s == 'delete_page') {
        if (!empty($_GET['id'])) {
            $delete = DeleteCustomPage($_GET['id']);
            if ($delete) {
                $data = array(
                    'status' => 200
                );
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'edit_page') {
        if (!empty($_POST['page_id']) && !empty($_POST['page_name']) && !empty($_POST['page_content']) && !empty($_POST['page_title'])) {
            $page_name    = $_POST['page_name'];
            $page_content = $_POST['page_content'];
            $page_title   = $_POST['page_title'];
            $page_type    = 0;
            if (!empty($_POST['page_type'])) {
                $page_type = 1;
            }
            if (!preg_match('/^[\w]+$/', $page_name)) {
                $data = array(
                    'status' => 400,
                    'message' => 'Invalid page name characters'
                );
                header("Content-type: application/json");
                echo json_encode($data);
                exit();
            }
            $data_ = array(
                'page_name' => $page_name,
                'page_content' => $page_content,
                'page_title' => $page_title,
                'page_type' => $page_type
            );
            $add   = UpdateCustomPageData($_POST['page_id'], $data_);
            if ($add) {
                $data['status'] = 200;
            }
        } else {
            $data = array(
                'status' => 400,
                'message' => 'Please fill all the required fields'
            );
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'add_new_page') {
        if (!empty($_POST['page_name']) && !empty($_POST['page_content']) && !empty($_POST['page_title'])) {
            $page_name    = FL_Secure($_POST['page_name']);
            $page_content = FL_Secure(str_replace(array("\r","\n"),"",$_POST['page_content']));
            $page_title   = FL_Secure($_POST['page_title']);
            $page_type    = 0;
            if (!empty($_POST['page_type'])) {
                $page_type = 1;
            }
            if (!preg_match('/^[\w]+$/', $page_name)) {
                $data = array(
                    'status' => 400,
                    'message' => 'Invalid page name characters'
                );
                header("Content-type: application/json");
                echo json_encode($data);
                exit();
            }
            $data_ = array(
                'page_name' => $page_name,
                'page_content' => $page_content,
                'page_title' => $page_title,
                'page_type' => $page_type
            );
            $add   = RegisterNewPage($data_);
            if ($add) {
                $data['status'] = 200;
            }
        } else {
            $data = array(
                'status' => 400,
                'message' => 'Please fill all the required fields'
            );
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'send_mail_to_all_users') {
        $isset_test = 'off';
        if (empty($_POST['message']) || empty($_POST['subject'])) {
            $send_errors = $fl['lang']['please_check_details'];
        } else {
            if (!empty($_POST['test_message'])) {
                if ($_POST['test_message'] == 'on') {
                    $isset_test = 'on';
                }
            }
            if ($isset_test == 'on') {
                $send_message_data = array(
                    'from_email' => $fl['config']['email'],
                    'from_name' => $fl['config']['name'],
                    'to_email' => $fl['user']['email'],
                    'to_name' => $fl['user']['name'],
                    'subject' => $_POST['subject'],
                    'charSet' => 'utf-8',
                    'message_body' => $_POST['message'],
                    'is_html' => true
                );
                $send              = FL_SendMessage($send_message_data);
            } else {
                $users_type = 'all';
                $users      = array();
                if (isset($_POST['selected_emails']) && strlen($_POST['selected_emails']) > 0) {
                    $user_ids = explode(',', $_POST['selected_emails']);
                    if (is_array($user_ids) && count($user_ids) > 0) {
                        foreach ($user_ids as $user_id) {
                            $users[] = FL_UserData($user_id);
                        }
                    }
                } else if ($_POST['send_to'] == 'active') {
                    $users = GetAllUsersByType('active');
                } else if ($_POST['send_to'] == 'inactive') {
                    $users = GetAllUsersByType('inactive');
                }
                foreach ($users as $user) {
                    $send_message_data = array(
                        'from_email' => $fl['config']['email'],
                        'from_name' => $fl['config']['name'],
                        'to_email' => $user['email'],
                        'to_name' => $user['name'],
                        'subject' => $_POST['subject'],
                        'charSet' => 'utf-8',
                        'message_body' => $_POST['message'],
                        'is_html' => true
                    );
                    $send              = FL_SendMessage($send_message_data);
                    $mail->ClearAddresses();
                }
            }
        }
        header("Content-type: application/json");
        if (!empty($send_errors)) {
            $send_errors_data = array(
                'status' => 400,
                'message' => $send_errors
            );
            echo json_encode($send_errors_data);
        } else {
            $data = array(
                'status' => 200
            );
            echo json_encode($data);
        }
        exit();
    }
    if ($s == 'get_users_emails' && isset($_GET['name'])) {
        $name  = FL_Secure($_GET['name']);
        $html  = '';
        $users = GetUsersByName($name, 20);
        $data  = array(
            'status' => 404
        );
        if (count($users) > 0) {
            foreach ($users as $user) {
                $html .= "<p data-user='" . $user['user_id'] . "'>" . $user['username'] . "</p>";
            }
            $data['status'] = 200;
            $data['html']   = $html;
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'insert-invitation') {
        $data             = array(
            'status' => 200,
            'html' => ''
        );
        $fl['invitation'] = InsertAdminInvitation();
        if ($fl['invitation'] && is_array($fl['invitation'])) {
            $data['html']   = FL_LoadAdminPage('manage-invitation-keys/list');
            $data['status'] = 200;
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'rm-invitation' && isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] > 0) {
        $data = array(
            'status' => 304
        );
        if (DeleteAdminInvitation('id', $_GET['id'])) {
            $data['status'] = 200;
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'test_s3_2') {
        try {
            $s3Client = S3Client::factory(array(
                'version' => 'latest',
                'region' => $fl['config']['region_2'],
                'credentials' => array(
                    'key' => $fl['config']['amazone_s3_key_2'],
                    'secret' => $fl['config']['amazone_s3_s_key_2']
                )
            ));

            $buckets  = $s3Client->listBuckets();
            $result   = $s3Client->putBucketCors(array(
                'Bucket' => $fl['config']['bucket_name_2'], // REQUIRED
                'CORSConfiguration' => array( // REQUIRED
                    'CORSRules' => array( // REQUIRED
                        array(
                            'AllowedHeaders' => array(
                                'Authorization'
                            ),
                            'AllowedMethods' => array(
                                'POST',
                                'GET',
                                'PUT'
                            ), // REQUIRED
                            'AllowedOrigins' => array(
                                '*'
                            ), // REQUIRED
                            'ExposeHeaders' => array(),
                            'MaxAgeSeconds' => 3000
                        )
                    )
                )
            ));
            if (!empty($buckets)) {
                if ($s3Client->doesBucketExist($fl['config']['bucket_name_2'])) {
                    $data['status'] = 200;
                } else {
                    $data['status'] = 300;
                }
            } else {
                $data['status'] = 500;
            }
        }

        catch (Exception $e) {
            $data['status']  = 400;
            $data['message'] = $e->getMessage();
        }
    }
    if ($s == 'delete_receipt') {
        if (!empty($_GET['receipt_id'])) {
            $user_id = FL_Secure($_GET['user_id']);
            $id = FL_Secure($_GET['receipt_id']);
            $photo_file = FL_Secure($_GET['receipt_file']);
            $receipt = $db->where('id',$id)->getOne(T_BANK_TRANSFER,array('*'));

            // $notification_data_array = array(
            //     'recipient_id' => $receipt->user_id,
            //     'type' => 'admin_notification',
            //     'url' => 'index.php',
            //     'text' => $wo['lang']['bank_decline'],
            //     'type2' => 'no_name'
            // );
            // Wo_RegisterNotification($notification_data_array);

            $db->where('id',$id)->delete(T_BANK_TRANSFER);
            if (file_exists($photo_file)) {
                @unlink(trim($photo_file));
            }
            else if($fl['config']['amazone_s3'] == 1){
                @FL_DeleteFromToS3($photo_file);
            }
            $data = array(
                'status' => 200
            );
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'approve_receipt') {
        if (!empty($_GET['receipt_id'])) {
            $id = FL_Secure($_GET['receipt_id']);
            $receipt = $db->where('id',$id)->getOne(T_BANK_TRANSFER,array('*'));

            if($receipt){
                $updated = $db->where('id',$id)->update(T_BANK_TRANSFER,array('approved'=>1,'approved_at'=>time()));
                $user_data = FL_UserData($receipt->user_id);
                $updated = true;
                if ($updated === true) {
                    if ($receipt->mode == 'wallet') {
                        $amount  = $receipt->price;
                        $update  = array('wallet' => ($user_data['wallet'] += $amount));

                        $db->where('user_id',$user_data['id'])->update(T_USERS,$update);
                        $_SESSION['refilled_balance'] = $amount;

                        $payment_data = array(
                            'user_id' => $user_data['id'],
                            'type'    => 'wallet',
                            'amount'  => $amount,
                            'date'    => date('n') . '/' . date('Y'),
                            'expire'  => 0,
                    'time' => time()
                        );

                        $db->insert(T_PAYMENTS,$payment_data);
                        $data['status'] = 200;
                    }
                    else{
                        $update = array('is_pro' => 1,'verified' => 1);
                        $go_pro = $db->where('user_id',$user_data['id'])->update(T_USERS,$update);

                        if ($go_pro === true) {
                            $payment_data = array(
                                'user_id' => $user_data['id'],
                                'type'    => 'pro',
                                'amount'  => intval($fl['config']['pro_pkg_price']),
                                'date'    => date('n') . '/' . date('Y'),
                                'expire'  => strtotime("+30 days"),
                    'time' => time()
                            );

                            $db->insert(T_PAYMENTS,$payment_data);

                            $db->where('user_id',$user_data['id'])->update(T_LISTS,array('featured' => 1));
                            $db->where('user_id',$user_data['id'])->update(T_QUIZZES,array('featured' => 1));
                            $db->where('user_id',$user_data['id'])->update(T_VIDEOS,array('featured' => 1));
                            $db->where('user_id',$user_data['id'])->update(T_MUSIC,array('featured' => 1));
                            $db->where('user_id',$user_data['id'])->update(T_POLLS_PAGES,array('featured' => 1));
                            $db->where('user_id',$user_data['id'])->update(T_NEWS,array('featured' => 1));

                            $_SESSION['upgraded'] = true;
                            $data = array(
                                'status' => 200
                            );
                        }
                        else{
                            $data['message'] = $fl['lang']['payment_declined'];
                        }
                    }
                }
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'general') {
        $error        = false;
        $data         = array(
            'status'  => 400
        );
        if (!empty($_POST) && empty($error)) {
            foreach ($_POST as $config_key => $config_name) {
                if ($config_key != 'hash_id') {
                    if ($config_key == 'theme') {
                        $_SESSION['theme'] = $config_name;
                    }
                    $save = FL_SaveConfig($config_key, $config_name);
                }
            }
            $data = array(
                'status' => 200,
                'message' => $success_icon . $fl['lang']['setting_updated']
            );
        }
    }
    if ($s == 'design' && FL_CheckSession($hash_id) === true) {
        $saveSetting = true;
        if (isset($_FILES['logo']['name'])) {
            $fileInfo   = array(
                'file' => $_FILES["logo"]["tmp_name"],
                'name' => $_FILES['logo']['name'],
                'size' => $_FILES["logo"]["size"],
                'type' => 'logo'
            );
            $media_logo = FL_UploadIcon($fileInfo);
        }
        if (isset($_FILES['favicon']['name'])) {
            $fileInfo   = array(
                'file' => $_FILES["favicon"]["tmp_name"],
                'name' => $_FILES['favicon']['name'],
                'size' => $_FILES["favicon"]["size"],
                'type' => 'icon'
            );
            $media_icon = FL_UploadIcon($fileInfo);
        }
        if ($saveSetting === true) {
            $data['status']  = 200;
            $data['message'] = $success_icon . $lang['setting_updated'];
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'themes' && FL_CheckSession($hash_id) === true && !empty($_POST['theme'])) {
        $theme       = FL_Secure($_POST['theme']);
        $data        = array(
            'status' => 304
        );
        $saveSetting = FL_SaveConfig('theme', $theme);
        if ($saveSetting === true) {
            $data['status'] = 200;
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'delete_post') {
        if (!empty($_GET['id'])) {
            if (!empty($_GET['type'])) {
                $delete_post = FL_DeletePost($_GET['id'], $_GET['type']);
                if ($delete_post) {
                    $data = array(
                        'status' => 200
                    );
                }
            }
        }
    }
    if ($s == 'delete-news') {
        if (!empty($_POST['id'])) {
            $delete_post = FL_DeletePost($_POST['id'], 'news');
            if ($delete_post) {
                $data = array(
                    'status' => 200
                );
            }
        }
    }
    if ($s == 'delete-lists') {
        if (!empty($_POST['id'])) {
            $delete_post = FL_DeletePost($_POST['id'], 'lists');
            if ($delete_post) {
                $data = array(
                    'status' => 200
                );
            }
        }
    }
    if ($s == 'delete-videos') {
        if (!empty($_POST['id'])) {
            $delete_post = FL_DeletePost($_POST['id'], 'videos');
            if ($delete_post) {
                $data = array(
                    'status' => 200
                );
            }
        }
    }
    if ($s == 'delete-music') {
        if (!empty($_POST['id'])) {
            $delete_post = FL_DeletePost($_POST['id'], 'music');
            if ($delete_post) {
                $data = array(
                    'status' => 200
                );
            }
        }
    }
    if ($s == 'delete-polls') {
        if (!empty($_POST['id'])) {
            $delete_post = FL_DeletePost($_POST['id'], 'polls');
            if ($delete_post) {
                $data = array(
                    'status' => 200
                );
            }
        }
    }
    if ($s == 'delete-quizzes') {
        if (!empty($_POST['id'])) {
            $delete_post = FL_DeletePost($_POST['id'], 'quiz');
            if ($delete_post) {
                $data = array(
                    'status' => 200
                );
            }
        }
    }
    if ($s == 'delete-user-ad') {
        if (!empty($_POST['id']) && is_numeric($_POST['id'])) {
            $delete_user_ad = FL_DeleteUserAD($_POST['id']);
            if ($delete_user_ad) {
                $data = array(
                    'status' => 200
                );
            }
        }
    }
    if ($s == 'activation') {
        if (!empty($_GET['id'])) {
            if (!empty($_GET['type']) && !empty($_GET['activation'])) {
                $activate_post = FL_ActivatePost($_GET['id'], $_GET['type'], $_GET['activation']);
                if ($activate_post) {
                    $data = array(
                        'status' => 200
                    );
                }
            }
        }
    }
    if ($s == 'featured') {
        if (!empty($_GET['id'])) {
            if (!empty($_GET['type']) && !empty($_GET['featured'])) {
                $activate_post = FL_FeaturedPost($_GET['id'], $_GET['type'], $_GET['featured']);
                if ($activate_post) {
                    $data = array(
                        'status' => 200
                    );
                }
            }
        }
    }
    if ($s == 'user_activation') {
        if (!empty($_GET['id'])) {
            if (!empty($_GET['activation'])) {
                $active        = ($_GET['activation'] == 'activate') ? 1 : 0;
                $activate_user = FL_UpdateUserData($_GET['id'], array(
                    'active' => $active
                ));
                if ($activate_user) {
                    $data = array(
                        'status' => 200
                    );
                }
            }
        }
    }
    if ($s == 'terms_setting') {
        $saveSetting = false;
        foreach ($_POST as $key => $value) {
            if ($key != 'hash_id') {
                $saveSetting = FL_SaveTerm($key, base64_decode($value));
            }
        }
        if ($saveSetting === true) {
            $data['status'] = 200;
        }
    }
    if ($s == 'delete-user') {
        if (!empty($_POST['id'])) {
            $delete = FL_DeleteUser($_POST['id']);
            if ($delete) {
                $data = array(
                    'status' => 200
                );
            }
        }
    }
    if ($s == 'update_ads') {
        $updated = false;
        foreach ($_POST as $key => $ads) {
            if ($key != 'hash_id') {
                $ad_data = array(
                    'type' => $key,
                    'code' => base64_decode($ads),
                    'active' => (empty($ads)) ? 0 : 1
                );
                $update = FL_UpdateAdsCode($ad_data);
                if ($update) {
                    $updated = true;
                }
            }
        }
        if ($updated == true) {
            $data = array(
                'status' => 200
            );
        }
    }
    if ($s == 'update_ads_status') {
        if (!empty($_GET['type'])) {
            if (FL_UpdateAdActivation($_GET['type']) == 'active') {
                $data = array(
                    'status' => 200
                );
            } else {
                $data = array(
                    'status' => 300
                );
            }
        }
    }
    if ($s == 'add_new_field') {

        if (FL_CheckSession($hash_id) === true && !empty($_POST['name']) && !empty($_POST['type']) && !empty($_POST['description'])) {
            $type              = FL_Secure($_POST['type']);
            $name              = FL_Secure($_POST['name']);
            $description       = FL_Secure($_POST['description']);
            $registration_page = 0;
            if (!empty($_POST['registration_page'])) {
                $registration_page = 1;
            }
            $profile_page = 0;
            if (!empty($_POST['profile_page'])) {
                $profile_page = 1;
            }
            $length = 32;
            if (!empty($_POST['length'])) {
                if (is_numeric($_POST['length']) && $_POST['length'] < 1001) {
                    $length = FL_Secure($_POST['length']);
                }
            }
            $placement_array = array(
                'profile',
                'general',
                'social',
                'none'
            );
            $placement       = 'profile';
            if (!empty($_POST['placement'])) {
                if (in_array($_POST['placement'], $placement_array)) {
                    $placement = FL_Secure($_POST['placement']);
                }
            }
            $data_ = array(
                'name' => $name,
                'description' => $description,
                'length' => $length,
                'placement' => $placement,
                'registration_page' => $registration_page,
                'profile_page' => $profile_page,
                'active' => 1
            );
            if (!empty($_POST['options'])) {
                $options              = @explode("\n", trim($_POST['options']));
                $type                 = FL_Secure(implode($options, ','));
                $data_['select_type'] = 'yes';
            }
            $data_['type'] = $type;
            $add           = FL_RegisterNewField($data_);
            if ($add) {
                $data['status'] = 200;
            }
        }

        else {
            $data = array(
                'status' => 400,
                'message' => 'Please fill all the required fields'
            );
        }
    }
    if ($s == 'edit_field') {
        if (FL_CheckSession($hash_id) === true && !empty($_POST['name']) && !empty($_POST['description']) && !empty($_POST['id'])) {
            $name              = FL_Secure($_POST['name']);
            $description       = FL_Secure($_POST['description']);
            $registration_page = 0;
            if (!empty($_POST['registration_page'])) {
                $registration_page = 1;
            }
            $profile_page = 0;
            if (!empty($_POST['profile_page'])) {
                $profile_page = 1;
            }
            $active = 0;
            if (!empty($_POST['active'])) {
                $active = 1;
            }
            $length = 32;
            if (!empty($_POST['length'])) {
                if (is_numeric($_POST['length']) && $_POST['length'] < 1001) {
                    $length = FL_Secure($_POST['length']);
                }
            }
            $placement_array = array(
                'profile',
                'general',
                'social',
                'none'
            );
            $placement       = 'profile';
            if (!empty($_POST['placement'])) {
                if (in_array($_POST['placement'], $placement_array)) {
                    $placement = FL_Secure($_POST['placement']);
                }
            }
            $up_data = array(
                'name' => $name,
                'description' => $description,
                'length' => $length,
                'placement' => $placement,
                'registration_page' => $registration_page,
                'profile_page' => $profile_page,
                'active' => $active
            );
            if (!empty($_POST['options'])) {
                $options              = @explode("\n", trim($_POST['options']));
                $up_data['type']        = implode($options, ',');
                $up_data['select_type'] = 'yes';
            }
            $table = T_PR_FIELDS;
            $add   = FL_UpdateData($_POST['id'], $up_data,$table);
            if ($add) {
                $data['status'] = 200;
            }
        }

        else{
            $data = array(
                'status' => 400,
                'message' => 'Please fill all the required fields'
            );
        }
    }
    if ($s == 'delete_field' && !empty($_GET['id'])) {
        echo "string";
        $data = array('status' => 304);
        if (FL_DeleteField($_GET['id']) === true) {
            $data['status'] = 200;
        }
    }
    if ($s == 'ban' && !empty($_POST['ip'])) {
        $data  = array('status' => 400);
        $error = false;
        if (!filter_var($_POST['ip'], FILTER_VALIDATE_IP)){
            $data['message'] = $error_icon . ' Invalid ip address, Please check your details';
            $error           = true;
        }
        
        if (empty($error)){
            $table   = T_BANNED_IPS;
            $re_data = array(
                'ip_address' => $_POST['ip'],
                'time'       => time()
            );
            $ban_ip  = FL_GetDataById(FL_InsertData($re_data,$table),$table);
            
            if (is_array($ban_ip)) {
                $data['status'] = 200;
                $data['html']   = FL_LoadAdminPage("ban-users/list",array(
                    'BANNEDIP_ID'     => $ban_ip['id'],
                    'BANNEDIP_TIME'   => FL_Time_Elapsed_String($ban_ip['time']),
                    'BANNEDIP_ADDR'   => $ban_ip['ip_address'],
                ));
            }
        }
    }
    if ($s == 'unban' && !empty($_GET['id']) && is_numeric($_GET['id'])) {
        $data     = array('status' => 400);
        $table    = T_BANNED_IPS;
        $where    = array();
        $where[0] = array(
            'column' => '`id`',
            'value' => $_GET['id'],
            'mark' => '='
        );
        if (FL_DeleteData($where,$table)){
            $data['status'] = 200;
        }
    }
    if ($s == 'reports' && !empty($_GET['id']) && !empty($_GET['a'])) {
        $data     = array('status' => 400);
        $request  = (is_numeric($_GET['id']) && ($_GET['a'] == 's' || $_GET['a'] == 'd') && $_GET['id'] > 0);
        if ($request === true){
            $action = FL_Secure($_GET['a']);
            $id     = $_GET['id'];
            $table  = T_REPORTS;
            if ($action == 's') {
                $where    = array();
                
                $where[0] = array(
                    'column' => '`id`',
                    'value' => $id,
                    'mark' => '='
                );
                FL_DeleteData($where,$table);
                $data['status'] = 200;
            }

            else if($action == 'd'){
                $report   = FL_GetDataById($id,$table);
                if (!empty($report)) {
                    FL_DeletePost($report['post_id'],$report['type']);
                    $where    = array();
                    $where[0] = array(
                        'column' => '`post_id`',
                        'value' => $report['post_id'],
                        'mark' => '='
                    );
                    FL_DeleteData($where,$table);
                    $data['status'] = 200;
                }
            }
        }
    }
    if ($s == 'backup') {
        $backup = FL_Backup($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name);
        if ($backup) {
            $data['status'] = 200;
            $data['date']   = date('d-m-Y');
        }
    }
    if ($s == 'ccode') {
        $data     = array('status' => 400);
        $theme    = $fl['config']['theme'];
        $request  = (isset($_POST['cheader']) && isset($_POST['cfooter']) && isset($_POST['css']));
        if ($request === true){
            if (is_writable("themes/$theme/custom")) {
                $up_data = array(
                    $_POST['cheader'],
                    $_POST['cfooter'],
                    $_POST['css']
                );
                $save           = FL_CustomCode('p',$up_data);
                $data['status'] = 200;
            }

            else{
                $data['status'] = 500;
            }
        }
    }

    if ($s == 'test_s3') {
        try {
            $s3Client = S3Client::factory(array(
                'version' => 'latest',
                'region' => $fl['config']['region'],
                'credentials' => array(
                    'key' => $fl['config']['amazone_s3_key'],
                    'secret' => $fl['config']['amazone_s3_s_key']
                )
            ));
            $buckets  = $s3Client->listBuckets();
            if (!empty($buckets)) {
                if ($s3Client->doesBucketExist($fl['config']['bucket_name'])) {
                    $data['status'] = 200;
                    $array          = array(
                        'upload/photos/cover.jpg',
                        'upload/photos/avatar.jpg',
                    );
                    foreach ($array as $key => $value) {
                        $upload = FL_UploadToS3($value, array(
                            'delete' => 'no'
                        ));
                    }
                } else {
                    $data['status'] = 300;
                }
            }
            else {
                $data['status'] = 500;
            }
        }

        catch (Exception $e) {
            $data['status']  = 400;
            $data['message'] = $e->getMessage();
        }
    }

    if ($s == 'reset_apps_key'){
        $app_key    = sha1(microtime());

        $data_array = array(
            'apps_api_key' => $app_key
        );

        foreach ($data_array as $key => $value) {
            $saveSetting = FL_SaveConfig($key, $value);
        }

        if ($saveSetting === true) {
            $data['status']  = 200;
            $data['app_key'] = $app_key;
        }
    }


    header("Content-type: application/json");
    if (isset($errors)) {
        echo json_encode(array(
            'errors' => $errors
        ));
    }

    else {
        echo json_encode($data);
    }
    exit();
}
if ($f == 'load_more') {
    if (!empty($_GET['type']) && !empty($_GET['where'])) {
        if (!empty($_GET['id'])) {
            $table         = '';
            $function_name = '';
            switch ($_GET['type']) {
                case 'news':
                    $function_name = 'FL_GetNews';
                    $table         = T_NEWS;
                    break;
                case 'list':
                    $function_name = 'FL_GetLists';
                    $table         = T_LISTS;
                    break;
                case 'poll':
                    $function_name = 'FL_GetPolls';
                    $table         = T_POLLS_PAGES;
                    break;
                case 'video':
                    $function_name = 'FL_GetVideos';
                    $table         = T_VIDEOS;
                    break;
                case 'music':
                    $function_name = 'FL_GetMusic';
                    $table         = T_MUSIC;
                    break;
                case 'quiz':
                    $function_name = 'FL_GetQuizzes';
                    $table         = T_QUIZZES;
                    break;
            }
            if (!empty($table) && !empty($function_name)) {
                $fetch_latest_news_page_data_array = array(
                    'table' => $table,
                    'column' => 'id',
                    'limit' => 5,
                    'order' => array(
                        'type' => 'desc',
                        'column' => 'id'
                    ),
                    'where' => array(
                        array(
                            'column' => 'active',
                            'value' => '1',
                            'mark' => '='
                        ),
                        array(
                            'column' => 'id',
                            'value' => $_GET['id'],
                            'mark' => '<'
                        )
                    ),
                    'final_data' => array(
                        array(
                            'function_name' => $function_name,
                            'column' => 'id',
                            'name' => 'news'
                        )
                    )
                );
                if (!empty($_GET['c_id'])) {
                    $fetch_latest_news_page_data_array['where'][] = array(
                        'column' => 'category',
                        'value' => $_GET['c_id'],
                        'mark' => '='
                    );
                }
                if (!empty($_GET['keyword'])) {
                    $fetch_latest_news_page_data_array['where'][] = array(
                        'column' => 'title',
                        'value' => '%' . $_GET['keyword'] . '%',
                        'mark' => 'LIKE'
                    );
                }
            }
            $fl['latest_page_news'] = FL_FetchDataFromDB($fetch_latest_news_page_data_array);
            $len                    = count($fl['latest_page_news']);
            $page_to_load = 'home/latest-news';
            if (!empty($_GET['page'])) {
                if ($_GET['page'] == 'search') {
                    $page_to_load = 'search/lists/news';
                }
            }
            $types_array = array('news' => 'news',
                                 'list' => 'lists',
                                 'poll' => 'polls',
                                 'video' => 'videos',
                                 'music' => 'music',
                                 'quiz' => 'quiz');
            foreach ($fl['latest_page_news'] as $key => $fl['latest_news_data']):
                $fl['latest_news_data']['news']['page_type'] = $types_array[$_GET['type']];
                $fl['latest_news_data']['last'] = false;
                if ($key == $len - 1) {
                    $fl['latest_news_data']['last'] = true;
                }
                if ($page_to_load == 'search/lists/news') {
                    echo FL_Loadpage('search/lists/news', array(
                        'NEWS_ID' => $fl['latest_news_data']['news']['id'],
                        'NEWS_URL' => $fl['latest_news_data']['news']['url'],
                        'NEWS_ENCODED_URL' => urlencode($fl['latest_news_data']['news']['url']),
                        'NEWS_IMAGE' => FL_GetMedia($fl['latest_news_data']['news']['image']),
                        'NEWS_TITLE' => $fl['latest_news_data']['news']['title'],
                        'NEWS_DESC' => mb_substr($fl['latest_news_data']['news']['description'], 0, 100, "UTF-8") . '..',
                        'NEWS_POSTED' => $fl['latest_news_data']['news']['posted'],
                        'NEWS_PUBLISHER__NAME' => $fl['latest_news_data']['news']['publisher']['name'],
                        'NEWS_PUBLISHER__URL' => $fl['latest_news_data']['news']['publisher']['url']
                    ));
                } else {
                    echo FL_LoadPage($page_to_load);
                }
            endforeach;
            exit();
        }
    } else {
        $data = array(
            'status' => 400,
            'message' => $lang['error_found_while_loading']
        );
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($f == 'upload_opt_img') {
    if (isset($_FILES['image']) && FL_CheckSession($hash_id) === true) {
        $error      = false;
        $data       = array();
        $max_up     = $fl['config']['upload'];
        if ($_FILES['image']['size'] > $max_up) {
            $max_up          = FL_SizeUnits($max_up);
            $data['status']  = 401;
            $data['message'] = $lang['max_upload_size_is'] . " $max_up";
            $error           = true;
        }
        if (empty($error)) {
            $fileInfo  = array(
                'file' => $_FILES["image"]["tmp_name"],
                'name' => $_FILES['image']['name'],
                'size' => $_FILES["image"]["size"],
                'type' => $_FILES["image"]["type"],
                'crop' => array(
                    'width' => 285,
                    'height' => '250'
                )
            );

            $get_image = FL_ShareFile($fileInfo);
            if (!empty($get_image)) {
                $media = $get_image['filename'];
                $html  = '<img src="' . FL_GetMedia($media) . '">';
                $data  = array(
                    'status' => 200,
                    'img' => FL_GetMedia($media),
                    'html' => $html
                );
            }

            else {
                $data = array(
                    'status' => 400,
                    'message' => $lang['error_found_while_uploading']
                );
            }
        }

        

        
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($f == 'comments') {
    if ($s == 'insert' && !empty($_POST['text']) && !empty($_POST['news_id']) && is_numeric($_POST['news_id']) && $fl['loggedin'] == true) {
        $error    = false;
        if (empty($_POST['page']) || !in_array($_POST['page'], $fl_pages)) {
            $error = true;
        }
        if (strlen($_POST['text']) > 600) {
            $error = true;
        }
        if (!$error) {
            $id      = FL_Secure($_POST['news_id']);
            $text    = FL_Secure($_POST['text']);
            $page    = FL_Secure($_POST['page']);
            $data    = array(
                'status' => 400
            );
            $re_data = array(
                'user_id' => $fl['user']['user_id'],
                'news_id' => $id,
                'page' => $page,
                'text' => $text,
                'time' => time()
            );
            $comm_id = FL_RegisterComment($re_data);
            if ($comm_id && is_numeric($comm_id)) {
                $fl['comment'] = FL_CommentData($comm_id);
                $html          = FL_LoadPage("comment/comment-content", array(
                    'COMM_ID' => $fl['comment']['id'],
                    'STORY_PAGE' => $page,
                    'POST_ID' => $id,
                    'COMM_TEXT' => $fl['comment']['text'],
                    'COMM_TIME' => FL_Time_Elapsed_String($fl['comment']['time']),
                    'COMM_USER_NAME' => $fl['comment']['user_data']['name'],
                    'USER_VERIFIED' => ($fl['comment']['user_data']['verified'] == 1) ? '<span class="verified-icon"><i class="fa fa-check-circle"></i></span>' : '',
                    'COMM_USER_URL' => $fl['comment']['user_data']['url'],
                    'COMM_USER_AVATAR' => $fl['comment']['user_data']['avatar'],
                    'COMM_REPLIES' => ''
                ));
                $data          = array(
                    'status' => 200,
                    'html' => $html
                );
            }
        } else {
            $data = array(
                'status' => 400
            );
        }
    }
    if ($s == 'reply' && !empty($_POST['text']) && !empty($_POST['id']) && is_numeric($_POST['id']) && $fl['loggedin'] == true) {
        $error    = false;
        $fl_pages = array(
            'news',
            'polls',
            'videos',
            'lists',
            'music',
            'quiz'
        );
        if (empty($_POST['page']) || !in_array($_POST['page'], $fl_pages)) {
            $error = true;
        }
        if (strlen($_POST['text']) > 600) {
            $error = true;
        }
        if (!$error) {
            $id      = FL_Secure($_POST['id']);
            $text    = FL_Secure($_POST['text']);
            $news_id = FL_Secure($_POST['news_id']);
            $page    = FL_Secure($_POST['page']);
            $data    = array(
                'status' => 400
            );
            $re_data = array(
                'user_id' => $fl['user']['user_id'],
                'comment' => $id,
                'news_id' => $news_id,
                'page' => $page,
                'text' => $text,
                'time' => time()
            );
            $comm_id = FL_RegisterReply($re_data);
            if ($comm_id && is_numeric($comm_id)) {
                $fl['reply'] = FL_CommentReplyData($comm_id);
                $html        = FL_LoadPage("comment/comment-reply", array(
                    'REPLY_ID' => $fl['reply']['id'],
                    'COMM_ID' => $fl['reply']['comment'],
                    'POST_ID' => $fl['reply']['news_id'],
                    'REPLY_TEXT' => $fl['reply']['text'],
                    'REPLY_TIME' => FL_Time_Elapsed_String($fl['reply']['time']),
                    'REPLY_USER_NAME' => $fl['reply']['user_data']['name'],
                    'USER_VERIFIED' => ($fl['reply']['user_data']['verified'] == 1) ? '<span class="verified-icon"><i class="fa fa-check-circle"></i></span>' : '',
                    'REPLY_USER_URL' => $fl['reply']['user_data']['url'],
                    'REPLY_USER_AVATAR' => $fl['reply']['user_data']['avatar']
                ));
                $data        = array(
                    'status' => 200,
                    'html' => $html
                );
            }
        }
    }
    if ($s == 'delete' && !empty($_POST['page']) && !empty($_POST['id']) && is_numeric($_POST['id']) && $fl['loggedin'] == true) {
        $error    = false;
        $fl_pages = array(
            'news',
            'polls',
            'videos',
            'lists',
            'music',
            'quiz'
        );
        if (empty($_POST['page']) || !in_array($_POST['page'], $fl_pages)) {
            $error = true;
        }
        if (!$error) {
            $id   = FL_Secure($_POST['id']);
            $page = FL_Secure($_POST['page']);
            $data = array(
                'status' => 400
            );
            if (FL_DeleteComment($id, $page)) {
                $data = array(
                    'status' => 200
                );
            }
        }
    }
    if ($s == 'delete-reply' && !empty($_POST['id']) && is_numeric($_POST['id']) && $fl['loggedin'] == true) {
        $data = array(
            'status' => 400
        );
        $id   = FL_Secure($_POST['id']);
        if (FL_DeleteReply($id)) {
            $data = array(
                'status' => 200
            );
        }
    }
    if ($s == 'load') {
        $data     = array(
            'status' => 404
        );
        $error    = false;
        $fl_pages = array(
            'news',
            'polls',
            'videos',
            'lists',
            'music',
            'quiz'
        );
        if (empty($_POST['page']) || !in_array($_POST['page'], $fl_pages)) {
            $error = true;
        }
        if (empty($_GET['after_id']) || !is_numeric($_GET['after_id']) || $_GET['after_id'] < 1) {
            $error = true;
        }
        if (empty($_GET['post']) || !is_numeric($_GET['post']) || $_GET['post'] < 1) {
            $error = true;
        }
        $offset   = FL_Secure($_GET['after_id']);
        $page     = FL_Secure($_GET['page']);
        $post     = FL_Secure($_GET['post']);
        $html     = '';
        $comments = FL_GetStoryComments(array(
            'page' => $page,
            'offset' => $offset,
            'post_id' => $post
        ));
        if (count($comments) > 0) {
            foreach ($comments as $key => $fl['comment']) {
                $html .= FL_LoadPage("comment/comment-content", array(
                    'COMM_ID' => $fl['comment']['id'],
                    'STORY_PAGE' => $page,
                    'POST_ID' => $post,
                    'COMM_TEXT' => $fl['comment']['text'],
                    'COMM_TIME' => FL_Time_Elapsed_String($fl['comment']['time']),
                    'COMM_USER_NAME' => $fl['comment']['user_data']['name'],
                    'USER_VERIFIED' => ($fl['comment']['user_data']['verified'] == 1) ? '<span class="verified-icon"><i class="fa fa-check-circle"></i></span>' : '',
                    'COMM_USER_URL' => $fl['comment']['user_data']['url'],
                    'COMM_USER_AVATAR' => $fl['comment']['user_data']['avatar'],
                    'COMM_REPLIES' => ''
                ));
            }
            $data = array(
                'status' => 200,
                'html' => $html
            );
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($f == 'reaction') {
    if ($s == 'insert') {
        $error    = false;
        
        $data     = array(
            'status' => 304
        );
        if (empty($_POST['id']) || !is_numeric($_POST['id']) || $_POST['id'] < 1) {
            $error = true;
        }
        if (empty($_POST['post_id']) || !is_numeric($_POST['post_id']) || $_POST['post_id'] < 1) {
            $error = true;
        }
        if (empty($_POST['page']) || !in_array($_POST['page'], $fl_pages)) {
            $error = true;
        }
        if (!$error) {
            $re_data = array(
                'user_id' => ($fl['loggedin'] == true) ? $fl['user']['user_id'] : 0,
                'post_id' => FL_Secure($_POST['post_id']),
                'page' => FL_Secure($_POST['page']),
                'option_id' => FL_Secure($_POST['id']),
                'time' => time()
            );
            if ($fl['loggedin'] == false) {
                $re_data['ip_address'] = $_SERVER['REMOTE_ADDR'];
            }
            if (FL_IsReactionExists($_POST['post_id'], $_POST['id'])) {
                FL_DeleteReaction($_POST['post_id'], $_POST['id']);
            }
            //if (!FL_IsReactionExists($_POST['post_id'], $_POST['id'])) {
                $re_id = FL_RegisterReaction($re_data);
                if ($re_id) {
                    $data = array(
                        'status' => 200,
                        'data_option' => FL_GetPercentageOfReactions($_POST['post_id'], $_POST['page'])
                    );
                }
            // }
            // else{

            // }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
}
if ($f == 'home') {
    if ($s == 'load') {
        $data  = array(
            'status' => 404
        );
        $error = false;
        if (empty($_GET['after_id']) || !is_numeric($_GET['after_id']) || $_GET['after_id'] < 1) {
            $error = true;
        }
        if (empty($_GET['before_id']) || !is_numeric($_GET['before_id']) || $_GET['before_id'] < 1) {
            $error = true;
        }
        $offset         = FL_Secure($_GET['after_id']);
        $before_id      = FL_Secure($_GET['before_id']);
        $html           = '';
        $fl['top_news'] = FL_GetMoreNews($offset, $before_id);
        $top_news_html  = '';
        if (is_array($fl['top_news']) && count($fl['top_news']) > 0) {
            foreach ($fl['top_news'] as $key => $fl['latest_news_data']) {
                $top_news_html .= FL_Loadpage('home/latest-news');
            }
            $data = array(
                'status' => 200,
                'html' => $top_news_html
            );
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($f == 'verification' && $fl['loggedin'] == true) {
    if ($s == 'request' && !FL_IsVerificationRequestExists()) {
        $data  = array(
            'status' => 400
        );
        $error = false;
        if (empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['text']) || empty($_FILES['passport']) || empty($_FILES['image'])) {
            $error = $error_icon . $lang['please_fill_info'];
        } else {
            if (!empty($_FILES["image"]["error"]) || !empty($_FILES["passport"]["error"])) {
                $error = $error_icon . $lang['file_is_big'];
            } else if (strlen($_POST['first_name']) < 4 || strlen($_POST['first_name']) > 32) {
                $error = $error_icon . $lang['enter_valid_name'];
            } else if (strlen($_POST['last_name']) > 32) {
                $error = $error_icon . $lang['invalid_last_name'];
            } else if (!file_exists($_FILES['passport']['tmp_name'])) {
                $error = $error_icon . $lang['id_file_invalid'];
            } else if (!file_exists($_FILES['image']['tmp_name'])) {
                $error = $error_icon . $lang['img_file_invalid'];
            } else if (file_exists($_FILES["passport"]["tmp_name"])) {
                $image = getimagesize($_FILES["passport"]["tmp_name"]);
                if (!in_array($image[2], array(
                    IMAGETYPE_GIF,
                    IMAGETYPE_JPEG,
                    IMAGETYPE_PNG,
                    IMAGETYPE_BMP
                ))) {
                    $error = $error_icon . $lang['id_file_mustbe_img'];
                }
            } else if (file_exists($_FILES["image"]["tmp_name"])) {
                $image = getimagesize($_FILES["image"]["tmp_name"]);
                if (!in_array($image[2], array(
                    IMAGETYPE_GIF,
                    IMAGETYPE_JPEG,
                    IMAGETYPE_PNG,
                    IMAGETYPE_BMP
                ))) {
                    $error = $error_icon . $lang['user_file_mustbe_img'];
                }
            } else if (!empty($_FILES["image"]["error"]) || !empty($_FILES["passport"]["error"])) {
                $error = $error_icon . $lang['file_is_big'];
            }
        }
        if (empty($error)) {
            $re_data    = array(
                'user_id' => $fl['user']['user_id'],
                'name' => FL_Secure($_POST['first_name']) . ' ' . FL_Secure($_POST['last_name']),
                'message' => FL_Secure($_POST['text']),
                'time' => time()
            );
            $request_id = FL_RegisterVerificationRequest($re_data);
            if ($request_id && is_numeric($request_id)) {
                $up_data              = array();
                $image_file_info      = array(
                    'file' => $_FILES['image']['tmp_name'],
                    'size' => $_FILES['image']['size'],
                    'name' => $_FILES['image']['name'],
                    'type' => $_FILES['image']['type']
                );
                $image_file_upload    = FL_ShareFile($image_file_info);
                $passport_file_info   = array(
                    'file' => $_FILES['passport']['tmp_name'],
                    'size' => $_FILES['passport']['size'],
                    'name' => $_FILES['passport']['name'],
                    'type' => $_FILES['passport']['type']
                );
                $passport_file_upload = FL_ShareFile($passport_file_info);
                if (!empty($image_file_upload) && $passport_file_upload) {
                    $up_data['photo']    = $image_file_upload['filename'];
                    $up_data['passport'] = $passport_file_upload['filename'];
                    if (FL_UpdateVerificationRequest($request_id, $up_data)) {
                        $data = array(
                            'status' => 200,
                            'message' => $lang['verif_request_sent'],
                            'url' => $fl['config']['site_url']
                        );
                    }
                } else {
                    $data = array(
                        'status' => 400,
                        'message' => $success_icon . $lang['invalid_verif_request'],
                        'url' => $fl['config']['site_url']
                    );
                }
            }
        } else {
            $data['message'] = $error;
        }
    }
    if ($s == 'verify' && (FL_IsAdmin() || FL_IsModerator()) && !empty($_GET['id']) && is_numeric($_GET['id'])) {
        $data = array(
            'status' => 304
        );
        $id   = $_GET['id'];
        if (FL_VerifyUser($id)) {
            $data['status'] = 200;
        }
    }
    if ($s == 'ignore' && (FL_IsAdmin() || FL_IsModerator()) && !empty($_GET['id']) && is_numeric($_GET['id'])) {
        $data = array(
            'status' => 304
        );
        $id   = $_GET['id'];
        if (FL_DeleteVerificationRequest($id)) {
            $data['status'] = 200;
        }
    }
    if ($s == 'load' && (FL_IsAdmin() || FL_IsModerator()) && !empty($_GET['id']) && is_numeric($_GET['id'])) {
        $data          = '';
        $id            = $_GET['id'];
        $fl['request'] = FL_VerificationRequestData($id);
        if (!empty($fl['request'])) {
            $data = FL_LoadAdminPage("manage-verification-requests/view", array(
                'REQUEST_ID' => $fl['request']['id'],
                'REQUEST_USERNAME' => $fl['request']['name'],
                'REQUEST_TYPE' => $fl['request']['type'],
                'REQUEST_MESSAGE' => $fl['request']['message'],
                'REQUEST_USER_PHOTO' => FL_GetMedia($fl['request']['photo']),
                'REQUEST_USER_ID' => FL_GetMedia($fl['request']['passport'])
            ));
            ;
        }
        echo $data;
        exit();
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($f == 'quiz') {
    if ($s == 'add-result' && $fl['loggedin'] == true) {
        $data = array(
            'status' => 400
        );
        $time = time() . rand(1111, 9999);
        $type = 'results';
        if (!empty($_GET['type']) && $_GET['type'] == 'trivia') {
            $type = 'trivia_results';
        }
        $html = FL_LoadPage("create-new/entries/".$type, array(
            'ENTRY_TIME' => $time
        ));
        if (!empty($html)) {
            $data['status'] = 200;
            $data['html']   = $html;
        }
    }
    if ($s == 'add-question' && $fl['loggedin'] == true) {
        $data = array(
            'status' => 400
        );
        $time = time() . rand(1111, 9999);
        $type = 'question';
        if (!empty($_GET['type']) && $_GET['type'] == 'trivia') {
            $type = 'trivia_question';
        }
        elseif (!empty($_GET['type']) && $_GET['type'] == 'flip') {
            $type = 'flip_question';
        }
        $html = FL_LoadPage("create-new/entries/".$type, array(
            'ENTRY_TIME' => $time
        ));
        if (!empty($html)) {
            $data['status'] = 200;
            $data['html']   = $html;
        }
    }
    if ($s == 'upload' && $fl['loggedin'] == true) {
        if (isset($_FILES['image']) && FL_CheckSession($hash_id) === true) {
            $data   = array('status' => 400);
            $max_up = $fl['config']['upload'];
            $error  = false;

            if ($_FILES['image']['size'] > $max_up) {
                $max_up          = FL_SizeUnits($max_up);
                $data['status']  = 401;
                $data['message'] = $lang['max_upload_size_is'] . " $max_up";
                $error           = true;
            }
            if (empty($error)) {
                $fileInfo  = array(
                    'file' => $_FILES["image"]["tmp_name"],
                    'name' => $_FILES['image']['name'],
                    'size' => $_FILES["image"]["size"],
                    'type' => $_FILES["image"]["type"],
                    'crop' => array(
                        'width' => 285,
                        'height' => 250
                    )
                );
                $get_image = FL_ShareFile($fileInfo);
                if (!empty($get_image)) {
                    $media = $get_image['filename'];
                    $html  = '<img src="' . FL_GetMedia($media) . '">';
                    $data  = array(
                        'status' => 200,
                        'img' => FL_GetMedia($media),
                        'html' => $html,
                        'image_src' => $media
                    );
                }
                else {
                    $data = array(
                        'status' => 400,
                        'message' => $lang['error_found_while_uploading']
                    );
                }
            }
        }
    }
    if ($s == 'answer' && $fl['loggedin'] == true) {
        if (!empty($_GET['id']) && isset($_GET['index']) && is_numeric($_GET['index']) && FL_CheckSession($hash_id) === true) {
            $data  = array(
                'status' => 400
            );
            $time  = FL_Secure($_GET['id']);
            $index = FL_Secure($_GET['index']);
            $type = 'quiz-answer';
            if (!empty($_GET['type']) && $_GET['type'] == 'trivia') {
                $type = 'trivia-answer';
            }
            $html  = FL_LoadPage("create-new/entries/includes/".$type, array(
                'ENTRY_TIME' => $time,
                'INDEX' => ($index + 1)
            ));
            if (!empty($html)) {
                $data['status'] = 200;
                $data['html']   = $html;
            }
        }
    }
    if ($s == 'result') {
        if (!empty($_GET['post']) && !empty($_GET['index']) && is_numeric($_GET['index']) && FL_CheckSession($hash_id) === true) {
            $data              = array(
                'status' => 400
            );
            $post              = FL_Secure($_GET['post']);
            $index             = FL_Secure($_GET['index']);
            $fl['quiz-result'] = FL_GetQuizResult($post, $index);
            if ($fl['quiz-result'] && is_array($fl['quiz-result']) && !empty($fl['quiz-result'])) {
                $post_data = FL_GetPost($fl['quiz-result']['post_id'], 0, 'quiz');
                if (!empty($post_data) && is_array(FL_GetPost($fl['quiz-result']['post_id'], 0, 'quiz'))) {
                    if (!empty($data['modal']) || true) {
                        $data['status'] = 200;
                        $data['title']  = $lang['you_got'] . $fl['quiz-result']['title'];
                        $data['text']   = $fl['quiz-result']['text'];
                        $data['image']  = $fl['quiz-result']['image'];
                        $data['share']  = FL_LoadPage("entries/quiz-result", array(
                            'STORY_TITLE' => $fl['quiz-result']['title'],
                            'STORY_ENCODED_URL' => urlencode($post_data['url'] . "?r=" . $fl['quiz-result']['index_id'])
                        ));
                        $data['result'] = FL_LoadPage("entries/result", array(
                            'RESULT_TITLE' => $fl['quiz-result']['title'],
                            'RESULT_TEXT' => $fl['quiz-result']['text'],
                            'RESULT_IMG' => $fl['quiz-result']['image'],
                            'RESULT_ENCODED_URL' => urlencode($post_data['url'] . "?r=" . $fl['quiz-result']['index_id'])
                        ));
                    }
                }
            }
        }
        else if(!empty($_GET['answer_id']) && is_numeric($_GET['answer_id']) && $_GET['answer_id'] > 0 && !empty($_GET['post']) && is_numeric($_GET['post']) && $_GET['post'] > 0){
            $final = 'notcorrect';
            $post_id              = FL_Secure($_GET['post']);
            $answer_id         = FL_Secure($_GET['answer_id']);
            $questions_count = FL_GetQuestionsCount($post_id);
            $data['result'] = '';
            $result = FL_IsCorrectAnswer($answer_id);
            $all_results = FL_GetAllQuizResult($post_id);
            
            $answer = FL_GetAnswerById($answer_id);
            $question = FL_GetEntryById($answer['entry_id']);
            if (!empty($question) && !empty($question['price'])) {
                if ($questions_count <= 1) {
                    if ($result) {
                        $final = 'correct';
                    }
                    else{
                        $question['price'] = 0;
                    }
                    $post_data = FL_GetPost($post_id, 0, 'quiz');
                    $data['result'] = '';
                    $data['title']  = $lang['you_got'] . $question['price'];
                    $data['text']   =  $lang['you_got'] . $question['price'];
                    $data['image']  = '';
                    $data['share']  = FL_LoadPage("entries/quiz-result", array(
                        'STORY_TITLE' => $lang['you_got'] . $question['price'],
                        'STORY_ENCODED_URL' => urlencode($post_data['url'] . "?r=")
                    ));
                }
                else{
                    $answers_count = 0;
                    $amount = 0;
                    if (!empty($_GET['answers_count']) && is_numeric($_GET['answers_count']) && $_GET['answers_count'] > 0) {
                        $answers_count = FL_Secure($_GET['answers_count']);
                    }
                    if (!empty($_GET['amount']) && is_numeric($_GET['amount']) && $_GET['amount'] > 0) {
                        $amount = FL_Secure($_GET['amount']);
                    }
                    if ($answers_count == ($questions_count - 1)) {
                        if ($result) {
                            $final = 'correct';
                            $question['price'] = $question['price'] + $amount;
                        }
                        else{
                            $question['price'] = $amount;
                        }
                        $post_data = FL_GetPost($post_id, 0, 'quiz');
                        $data['result'] = '';
                        $data['title']  = $lang['you_got'] . $question['price'];
                        $data['text']   =  $lang['you_got'] . $question['price'];
                        $data['image']  = '';
                        $data['share']  = FL_LoadPage("entries/quiz-result", array(
                            'STORY_TITLE' => $lang['you_got'] . $question['price'],
                            'STORY_ENCODED_URL' => urlencode($post_data['url'] . "?r=")
                        ));
                    }
                    else{
                        $data['amount']  = 0;
                        if ($result) {
                            $final = 'correct';
                            $data['amount']  = $question['price'];
                        }
                        $data['result'] = '';
                        $data['title']  = '';
                        $data['text']   = '';
                        $data['image']  = '';
                        $data['share']  = '';
                    }
                }
            }
            else{
                if ($questions_count <= 1) {
                    $fl['quiz-result'] = $all_results[0];
                    if ($result) {
                        $final = 'correct';
                        $fl['quiz-result'] = $all_results[(count($all_results) - 1)];
                    }
                    $post_data = FL_GetPost($fl['quiz-result']['post_id'], 0, 'quiz');
                    $data['result'] = FL_LoadPage("entries/result", array(
                        'RESULT_TITLE' => $fl['quiz-result']['title'],
                        'RESULT_TEXT' => $fl['quiz-result']['text'],
                        'RESULT_IMG' => $fl['quiz-result']['image'],
                        'RESULT_ENCODED_URL' => urlencode($post_data['url'] . "?r=" . $fl['quiz-result']['index_id'])
                    ));
                    $data['title']  = $lang['you_got'] . $fl['quiz-result']['title'];
                    $data['text']   = $fl['quiz-result']['text'];
                    $data['image']  = $fl['quiz-result']['image'];
                    $data['share']  = FL_LoadPage("entries/quiz-result", array(
                        'STORY_TITLE' => $fl['quiz-result']['title'],
                        'STORY_ENCODED_URL' => urlencode($post_data['url'] . "?r=" . $fl['quiz-result']['index_id'])
                    ));
                }
                else{
                    $answers_array = array();
                    if (empty($_COOKIE["quiz_".$post_id])) {
                        setcookie("quiz_".$post_id, implode(array($answer_id), ',') , time()+300);
                    }
                    else{
                        $answers_array = explode(',', $_COOKIE["quiz_".$post_id]);
                        if (count($answers_array) >= $questions_count) {
                            $answers_array = array();
                            setcookie("quiz_".$post_id, implode($answers_array, ',') , time()+300);
                            $_COOKIE["quiz_".$post_id] = array();
                            unset($_COOKIE["quiz_".$post_id]);
                            setcookie("quiz_".$post_id, null, -1);
                            setcookie("quiz_".$post_id, null, -1,'/');
                        }
                        $answers_array[] = $answer_id;
                        setcookie("quiz_".$post_id, implode($answers_array, ',') , time()+300);
                    }

                    if (count($answers_array) == $questions_count) {
                        $_COOKIE["quiz_".$post_id] = array();
                        unset($_COOKIE["quiz_".$post_id]);
                        setcookie("quiz_".$post_id, null, -1);
                        setcookie("quiz_".$post_id, null, -1,'/');
                        $correct = 0;
                        foreach ($answers_array as $key => $value) {
                            $answer_result = FL_IsCorrectAnswer($value);
                            if ($answer_result) {
                                $correct = $correct + 1;
                            }
                        }
                        $fl['quiz-result'] = $all_results[$correct];
                        if ($result) {
                            $final = 'correct';
                            $fl['quiz-result'] = $all_results[$correct];
                        }
                        $post_data = FL_GetPost($fl['quiz-result']['post_id'], 0, 'quiz');
                        $data['result'] = FL_LoadPage("entries/result", array(
                            'RESULT_TITLE' => $fl['quiz-result']['title'],
                            'RESULT_TEXT' => $fl['quiz-result']['text'],
                            'RESULT_IMG' => $fl['quiz-result']['image'],
                            'RESULT_ENCODED_URL' => urlencode($post_data['url'] . "?r=" . $fl['quiz-result']['index_id'])
                        ));
                        $data['title']  = $lang['you_got'] . $fl['quiz-result']['title'];
                        $data['text']   = $fl['quiz-result']['text'];
                        $data['image']  = $fl['quiz-result']['image'];
                        $data['share']  = FL_LoadPage("entries/quiz-result", array(
                            'STORY_TITLE' => $fl['quiz-result']['title'],
                            'STORY_ENCODED_URL' => urlencode($post_data['url'] . "?r=" . $fl['quiz-result']['index_id'])
                        ));
                    }
                    else{
                        if ($result) {
                            $final = 'correct';
                        }
                        $data['result'] = '';
                        $data['title']  = '';
                        $data['text']   = '';
                        $data['image']  = '';
                        $data['share']  = '';
                    }
                }
            }
            
            $data['status'] = 200;
            $data['final'] = $final;
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($f == 'brnews') {
    if ($s == 'insert' && (FL_IsAdmin() || FL_IsModerator())) {
        $data  = array(
            'status' => 400,
            'html' => ''
        );
        $error = false;
        if (empty($_POST['text']) || empty($_POST['time'])) {
            $error = $error_icon . $lang['please_fill_info'];
        } else {
            if (!empty($_POST['url'])) {
                if (!FL_IsUrl($_POST['url'])) {
                    $error = $error_icon . $lang['invalid_news_url'];
                }
            }
            if (!is_numeric($_POST['time']) || $_POST['time'] < 1) {
                $error = $error_icon . $lang['invalid_time'];
            }
        }
        if (empty($error)) {
            $expire     = intval(FL_Secure($_POST['time']));
            $active     = (!empty($_POST['publish'])) ? 1 : 0;
            $table      = T_BR_NEWS;
            $re_data    = array(
                'user_id' => $fl['user']['user_id'],
                'expire' => (time() + (3600 * $expire)),
                'url' => FL_Secure($_POST['url']),
                'text' => FL_Secure(FL_ShortText(strip_tags($_POST['text']), 600)),
                'active' => $active,
                'time' => $expire,
                'posted' => time()
            );
            $br_news_id = FL_InsertData($re_data, $table);
            if (is_numeric($br_news_id) && $br_news_id > 0) {
                $fetch_brnews_data_array = array(
                    'table' => T_BR_NEWS,
                    'column' => 'id',
                    'limit' => 1,
                    'order' => array(
                        'type' => 'DESC',
                        'column' => 'id'
                    ),
                    'where' => array(
                        array(
                            'column' => 'id',
                            'value' => $br_news_id,
                            'mark' => '='
                        )
                    ),
                    'final_data' => array(
                        array(
                            'function_name' => 'FL_GetBrNews',
                            'column' => 'id'
                        )
                    )
                );
                $breaking_news_data      = FL_FetchDataFromDB($fetch_brnews_data_array);
                foreach ($breaking_news_data as $fl['brnews']) {
                    $data['html'] .= FL_LoadAdminPage("manage-breaking-news/list", array(
                        'BRNEWS_ID' => $fl['brnews']['id'],
                        'BRNEWS_EXPIRE' => $fl['brnews']['expire'],
                        'BRNEWS_URL' => $fl['brnews']['url'],
                        'BRNEWS_STATUS' => $fl['brnews']['active']
                    ));
                }
                $data['status']  = 200;
                $data['message'] = $success_icon . $lang['br_news_added'];
            }
        } else {
            $data['status']  = 400;
            $data['message'] = $error;
        }
    }
    if ($s == 'update' && (FL_IsAdmin() || FL_IsModerator())) {
        $data  = array(
            'status' => 400,
            'html' => ''
        );
        $error = false;
        if (empty($_POST['text']) || empty($_POST['time']) || empty($_POST['id'])) {
            $error = $error_icon . $lang['please_fill_info'];
        } else {
            if (!empty($_POST['url'])) {
                if (!FL_IsUrl($_POST['url'])) {
                    $error = $error_icon . $lang['invalid_news_url'];
                }
            }
            if (!is_numeric($_POST['time']) || $_POST['time'] < 1) {
                $error = $error_icon . $lang['invalid_time'];
            }
            if (!is_numeric($_POST['id']) || $_POST['id'] < 1) {
                $error = $error_icon . $lang['please_check_details'];
            }
        }
        if (empty($error)) {
            $expire  = intval(FL_Secure($_POST['time']));
            $id      = FL_Secure($_POST['id']);
            $active  = (!empty($_POST['publish'])) ? 1 : 0;
            $table   = T_BR_NEWS;
            $re_data = array(
                'expire' => (time() + (3600 * $expire)),
                'url' => FL_Secure($_POST['url']),
                'text' => FL_Secure(FL_ShortText(strip_tags($_POST['text']), 600)),
                'active' => $active,
                'time' => $expire,
                'posted' => time()
            );
            if (FL_UpdateData($id, $re_data, $table)) {
                $fetch_brnews_data_array = array(
                    'table' => T_BR_NEWS,
                    'column' => 'id',
                    'limit' => 1,
                    'order' => array(
                        'type' => 'DESC',
                        'column' => 'id'
                    ),
                    'where' => array(
                        array(
                            'column' => 'id',
                            'value' => $id,
                            'mark' => '='
                        )
                    ),
                    'final_data' => array(
                        array(
                            'function_name' => 'FL_GetBrNews',
                            'column' => 'id'
                        )
                    )
                );
                $breaking_news_data      = FL_FetchDataFromDB($fetch_brnews_data_array);
                foreach ($breaking_news_data as $fl['brnews']) {
                    $data['html'] .= FL_LoadAdminPage("manage-breaking-news/list", array(
                        'BRNEWS_ID' => $fl['brnews']['id'],
                        'BRNEWS_EXPIRE' => $fl['brnews']['expire'],
                        'BRNEWS_URL' => $fl['brnews']['url'],
                        'BRNEWS_STATUS' => $fl['brnews']['active']
                    ));
                }
                $data['status']  = 200;
                $data['id']      = $id;
                $data['message'] = $success_icon . $lang['br_news_saved'];
            }
        } else {
            $data['status']  = 400;
            $data['message'] = $error;
        }
    }
    if ($s == 'edit' && !empty($_GET['id']) && is_numeric($_GET['id'])) {
        $data   = array(
            'status' => 404
        );
        $id     = FL_Secure($_GET['id']);
        $brnews = FL_GetBrNews($id);
        if (is_array($brnews) && !empty($brnews)) {
            $data['status'] = 200;
            $data['data']   = array(
                'text' => $brnews['text'],
                'url' => $brnews['url'],
                'time' => $brnews['time']
            );
        }
    }
    if ($s == 'delete' && !empty($_GET['id']) && is_numeric($_GET['id'])) {
        $data     = array(
            'status' => 304
        );
        $id       = FL_Secure($_GET['id']);
        $where    = array();
        $table    = T_BR_NEWS;
        $where[0] = array(
            'column' => '`id`',
            'value' => $id,
            'mark' => '='
        );
        if (FL_DeleteData($where, T_BR_NEWS)) {
            $data['status'] = 200;
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($f == 'announcement') {
    if ($s == 'insert' && (FL_IsAdmin() || FL_IsModerator())) {
        $data  = array(
            'status' => 400,
            'html' => ''
        );
        $error = false;
        if (empty($_POST['text'])) {
            $error = $error_icon . $lang['please_fill_info'];
        }
        if (empty($error)) {
            $table   = T_ANNOUNCEMENT;
            $re_data = array(
                'text' => FL_Secure(FL_ShortText($_POST['text'], 2000)),
                'time' => time(),
                'active' => 1
            );
            $id      = FL_InsertData($re_data, $table);
            if (is_numeric($id) && $id > 0) {
                $fetch_data_array  = array(
                    'table' => $table,
                    'column' => '*',
                    'limit' => 1,
                    'order' => array(
                        'type' => 'DESC',
                        'column' => 'id'
                    ),
                    'where' => array(
                        array(
                            'column' => 'id',
                            'value' => $id,
                            'mark' => '='
                        )
                    )
                );
                $announcement_data = FL_FetchDataFromDB($fetch_data_array);
                $table_views       = T_ANNOUNCEMENT_VIEWS;
                foreach ($announcement_data as $fl['announcement']) {
                    $fl['announcement']['time']  = FL_Time_Elapsed_String($fl['announcement']['time']);
                    $fl['announcement']['views'] = FL_CountData(array(
                        0 => array(
                            'column' => '`announcement_id`',
                            'value' => $fl['announcement']['id'],
                            'mark' => '='
                        )
                    ), $table_views);
                    $data['html'] .= FL_LoadAdminPage("manage-announcements/active", array(
                        'ANN_ID' => $fl['announcement']['id'],
                        'ANN_VIEWS' => $fl['announcement']['views'],
                        'ANN_TEXT' => FL_Decode($fl['announcement']['text']),
                        'ANN_TIME' => $fl['announcement']['time']
                    ));
                }
                $data['status'] = 200;
            }
        } else {
            $data['status']  = 400;
            $data['message'] = $error;
        }
    }
    if ($s == 'toggle' && (FL_IsAdmin() || FL_IsModerator()) && !empty($_GET['id']) && isset($_GET['a']) && is_numeric($_GET['id'])) {
        $data     = array(
            'status' => 400,
            'html' => ''
        );
        $table    = T_ANNOUNCEMENT;
        $id       = FL_Secure($_GET['id']);
        $action   = ($_GET['a'] == 0) ? 0 : 1;
        $ann_item = ($_GET['a'] == 0) ? 'inactive' : 'active';
        $up_data  = array(
            'active' => $action
        );
        if (FL_UpdateData($id, $up_data, $table)) {
            $fetch_data_array  = array(
                'table' => $table,
                'column' => '*',
                'limit' => 1,
                'order' => array(
                    'type' => 'DESC',
                    'column' => 'id'
                ),
                'where' => array(
                    array(
                        'column' => 'id',
                        'value' => $id,
                        'mark' => '='
                    )
                )
            );
            $table_views       = T_ANNOUNCEMENT_VIEWS;
            $announcement_data = FL_FetchDataFromDB($fetch_data_array);
            foreach ($announcement_data as $fl['announcement']) {
                $fl['announcement']['time']  = FL_Time_Elapsed_String($fl['announcement']['time']);
                $fl['announcement']['views'] = FL_CountData(array(
                    0 => array(
                        'column' => '`announcement_id`',
                        'value' => $fl['announcement']['id'],
                        'mark' => '='
                    )
                ), $table_views); 
                $data['html'] .= FL_LoadAdminPage("manage-announcements/$ann_item", array(
                    'ANN_ID' => $fl['announcement']['id'],
                    'ANN_VIEWS' => $fl['announcement']['views'],
                    'ANN_TEXT' => FL_Decode($fl['announcement']['text']),
                    'ANN_TIME' => $fl['announcement']['time']
                ));
            }
            $data['status'] = 200;
        }
    }
    if ($s == 'delete' && (FL_IsAdmin() || FL_IsModerator()) && !empty($_GET['id']) && is_numeric($_GET['id'])) {
        $data     = array(
            'status' => 400
        );
        $table    = T_ANNOUNCEMENT;
        $id       = FL_Secure($_GET['id']);
        $where    = array();
        $where[0] = array(
            'column' => '`id`',
            'value' => $id,
            'mark' => '='
        );
        if (FL_DeleteData($where, $table)) {
            $data['status'] = 200;
            $table          = T_ANNOUNCEMENT_VIEWS;
            $where[0]       = array(
                'column' => '`announcement_id`',
                'value' => $id,
                'mark' => '='
            );
            @FL_DeleteData($where, $table);
        }
    }
    if ($s == 'hide' && !empty($_GET['id']) && is_numeric($_GET['id']) && $fl['loggedin']) {
        $data    = array(
            'status' => 400
        );
        $table   = T_ANNOUNCEMENT_VIEWS;
        $id      = FL_Secure($_GET['id']);
        $re_data = array(
            'announcement_id' => $id,
            'user_id' => $fl['user']['user_id']
        );
        if (FL_InsertData($re_data, $table)) {
            $data['status'] = 200;
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($f == 'add_new_lang') {
    if ((FL_IsAdmin() || FL_IsModerator())) {
        $mysqli = Fl_LangsNamesFromDB();
        if (in_array($_POST['lang'], $mysqli)) {
            $data['status']  = 400;
            $data['message'] = 'This lang is already used.';
        } else {
            $lang_name = FL_Secure($_POST['lang']);
            $lang_name = strtolower($lang_name);
            $query     = mysqli_query($sqlConnect, "ALTER TABLE " . T_LANGS . " ADD `$lang_name` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;");
            if ($query) {
                $content = file_get_contents('assets/languages/extra/english.php');
                $fp      = fopen("assets/languages/extra/$lang_name.php", "wb");
                fwrite($fp, $content);
                fclose($fp);
                $english = Fl_LangsFromDB('english');
                foreach ($english as $key => $lang) {
                    $lang  = FL_Secure($lang);
                    $query = mysqli_query($sqlConnect, "UPDATE " . T_LANGS . " SET `{$lang_name}` = '$lang' WHERE `lang_key` = '{$key}'");
                }
                $data['status'] = 200;
            }
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($f == 'add_new_lang_key') {
    if ((FL_IsAdmin() || FL_IsModerator())) {
        if (!empty($_POST['lang_key'])) {
            $lang_key  = FL_Secure($_POST['lang_key']);
            $mysqli    = mysqli_query($sqlConnect, "SELECT COUNT(id) as count FROM " . T_LANGS . " WHERE `lang_key` = '$lang_key'");
            $sql_fetch = mysqli_fetch_assoc($mysqli);
            if ($sql_fetch['count'] == 0) {
                $mysqli = mysqli_query($sqlConnect, "INSERT INTO " . T_LANGS . " (`lang_key`) VALUE ('$lang_key')");
                if ($mysqli) {
                    $data['status'] = 200;
                    $data['url']    = FL_LoadAdminLinkSettings('manage-languages');
                }
            } else {
                $data['status']  = 400;
                $data['message'] = 'This key is already used, please use other one.';
            }
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($f == 'update_lang_status') {
    $saveSetting = FL_SaveConfig($_POST['name'],$_POST['value']);
    $data = array(
                'status' => 200
            );
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($f == 'delete_lang') {
    if ((FL_IsAdmin() || FL_IsModerator())) {
        $mysqli = Fl_LangsNamesFromDB();
        if (in_array($_GET['id'], $mysqli)) {
            $lang_name = FL_Secure($_GET['id']);
            $query     = mysqli_query($sqlConnect, "ALTER TABLE " . T_LANGS . " DROP COLUMN `$lang_name`");
            if ($query) {
                unlink("assets/languages/extra/$lang_name.php");
                $data['status'] = 200;
            }
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($f == 'get_lang_key') {
    if ((FL_IsAdmin() || FL_IsModerator())) {
        $html  = '';
        $langs = Fl_GetLangDetails($_GET['id']);
        if (count($langs) > 0) {
            foreach ($langs as $key => $fl['langs']) {
                foreach ($fl['langs'] as $fl['key_'] => $fl['lang_vlaue']) {
                    $wo['is_editale'] = 0;
                    if ($_GET['lang_name'] == $fl['key_']) {
                        $wo['is_editale'] = 1;
                    }
                    $html .= FL_LoadAdminPage('edit-lang/form-list');
                }
            }
        } else {
            $html = "<h4>Keyword not found</h4>";
        }
        $data['status'] = 200;
        $data['html']   = $html;
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($f == 'update_lang_key') {
    if ((FL_IsAdmin() || FL_IsModerator())) {
        $array_langs = array();
        $lang_key    = FL_Secure($_POST['id_of_key']);
        $langs       = Fl_LangsNamesFromDB();
        foreach ($_POST as $key => $value) {
            if (in_array($key, $langs)) {
                $key   = FL_Secure($key);
                $value = FL_Secure($value);
                $value = mysqli_real_escape_string($sqlConnect,$value);
                $query = mysqli_query($sqlConnect, "UPDATE " . T_LANGS . " SET `{$key}` = '{$value}' WHERE `lang_key` = '{$lang_key}'");
                if ($query) {
                    $data['status'] = 200;
                }
            }
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($f == 'upload') {
    if ($s == 'video' && !empty($_FILES['video']['tmp_name'])) {
        $data  = array(
            'status' => 400
        );
        $error  = false;
        $max_up = $fl['config']['upload'];
        if (!empty($_FILES["video"]["error"])) {
            $data['status']  = 401;
            $data['message'] = $lang['file_is_big'];
            $error           = true;
        }
        
        elseif ($_FILES['video']['size'] > $max_up) {
            $max_up          = FL_SizeUnits($max_up);
            $data['status']  = 401;
            $data['message'] = $lang['max_upload_size_is'] . " $max_up";
            $error           = true;
        }

        
        elseif (!in_array($_FILES['video']['type'], $fl['vid_mime_types'])) {
            $error           = true;
        }
        

        if (empty($error)) {
            $file_info   = array(
                'file' => $_FILES['video']['tmp_name'],
                'size' => $_FILES['video']['size'],
                'name' => $_FILES['video']['name'],
                'type' => $_FILES['video']['type']
            );
            $file_upload = FL_ShareFile($file_info);
            if (!empty($file_upload['filename'])) {
                $data['filename']      = FL_GetMedia($file_upload['filename']);
                $data['status']        = 200;
                $data['html']          = FL_LoadPage('players/video', array(
                    'VIDEO_SRC' => FL_GetMedia($file_upload['filename'])
                ));
                $_SESSION['uploads'][] = $data['filename'];
            }
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($f == 'site') {
    if ($s == 'report' && $fl['loggedin'] == true) {
        $is_rvalid = (!empty($_GET['id']) && !empty($_GET['page']) && in_array($_GET['page'], $fl_pages) && is_numeric($_GET['id']));
        $data      = array('status' => 400);

        if ($is_rvalid === true) {
            $id          = $_GET['id'];
            $table       = T_REPORTS;
            $page        = $_GET['page']; 
            $user_id     = $fl['user']['user_id'];
            $where       = array();
            $query_cols  = array('`post_id`' => $id,'`user_id`' => $user_id,'`type`' => $page);
            foreach ($query_cols as $col => $col_val) {
                $where[] = array(
                    'column' => $col,
                    'value'  => $col_val,
                    'mark'   => '=',
                );
            }
            $user_reports = FL_CountData($where,$table);
            if (is_numeric($user_reports) && $user_reports > 0) {
                FL_DeleteData($where,$table);
                $data['code'] = 0;
            }
            else{
                $re_data = array(
                    'post_id' => $id,
                    'user_id' => $user_id,
                    'type'    => $page,
                    'time'    => time()
                );
                FL_InsertData($re_data,$table);
                $data['code'] = 1;
            }

            $data['status']   = 200;

        }
    }

    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}


