<?php
require_once 'model.php';

$model = new Model('localhost', 'root', '', 'event_attendance_management_system');

date_default_timezone_set('Asia/Manila');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function generateUUIDv4()
{
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
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

        if ((password_verify($password, $user['password'])) && ($user["user_type"] == "admin")) {
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
            $image = upload_image("../static/uploads/admin/", $image_file);
        }

        $name = $model->escape($name);
        $username = $model->escape($username);

        $query = "UPDATE `users` SET `name`='" . $name . "', `username`='" . $username . "', `password`='" . $password . "', `image`='" . $image . "', `updated_at`='" . date("Y-m-d H:i:s") . "' WHERE `id`='" . $id . "'";
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

if (isset($_POST["add_attendee"])) {
    $image = $_FILES["image"];
    $student_number = $_POST["student_number"];
    $course = $_POST["course"];
    $year = $_POST["year"];
    $section = $_POST["section"];
    $first_name = $_POST["first_name"];
    $middle_name = $_POST["middle_name"];
    $last_name = $_POST["last_name"];
    $birthday = $_POST["birthday"];
    $mobile_number = $_POST["mobile_number"];
    $email = $_POST["email"];
    $address = $_POST["address"];
    $username = $_POST["username"];
    $password = $_POST["password"];

    $errors = 0;
    $student_number_ok = true;
    $username_ok = true;

    $student_number = $model->escape($student_number);
    $username = $model->escape($username);

    $query = "SELECT * FROM `attendees` WHERE `student_number` = '" . $student_number . "'";
    $result = $model->query($query);

    if ($result->num_rows > 0) {
        $student_number_ok = false;

        $errors++;
    }

    $query_2 = "SELECT * FROM `users` WHERE `username` = '" . $username . "'";
    $result_2 = $model->query($query_2);

    if ($result_2->num_rows > 0) {
        $username_ok = false;

        $errors++;
    }

    if (!$errors) {
        $current_date = date("Y-m-d H:i:s");
        $uuid = generateUUIDv4();
        $image = upload_image("../static/uploads/admin/", $image);
        $password = password_hash($password, PASSWORD_BCRYPT);
        $name = trim($first_name . ' ' . (!empty($middle_name) ? substr($middle_name, 0, 1) . '. ' : '') . $last_name);

        $query_3 = "INSERT INTO `users` (`uuid`, `name`, `username`, `password`, `image`, `user_type`, `created_at`, `updated_at`) VALUES ('" . $uuid . "', '" . $name . "', '" . $username . "', '" . $password . "', '" . $image . "', 'attendee', '" . $current_date . "', '" . $current_date . "')";
        $model->query($query_3);

        $account_id = $model->last_inserted_id();

        $query_4 = "INSERT INTO `attendees` (`uuid`, `account_id`, `first_name`, `middle_name`, `last_name`, `birthday`, `mobile_number`, `email`, `address`, `student_number`, `course`, `year`, `section`, `created_at`, `updated_at`) VALUES ('" . $uuid . "', '" . $account_id . "', '" . $first_name . "', '" . $middle_name . "', '" . $last_name . "', '" . $birthday . "', '" . $mobile_number . "', '" . $email . "', '" . $address . "', '" . $student_number . "', '" . $course . "', '" . $year . "', '" . $section . "', '" . $current_date . "', '" . $current_date . "')";
        $model->query($query_4);

        $_SESSION["notification"] = [
            "title" => "Success!",
            "text" => "An attendee has been added to the database.",
            "icon" => "success",
        ];
    }

    $response = [
        "student_number" => $student_number_ok,
        "username" => $username_ok,
    ];

    echo json_encode($response);
}

if (isset($_POST["delete_attendee"])) {
    $attendee_id = $_POST["attendee_id"];

    $query = "DELETE FROM `users` WHERE `id` = '" . $attendee_id . "'";
    $model->query($query);

    $query_2 = "DELETE FROM `attendees` WHERE `account_id` = '" . $attendee_id . "'";
    $model->query($query_2);

    $_SESSION["notification"] = [
        "title" => "Success!",
        "text" => "An attendee has been deleted from the database.",
        "icon" => "success",
    ];

    echo json_encode(true);
}

if (isset($_POST["logout"])) {
    unset($_SESSION["user_id"]);

    $_SESSION["notification"] = [
        "type" => "alert-success",
        "message" => "You had been signed out.",
    ];

    echo json_encode(true);
}
