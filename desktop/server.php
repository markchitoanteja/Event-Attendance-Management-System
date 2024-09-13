<?php
require_once 'model.php';

$model = new Model('localhost', 'root', '', 'event_attendance_management_system');

date_default_timezone_set('Asia/Manila');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function upload_image($target_directory, $image_file)
{
    $response = false;

    if (isset($image_file) && $image_file['error'] == UPLOAD_ERR_OK) {
        $uploadedFile = $image_file;

        $target_dir = $target_directory;

        if ($uploadedFile['size'] > 0) {
            $file_temp = $uploadedFile['tmp_name'];
            $file_ext = pathinfo($uploadedFile['name'], PATHINFO_EXTENSION);

            $unique_name = uniqid('img_', true) . '.' . $file_ext;

            if (move_uploaded_file($file_temp, $target_dir . '/' . $unique_name)) {
                $response = $unique_name;
            }
        }
    }

    return $response;
}

if (isset($_POST["login"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $remember_me = $_POST["remember_me"];

    $username = $model->escape($username);

    $query = "SELECT * FROM `users` WHERE `username` = '" . $username . "'";
    $result = $model->query($query);

    $response = false;

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];

            if ($remember_me == "true") {
                $_SESSION['username'] = $username;
                $_SESSION['password'] = $password;
            }

            $response = true;
        }
    }

    echo json_encode($response);
}

if (isset($_POST["change_mode"])) {
    $mode = $_POST["mode"];

    $_SESSION["mode"] = $mode;

    echo json_encode(true);
}

if (isset($_POST["get_admin_data"])) {
    $id = $_POST["id"];

    $query = "SELECT * FROM `users` WHERE `id` = '" . $id . "'";
    $result = $model->query($query);

    $admin = $result->fetch_assoc();

    echo json_encode($admin);
}

if (isset($_POST["update_admin"])) {
    $id = $_POST["id"];
    $name = $_POST["name"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $image = isset($_POST["image"]) ? $_POST["image"] : null;
    $image_file = isset($_FILES["image_file"]) ? $_FILES["image_file"] : null;
    $is_new_username = $_POST["is_new_username"];
    $is_new_password = $_POST["is_new_password"];
    $is_new_image = $_POST["is_new_image"];

    $errors = 0;
    $response = false;

    if ($is_new_username == "true") {
        $query = "SELECT * FROM `users` WHERE `username` = '" . $username . "'";
        $result = $model->query($query);

        if ($result->num_rows > 0) {
            $errors++;
        }
    }

    if (!$errors) {
        if ($is_new_password == "true") {
            $password = password_hash($password, PASSWORD_BCRYPT);
        }

        if ($is_new_image == "true") {
            $image = upload_image("static/uploads/admin/", $image_file);
        }

        $name = $model->escape($name);
        $username = $model->escape($username);

        $query = "UPDATE `users` SET `name`='" . $name . "', `username`='" . $username . "', `password`='" . $password . "', `image`='" . $image . "' WHERE `id`='" . $id . "'";
        $model->query($query);

        $_SESSION["notification"] = [
            "title" => "Success!",
            "text" => "Account is updated successfully.",
            "icon" => "success",
        ];

        $response = true;
    }

    echo json_encode($response);
}

if (isset($_POST["logout"])) {
    unset($_SESSION["user_id"]);

    $_SESSION["notification"] = [
        "type" => "alert-success",
        "message" => "You had been signed out.",
    ];

    echo json_encode(true);
}
