<?php

use MiladRahimi\Jwt\Enums\PublicClaimNames;

header('Content-Type: application/json');

try {

	require_once __DIR__.'/setup.php';
	if(!$auth->authenticated()) {
		if (isset($_POST['auth'])) {
			header('Content-Type: application/json');
			$auth->addClaim(PublicClaimNames::SUBJECT, 1)
				 ->addClaim(PublicClaimNames::ID, 2);
			$auth->authenticate();
			echo json_encode(
				[
					'referer' => 'http://'.$_SERVER['HTTP_HOST'],
				]
			);
			return;
		}
	}
	echo json_encode(
		 [
		 	'error' => true,
			'message' => 'You are already authenticated',
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