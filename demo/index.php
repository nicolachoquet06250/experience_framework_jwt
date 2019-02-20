
<head>
	<script src="https://code.jquery.com/jquery-3.3.1.js"
			integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
			crossorigin="anonymous"></script>
	<script>
        $(window).ready(() => {
            $('#auth').on('click', on_auth_click);

			$("#action_bearer").on("click", on_action_bearer_click);
        });
	</script>
</head>
<body>

<?php

	try {
		require_once __DIR__.'/setup.php';

		echo '
<input type="hidden" id="access_token" value="'.$auth->get_token().'tralala" />
<div id="content">';

		if (isset($_GET['disconnect'])) {
			$auth->disconnect();
			header('Location: /');
		}

		if (!$auth->authenticated()) {
			echo '
		<input type="button" name="auth" id="auth" value="demander l\'authentification">
	</div>';
		}
		else {
			echo '
		<input type="button" onclick="window.location.href=\'?disconnect=1\'" value="déconnection">
		<input type="button" onclick="window.location.href=\'page2.php\'" value="page 2"><br>
		<input type="button" value="suis-je authentifié ?" id="action_bearer" /><br>
		<span id="auth_status"></span>';
		}
		echo "</div>
	<script>
		function on_action_bearer_click() {
			console.log('hello');
			$.ajax({
				url: \"test_bearer.php\",
				method: \"post\",
				headers: {
					Authentication: \"Bearer \" + $(\"#access_token\").val()
				}
			}).done(action_bearer_callback);
		}
		function on_auth_click() {
			$.ajax({
				url: \"authenticate.php\",
				method: \"post\",
				data: {
					auth: true
				},
				async: false,
				complete: response => {
					$(\"#access_token\").val(response.getResponseHeader(\"X-Token\"));
				}
			}).done(authenticate_callback);
		}

		let authenticate_callback = data => {
			if(data.referer !== undefined) {
				$(\"#content\").html(
					\"<input type=\\\"button\\\" onclick=\\\"window.location.href = '?disconnect=1'\\\" value=\\\"déconnection\\\" />\"
					+ \"<input type=\\\"button\\\" onclick=\\\"window.location.href = 'page2.php'\\\" value=\\\"page 2\\\" /><br>\"
					+ \"<input type=\\\"button\\\" value=\\\"suis - je authentifié ? \\\" id=\\\"action_bearer\\\" onclick='on_action_bearer_click()' />\"
					+ \"<span id=\\\"auth_status\\\"></span>\");
			}
		};
		let action_bearer_callback = data => {
			if(!data.forbidden) {
				$(\"#auth_status\").html(\"Vous êtes authentifié\").css(\"color\", \"green\");
			}
			else {
				$(\"#auth_status\").html(\"Vous n\'êtes pas authentifié\").css(\"color\", \"red\");
				$.ajax({
					url: \"refresh_token.php\",
					method: \"post\",
					headers: {
						Authentication: \"Bearer \" + $(\"#access_token\").val()
					},
					async: true,
					complete: response => {
						console.log(response.getResponseHeader(\"X-Token\"));
						$(\"#access_token\").val(response.getResponseHeader(\"X-Token\"));
						$(\"#auth_status\").html(\"Vous êtes re authentifié\").css(\"color\", \"green\");
					}
				})
			}
		};
	</script>
</body>";
	}
	catch (Exception $e) {
		echo '<div style="text-align: center;"><b style="color: red;">'.$e->getMessage().'</b></div>';
	}