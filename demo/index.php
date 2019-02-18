<?php

require_once __DIR__.'/../autoload.php';

use MiladRahimi\Jwt\Authentication\Authentication;
use MiladRahimi\Jwt\Authentication\Subdomains;
use MiladRahimi\Jwt\Enums\PublicClaimNames;

$auth = new Authentication();
$sub_domains = Subdomains::create();
$domain = explode('.', $_SERVER['HTTP_HOST']);
if(count($domain) >= 3) {
	array_shift($domain);
}
$domain = implode('.', $domain);
$domain = explode(':', $domain)[0];

$sub_domains->set_domain($domain);

if(isset($_GET['disconnect'])) {
	$auth->disconnect();
	header('Location: /');
}

if(!$auth->authenticated()) {
	if(isset($_POST['auth'])) {
		header('Content-Type: application/json');
		$auth->addClaim(PublicClaimNames::SUBJECT, 1)
			 ->addClaim(PublicClaimNames::ID, 2);
		$auth->authenticate();
		echo json_encode(
			 [
			 	'referer' => 'http://'.$_SERVER['HTTP_HOST'],
			 ]
		);
	}
	else {
		echo '
<head>
	<script src="https://code.jquery.com/jquery-3.3.1.js"
			integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
			crossorigin="anonymous"></script>
	<script>
		$(window).ready(() => {
		    $(\'#auth\').on(\'click\', () => {
		    	$.ajax({
		    		url: \'\',
		    		method: \'post\',
		    		data: {
		    		    auth: true
		    		}
		    	}).done(data => {
		    	    window.location.href = data.referer;
		    	});
		    });
		});
	</script>
</head>
<input type="button" name="auth" id="auth" value="demander l\'authentification">';
	}
}
else {
	echo '<input type="button" onclick="window.location.href=\'?disconnect=1\'" value="déconnection"><input type="button" onclick="window.location.href=\'page2.php\'" value="page 2"><br>';
	var_dump($auth->get_token());
}
