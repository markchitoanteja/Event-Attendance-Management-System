<?php
require_once 'model.php';
require_once 'third-party/phpmailer/PHPMailer.php';
require_once 'third-party/phpmailer/SMTP.php';
require_once 'third-party/phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$model = new Model('localhost', 'root', '', 'event_attendance_management_system');

date_default_timezone_set('Asia/Manila');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function send_email($name, $email, $subject, $message)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'essucanavid1960@gmail.com';
        $mail->Password = 'imdztqqgoaprrwmh';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('essucanavid1960@gmail.com', 'ESSU Can-Avid Campus');
        $mail->addAddress($email, $name);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();

        return true;
    } catch (Exception $e) {
        return false;
    }
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

function event_logs($icon, $event)
{
    $model = new Model('localhost', 'root', '', 'event_attendance_management_system');

    $uuid = generateUUIDv4();
    $current_date = date("Y-m-d H:i:s");

    $query = "INSERT INTO `logs` (`uuid`, `icon`, `event`, `created_at`, `updated_at`) VALUES ('" . $uuid . "', '" . $icon . "', '" . $event . "', '" . $current_date . "', '" . $current_date . "')";
    $model->query($query);
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

            event_logs("fas fa-check-circle text-success", "Admin successfully logged in at " . date("F j, Y h:i a") . ".");

            $response = true;
        }
    }

    echo json_encode($response);
}

if (isset($_POST["change_mode"])) {
    $mode = $_POST["mode"];

    $_SESSION["mode"] = $mode;

    $icon = "fas fa-moon";

    if ($mode == "light") {
        $icon = "fas fa-sun";
    }

    event_logs($icon . " text-primary", "Admin switched to " . ucfirst($mode) . " Mode on " . date("F j, Y h:i a") . ".");

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
            $image = upload_image("static/uploads/", $image_file);
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

        event_logs("fas fa-user-cog text-info", "Admin data updated successfully for admin " . $name . " at " . date("F j, Y h:i a") . ".");

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
        $image = upload_image("static/uploads/", $image);
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

        event_logs("fas fa-user-plus text-success", "Attendee " . $name . " added successfully at " . date("F j, Y h:i a") . ".");
    }

    $response = [
        "student_number" => $student_number_ok,
        "username" => $username_ok,
    ];

    echo json_encode($response);
}

if (isset($_POST["update_attendee"])) {
    $image_file = isset($_FILES["image_file"]) ? $_FILES["image_file"] : null;
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

    $id = $_POST["id"];
    $old_student_number = $_POST["old_student_number"];
    $old_username = $_POST["old_username"];
    $old_password = $_POST["old_password"];
    $old_image = $_POST["old_image"];

    $is_new_student_number = $_POST["is_new_student_number"];
    $is_new_username = $_POST["is_new_username"];
    $is_new_password = $_POST["is_new_password"];
    $is_new_image = $_POST["is_new_image"];

    $errors = 0;
    $student_number_ok = true;
    $username_ok = true;

    $student_number = $model->escape($student_number);
    $username = $model->escape($username);

    if ($is_new_student_number == "true") {
        $query = "SELECT * FROM `attendees` WHERE `student_number` = '" . $student_number . "'";
        $result = $model->query($query);

        if ($result->num_rows > 0) {
            $student_number_ok = false;

            $errors++;
        }
    }

    if ($is_new_username == "true") {
        $query_2 = "SELECT * FROM `users` WHERE `username` = '" . $username . "'";
        $result_2 = $model->query($query_2);

        if ($result_2->num_rows > 0) {
            $username_ok = false;

            $errors++;
        }
    }

    if (!$errors) {
        if ($is_new_image == "true") {
            $image = upload_image("static/uploads/", $image_file);
        } else {
            $image = $old_image;
        }

        if ($is_new_password == "true") {
            $password = password_hash($password, PASSWORD_BCRYPT);
        } else {
            $password = $old_password;
        }

        $current_date = date("Y-m-d H:i:s");
        $name = trim($first_name . ' ' . (!empty($middle_name) ? substr($middle_name, 0, 1) . '. ' : '') . $last_name);

        $query_3 = "UPDATE `users` SET `name`='" . $name . "', `username`='" . $username . "', `password`='" . $password . "', `image`='" . $image . "', `updated_at`='" . $current_date . "' WHERE `id`='" . $id . "'";
        $model->query($query_3);

        $query_4 = "UPDATE `attendees` SET `student_number`='" . $student_number . "', `course`='" . $course . "', `year`='" . $year . "', `section`='" . $section . "', `first_name`='" . $first_name . "', `middle_name`='" . $middle_name . "', `last_name`='" . $last_name . "', `birthday`='" . $birthday . "', `mobile_number`='" . $mobile_number . "', `email`='" . $email . "', `address`='" . $address . "' WHERE `account_id`='" . $id . "'";
        $model->query($query_4);

        $_SESSION["notification"] = [
            "title" => "Success!",
            "text" => "An attendee has been updated successfully.",
            "icon" => "success",
        ];

        event_logs("fas fa-user-edit text-info", "Attendee " . $name . " updated successfully with new details at " . date("F j, Y h:i a") . ".");
    }

    $response = [
        "student_number" => $student_number_ok,
        "username" => $username_ok,
    ];

    echo json_encode($response);
}

