<?php
	if (session_status() == PHP_SESSION_ACTIVE) {
		header('Location: /logout.php');
	} else {
		header('Location: /login.php');
	}
?>