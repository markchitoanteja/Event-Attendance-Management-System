<?php
require_once "check_device.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION["user_id"])) {
    header("location: " . $device . "/dashboard");
} else {
    header("location: " . $device . "/login");
}
