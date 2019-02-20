<?php

ini_set('display_errors', 'on');
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

try {
	require_once __DIR__.'/setup.php';

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