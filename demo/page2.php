<?php

require_once __DIR__.'/../autoload.php';

use MiladRahimi\Jwt\Authentication\Authentication;
use MiladRahimi\Jwt\Authentication\Subdomains;

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

	if (isset($_GET['disconnect'])) {
		$auth->disconnect();
		header('Location: /');
	}

	if (!$auth->authenticated()) header('Location: /');
	else
		echo '
<head>
	<script src="https://code.jquery.com/jquery-3.3.1.js"
			integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
			crossorigin="anonymous"></script>
	<script>
		$(window).ready(() => {
		    $("#action_bearer").on("click", () => {
		    	$.ajax({
		    		url: "test_bearer.php",
		    		method: "post",
		    		headers: {
		    		    Authentication: "Bearer " + $("#access_token").val()
		    		}
		    	}).done(data => {
		    	    if(!data.forbidden) {
		    	        $("#auth_status").html("Vous êtes authentifié").css("color", "green");
		    	    }
		    	    else {
		    	        $("#auth_status").html("Vous n\'êtes pas authentifié").css("color", "red");
		    	    }
		    	});
		    });
		});
	</script>
</head>
<input type="hidden" id="access_token" value="'.$auth->get_token().'">
<input type="button" value="suis-je authentifié ?" id="action_bearer" />
<input type="button" onclick="window.location.href=\'?disconnect=1\'" value="déconnection"><br>
<span id="auth_status"></span>';
}
catch (Exception $e) {
	echo '<div style="text-align: center;"><b style="color: red;">'.$e->getMessage().'</b></div>';
}
