<?php

try {
	require_once __DIR__.'/setup.php';
	header('Content-Type: application/json');

	$php_input = getallheaders();
	$access_token_received = str_replace('Bearer ', '', $php_input['Authentication']);

	if(!$auth->authenticated()) {
		echo json_encode(
			[
				'error' => true,
				'message' => 'Session has expired',
			]
		);
		return;
	}
	$auth->set_refresh_token();
	$refresh_token = $auth->get_refresh_token();
	header('X-Token: '.$refresh_token);
	echo json_encode(
		[
			'error' => false
		]
	);
}
catch (Exception $e) {
	echo json_encode(
		[
			'error' => true,
			'message' => $e->getMessage(),
		]
	);
}