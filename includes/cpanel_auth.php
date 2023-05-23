<?php

// NOT CURRENTLY USED
//
// Should extend the plugin to support cpanel apikeys as well and depending on the user choice: pass or api, change the autherization header for the api request
//
// Problem is that for some requests we use curl and for others wp_remote_get
//
//
function pluginsclub_get_cpanel_auth() {
  $username = get_option('cpanel_username');
  $password = get_option('cpanel_password');
  $apikey = get_option('cpanel_apikey');

  if ($password) {
    $auth = base64_encode(get_option('cpanel_username') . ':' . get_option('cpanel_password'));
    $headers = array(
  'Authorization' => 'Basic ' . $auth,
  'Content-Type' => 'application/json'
);
  } elseif ($apikey) {
    $auth = "$username:$apikey";
    $headers = array(
      "Authorization: cpanel $auth",
      "Content-Type: application/json"
    );
  } else {
    // handle case when neither cpanel_password nor cpanel_apikey are set
    die;
  }

  return array('auth' => $auth, 'headers' => $headers);
}

$cpanel_auth = pluginsclub_get_cpanel_auth();


// Define cPanel login credentials as constants
define('CPANEL_USERNAME', get_option('cpanel_username'));
define('CPANEL_PASSWORD', get_option('cpanel_password'));
define('CPANEL_HOSTNAME', get_option('cpanel_host'));
define('CPANEL_APIKAY', get_option('cpanel_apikey'));
define('CPANEL_AUTH', $cpanel_auth['auth']);
define('CPANEL_HEADERS', $cpanel_auth['headers']);