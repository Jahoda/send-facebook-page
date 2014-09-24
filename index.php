<?php

// config
define('APP_ID', '');
define('APP_SECRET', '');
define("PAGE_ID", "");
define("SCRIPT_URL", "http://example.com"); // URL for redirect back from FB

function doWallPost($postName = '', $postMessage = '', $postLink = '', $postCaption = '', $postDescription = '') {
    $FB_APP_ID = APP_ID;
    $FB_APP_SECRET = APP_SECRET;

    $code = $_REQUEST["code"];

    if (empty($code)) {
        $dialog_url = "http://www.facebook.com/dialog/oauth?client_id=" . $FB_APP_ID . "&redirect_uri=" . urlencode(SCRIPT_URL) . "&scope=publish_stream";
        header("Location: $dialog_url");
    }

    $token_url = "https://graph.facebook.com/oauth/access_token?client_id=" . $FB_APP_ID . "&redirect_uri=" . urlencode(SCRIPT_URL) . "&client_secret=" . $FB_APP_SECRET . "&code=" . $code;

    $access_token = file_get_contents($token_url);

    $param1 = explode("&", $access_token);
    $param2 = explode("=", $param1[0]);
    $FB_ACCESS_TOKEN = $param2[1];


    $token_url = "https://graph.facebook.com/" . PAGE_ID . "/?fields=access_token&access_token=" . $FB_ACCESS_TOKEN;
    $pageAccessToken = file_get_contents($token_url);
    $pageAccessToken = json_decode($pageAccessToken, true);

    $FB_ACCESS_TOKEN = $pageAccessToken["access_token"];

    $url = "https://graph.facebook.com/" . PAGE_ID . "/feed";
    $attachment = array(
        'access_token' => $FB_ACCESS_TOKEN,
        'name' => $postName,
        'link' => $postLink,
        'description' => $postDescription,
        'message' => $postMessage,
        'caption' => $postCaption
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $attachment);
    $result = curl_exec($ch);
    header('Content-type:text/html');
    curl_close($ch);

    return $result;
}

// Test post
doWallPost("Test", "test");
