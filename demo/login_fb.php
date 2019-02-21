<?php

session_start();

ini_set('display_errors', 'on');

use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;

require_once __DIR__.'/../vendor/autoload.php'; // change path as needed

if(!is_file(__DIR__.'/app_key.txt') || !is_file(__DIR__.'/secret.txt')) {
	exit('Vous devez créer les fichier app_key.txt et secret.txt');
}


// Use one of the helper classes to get a Facebook\Authentication\AccessToken entity.
//   $helper = $fb->getRedirectLoginHelper();
//   $helper = $fb->getJavaScriptHelper();
//   $helper = $fb->getCanvasHelper();
//   $helper = $fb->getPageTabHelper();


$fb = new Facebook([
	'app_id' => file_get_contents(__DIR__.'/app_key.txt'),
	'app_secret' => file_get_contents(__DIR__.'/secret.txt'),
	'default_graph_version' => 'v2.10',
]);

if(isset($_SESSION['fb_access_token'])) {
	try {
		// Get the \Facebook\GraphNodes\GraphUser object for the current user.
		// If you provided a 'default_access_token', the '{access-token}' is optional.
		$response = $fb->get('/me', '{access-token}');
	} catch(FacebookResponseException $e) {
		// When Graph returns an error
		echo 'Graph returned an error: ' . $e->getMessage();
		exit;
	} catch(FacebookSDKException $e) {
		// When validation fails or other local issues
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}

	$me = $response->getGraphUser();
	echo 'Logged in as '.$me->getName();
}
else {
	$helper = $fb->getRedirectLoginHelper();

	$permissions = ['email']; // Optional permissions
	$loginUrl    = $helper->getLoginUrl('http://pizzygo.local:2107/fb-callback.php', $permissions);

	echo '<a href="'.htmlspecialchars($loginUrl).'">Log in with Facebook!</a>';
}