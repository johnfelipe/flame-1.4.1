<?php
if (!empty($_GET['type'])) {
    if ($_GET['type'] == 'day') {
        setcookie("mode", 'day', time() + (10 * 365 * 24 * 60 * 60), '/');
        $_COOKIE['mode'] = 'day';
        $fl['mode_link'] = 'night';
        $fl['mode_text'] = $fl['lang']['night_mode'];
    } else if ($_GET['type'] == 'night') {
        setcookie("mode", 'night', time() + (10 * 365 * 24 * 60 * 60), '/');
        $_COOKIE['mode'] = 'night';
        $fl['mode_link'] = 'day';
        $fl['mode_text'] = $fl['lang']['day_mode'];
    }
}