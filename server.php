<?php
require_once 'model.php';

$model = new Model('localhost', 'root', '', 'event_attendance_management_system');

date_default_timezone_set('Asia/Manila');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST["login"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $remember_me = $_POST["remember_me"];

    $username = $model->escape($username);

    $query = "SELECT * FROM users WHERE username = '" . $username . "'";
    $result = $model->query($query);

    $response = false;

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];

            if ($remember_me == "true"){
                $_SESSION['username'] = $username;
                $_SESSION['password'] = $password;
            }

            $response = true;
        }
    }

    echo json_encode($response);
}
