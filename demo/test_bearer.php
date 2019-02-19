<?php

require_once __DIR__.'/../autoload.php';

use MiladRahimi\Jwt\Authentication\Authentication;
use MiladRahimi\Jwt\Authentication\Subdomains;
ini_set('display_errors', 'on');
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

try {
	$auth        = new Authentication();
	$sub_domains = Subdomains::create();

	$domain      = explode('.', $_SERVER['HTTP_HOST']);
	if (count($domain) >= 3) {
		array_shift($domain);
	}
	$domain = implode('.', $domain);
	$domain = explode(':', $domain)[0];

	$sub_domains->set_domain($domain);

	$php_input = getallheaders();
	$access_token_received = str_replace('Bearer ', '', $php_input['Authentication']);

	if($auth->authenticated() && $auth->get_token() === $access_token_received) {
		echo json_encode(
			[
				'forbidden' => false,
			]
		);
	}
	else {
		echo json_encode(
			[
				'forbidden' => true,
			]
		);
	}
}
catch (Exception $e) {
	echo json_encode(
		[
			'error' => true,
		]
	);
}