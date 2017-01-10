<?php
    if(session_status() == PHP_SESSION_ACTIVE ) {
        session_destroy();
    }
    require_once("index.php");
?>