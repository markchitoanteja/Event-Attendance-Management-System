<?php
require_once 'model.php';

$model = new Model('localhost', 'root', '', 'event_attendance_management_system');

date_default_timezone_set('Asia/Manila');

if (isset($_POST["check_connection"])) {
    echo json_encode(true);
}

if (isset($_POST["verify_login"])) {
    $username = $model->escape($_POST["username"]);
    $password = $_POST["password"];

    $query = "SELECT * FROM `users` WHERE `username` = '" . $username . "'";
    $result = $model->query($query);

    $response = false;

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password']) && $user["user_type"] == "attendee") {
            $query_2 = "SELECT * FROM `attendees` WHERE `account_id` = '" . $user['id'] . "'";
            $result_2 = $model->query($query_2);

            $attendee = $result_2->fetch_assoc();

            $response = $user['id'] . "|" . $user['name'] . "|" . $user['username'] . "|" . $user['password'] . "|" . $user['image'] . "|" . $attendee['first_name'] . "|" . $attendee['middle_name'] . "|" . $attendee['last_name'] . "|" . $attendee['birthday'] . "|" . $attendee['mobile_number'] . "|" . $attendee['email'] . "|" . $attendee['address'] . "|" . $attendee['student_number'] . "|" . $attendee['course'] . "|" . $attendee['year'] . "|" . $attendee['section'];
        }
    }

    echo json_encode($response);
}

if (isset($_POST["take_attendance"])) {
    $attendee = $_POST["attendee"];
    $uuid = $_POST["uuid"];
    $today = date('Y-m-d');
    $current_time = date('h:i A');
    $current_datetime = date('Y-m-d H:i:s');

    $query = "SELECT * FROM `events` WHERE `uuid` = '" . $uuid . "'";
    $result = $model->query($query);

    $response = "";

    if ($result->num_rows > 0) {
        $event = $result->fetch_assoc();

        $event_id = $event["id"];

        $query_2 = "SELECT * FROM `events` WHERE `uuid` = '" . $uuid . "' AND FIND_IN_SET('" . $attendee . "', `attendees`) > 0";
        $result_2 = $model->query($query_2);

        if ($result_2->num_rows > 0) {
            $query_3 = "SELECT * FROM `attendance` WHERE DATE(`created_at`) = '" . $today . "' AND `student_id` = '" . $attendee . "' AND `event_id` = '" . $event_id . "'";
            $result_3 = $model->query($query_3);

            if ($result_3->num_rows > 0) {
                $attendance = $result_3->fetch_assoc();

                if ($attendance["status"] == "In") {
                    $query_4 = "UPDATE `attendance` SET `time_out`='" . $current_time . "', `status`='temp', `updated_at`='" . $current_datetime . "' WHERE `id`='" . $attendance["id"] . "'";
                    $model->query($query_4);
                } else {
                    $response = "have_taken_attendance";
                }
            } else {
                $query_4 = "INSERT INTO `attendance` (`student_id`, `event_id`, `time_in`, `status`, `created_at`, `updated_at`) VALUES ('" . $attendee . "', '" . $event_id . "', '" . $current_time . "', 'temp', '" . $current_datetime . "', '" . $current_datetime . "')";
                $model->query($query_4);
            }
        } else {
            $response = "not_invited";
        }
    } else {
        $response = "no_event";
    }

    echo json_encode($response);
}