if (isset($_POST["delete_attendee"])) {
    $attendee_id = $_POST["attendee_id"];

    $query = "SELECT `name` FROM `users` WHERE `id`='" . $attendance_id . "'";
    $result = $model->query($query);

    $name = $result->fetch_assoc()["name"];

    $query_2 = "DELETE FROM `users` WHERE `id` = '" . $attendee_id . "'";
    $model->query($query_2);

    $query_3 = "DELETE FROM `attendees` WHERE `account_id` = '" . $attendee_id . "'";
    $model->query($query_3);

    $_SESSION["notification"] = [
        "title" => "Success!",
        "text" => "An attendee has been deleted from the database.",
        "icon" => "success",
    ];

    event_logs("fas fa-user-minus text-danger", "Attendee " . $name . " was deleted successfully from the database at " . date("F j, Y h:i a") . ".");

    echo json_encode(true);
}

if (isset($_POST["get_attendee_data"])) {
    $attendee_id = $_POST["attendee_id"];

    $query = "SELECT * FROM `users` JOIN `attendees` ON `users`.`id` = `attendees`.`account_id` WHERE `users`.`id` = '" . $attendee_id . "'";
    $result = $model->query($query);

    $attendee = $result->fetch_assoc();

    echo json_encode($attendee);
}

if (isset($_POST["send_email"])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $subject = $_POST["subject"];
    $message = $_POST["message"];

    $message = str_replace("\n", "<br>", $message);

    $is_sent = send_email($name, $email, $subject, $message);

    if ($is_sent) {
        $_SESSION["notification"] = [
            "title" => "Success!",
            "text" => "Message has been sent successfully.",
            "icon" => "success",
        ];
    } else {
        $_SESSION["notification"] = [
            "title" => "Oops...",
            "text" => "There was an error while sending your message",
            "icon" => "error",
        ];
    }

    event_logs("fas fa-envelope text-primary", "Email sent successfully to " . $name . " regarding " . $subject . " at " . date("F j, Y h:i a") . ".");

    echo json_encode(true);
}

if (isset($_POST["add_event"])) {
    $uuid = generateUUIDv4();
    $name = $_POST["name"];
    $date = $_POST["date"];
    $attendees = $_POST["attendees"];
    $status = $_POST["status"];

    $current_date = date("Y-m-d H:i:s");

    $query = "INSERT INTO `events` (`uuid`, `name`, `date`, `attendees`, `status`, `created_at`, `updated_at`) VALUES ('" . $uuid . "', '" . $name . "', '" . $date . "', '" . $attendees . "', '" . $status . "', '" . $current_date . "', '" . $current_date . "')";
    $model->query($query);

    $_SESSION["notification"] = [
        "title" => "Success!",
        "text" => "An event has beed added successfully to the database.",
        "icon" => "success",
    ];

    event_logs("fas fa-calendar-plus text-success", "New event titled \'" . $name . "\' added successfully on " . date("F j, Y h:i a") . ".");

    echo json_encode(true);
}

if (isset($_POST["get_event_data"])) {
    $event_id = $_POST["event_id"];

    $query = "SELECT * FROM `events` WHERE `id`='" . $event_id . "'";
    $result = $model->query($query);

    $attendee = $result->fetch_assoc();

    echo json_encode($attendee);
}

if (isset($_POST["update_event"])) {
    $id = $_POST["id"];
    $name = $_POST["name"];
    $date = $_POST["date"];
    $attendees = $_POST["attendees"];
    $status = $_POST["status"];

    $current_date = date("Y-m-d H:i:s");

    $query = "UPDATE `events` SET `name`='" . $name . "', `date`='" . $date . "', `attendees`='" . $attendees . "', `status`='" . $status . "', `updated_at`='" . $current_date . "' WHERE `id`='" . $id . "'";
    $model->query($query);

    $_SESSION["notification"] = [
        "title" => "Success!",
        "text" => "An event has beed updated successfully.",
        "icon" => "success",
    ];

    event_logs("fas fa-pencil-alt text-info", "Event titled \'" . $name . "\' updated successfully with new details at " . date("F j, Y h:i a") . ".");

    echo json_encode(true);
}

if (isset($_POST["delete_event"])) {
    $event_id = $_POST["event_id"];

    $query = "SELECT `name` FROM `events` WHERE `id`='" . $event_id . "'";
    $result = $model->query($query);

    $name = $result->fetch_assoc()["name"];

    $query_2 = "DELETE FROM `events` WHERE `id` = '" . $event_id . "'";
    $model->query($query_2);

    $_SESSION["notification"] = [
        "title" => "Success!",
        "text" => "An event has been deleted from the database.",
        "icon" => "success",
    ];

    event_logs("fas fa-calendar-times text-danger", "Event titled \'" . $name . "\' was deleted successfully at " . date("F j, Y h:i a") . ".");

    echo json_encode(true);
}

