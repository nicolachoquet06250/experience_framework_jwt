<?php

try {
	require_once __DIR__.'/setup.php';

	if (isset($_GET['disconnect'])) {
		$auth->disconnect();
		header('Location: /');
	}

	if (!$auth->authenticated()) {
		echo '
<head>
	<script src="https://code.jquery.com/jquery-3.3.1.js"
			integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
			crossorigin="anonymous"></script>
	<script>
		$(window).ready(() => {
		    $(\'#auth\').on(\'click\', () => {
		    	$.ajax({
		    		url: \'authenticate.php\',
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
		return;
	}
	echo '<input type="button" onclick="window.location.href=\'?disconnect=1\'" value="déconnection"><input type="button" onclick="window.location.href=\'page2.php\'" value="page 2"><br>';
	return;
}
catch (Exception $e) {
	echo '<div style="text-align: center;"><b style="color: red;">'.$e->getMessage().'</b></div>';
}
