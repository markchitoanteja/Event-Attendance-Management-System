<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

unset($_SESSION["user_id"]);

if (!isset($_SESSION["user_id"])) {
    $_SESSION["notification"] = [
        "type" => "alert-danger",
        "message" => "You must login first!",
    ];

    header("location: login");
}