if (isset($_POST["set_to_current"])) {
    $event_id = $_POST["event_id"];

    $query = "SELECT * FROM `events` WHERE `status` = 'Current'";
    $result = $model->query($query);

    if ($result->num_rows > 0) {
        $_SESSION["notification"] = [
            "title" => "Oops..",
            "text" => "There is still an event going on.",
            "icon" => "error",
        ];
    } else {
        $current_date = date('Y-m-d');

        $query_2 = "UPDATE `events` SET  `status`='Current', `date` = CONCAT('$current_date', ' ', DATE_FORMAT(`date`, '%H:%i:%s')), `updated_at`='" . date('Y-m-d H:i:s') . "' WHERE `id`='" . $event_id . "'";
        $model->query($query_2);

        $query_3 = "SELECT `name` FROM `events` WHERE `id`='" . $event_id . "'";
        $result_2 = $model->query($query_3);

        $name = $result_2->fetch_assoc()["name"];

        event_logs("fas fa-calendar-check text-primary", "Current event set to \'" . $name . "\' successfully at " . date("F j, Y h:i a") . ".");

        $_SESSION["notification"] = [
            "title" => "Success!",
            "text" => "An event has been set to Current.",
            "icon" => "success",
        ];
    }

    echo json_encode(true);
}

if (isset($_POST["get_settings_data"])) {
    $query = "SELECT `ip_address` FROM `settings` WHERE `id`='1'";
    $result = $model->query($query);

    $ip_address = $result->fetch_assoc();

    echo json_encode($ip_address);
}

if (isset($_POST["update_ip_address"])) {
    $ip_address = $_POST["ip_address"];

    $query = "UPDATE `settings` SET `ip_address`='" . $ip_address . "', `updated_at`='" . date("Y-m-d H:i:s") . "'";
    $model->query($query);

    $_SESSION["notification"] = [
        "title" => "Success!",
        "text" => "IP Address has been updated.",
        "icon" => "success",
    ];

    event_logs("fas fa-network-wired text-info", "IP address updated successfully to " . $ip_address . " at " . date("F j, Y h:i a") . ".");

    echo json_encode(true);
}

if (isset($_POST["check_attendance"])) {
    $query = "SELECT `id`, `student_id`, `time_in`, `time_out` FROM `attendance` WHERE `status`='temp'";
    $result = $model->query($query);

    $response = false;

    if ($result->num_rows > 0) {
        $attendance = $result->fetch_assoc();

        $attendance_id = $attendance["id"];
        $student_id = $attendance["student_id"];
        $time_in = $attendance["time_in"];
        $time_out = $attendance["time_out"];

        $query_2 = "SELECT users.*, attendees.* FROM `users` INNER JOIN `attendees` ON `users`.`id` = `attendees`.`account_id` WHERE users.id = '" . $student_id . "'";
        $result_2 = $model->query($query_2);

        $student = $result_2->fetch_assoc();

        $student_name = $student["name"];
        $student_image = $student["image"];
        $student_student_number = $student["student_number"];
        $student_course_year_section = $student["course"] . " " . $student["year"][0] . "-" . $student["section"];

        if ($time_in && !$time_out) {
            $query_3 = "UPDATE `attendance` SET `status`='In', `updated_at`='" . date("Y-m-d H:i:s") . "' WHERE `id`='" . $attendance_id . "'";
            $model->query($query_3);

            $status = "In";

            $event_message = "Attendee clocked in successfully at " . date("F j, Y h:i a") . ".";
            $event_icon = "fas fa-sign-in-alt text-success";
        } else {
            $query_3 = "UPDATE `attendance` SET `status`='Out', `updated_at`='" . date("Y-m-d H:i:s") . "' WHERE `id`='" . $attendance_id . "'";
            $model->query($query_3);

            $status = "Out";

            $event_message = "Attendee clocked out successfully at " . date("F j, Y h:i a") . ".";
            $event_icon = "fas fa-sign-out-alt text-success";
        }

        $_SESSION["attendee_data"] = [
            "student_name" => $student_name,
            "student_image" => $student_image,
            "student_student_number" => $student_student_number,
            "student_course_year_section" => $student_course_year_section,
            "time_in" => $time_in,
            "time_out" => $time_out,
            "status" => $status,
        ];

        $response = true;

        event_logs($event_icon, $event_message);
    }

    echo json_encode($response);
}

if (isset($_POST["logout"])) {
    unset($_SESSION["user_id"]);

    $_SESSION["notification"] = [
        "type" => "alert-success",
        "message" => "You had been signed out.",
    ];

    event_logs("fas fa-power-off text-success", "Admin logged out successfully at " . date("F j, Y h:i a") . ".");

    echo json_encode(true);
}
