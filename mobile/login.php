<?php
require_once "../check_device.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($device == "desktop") {
    header('HTTP/1.1 403 Forbidden');

    exit;
}
?>

Fuck You Login!