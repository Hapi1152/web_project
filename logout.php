<?php
    session_start();
    if (array_key_exists('IS_AUTH', $_SESSION)) {
        session_destroy();
        header('Location: authorization.php');
        die();
    }
?>
