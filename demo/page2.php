<?php

require_once __DIR__.'/../autoload.php';

use MiladRahimi\Jwt\Authentication\Authentication;
use MiladRahimi\Jwt\Authentication\Subdomains;

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
	header('Location: /');
}
else {
	echo '<input type="button" onclick="window.location.href=\'?disconnect=1\'" value="déconnection"><br>';
	var_dump($auth->get_token());
}
