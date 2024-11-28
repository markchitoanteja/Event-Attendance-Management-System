<?php
require_once 'model.php';

$model = new Model('localhost', 'root', '', 'event_attendance_management_system');

date_default_timezone_set('Asia/Manila');

if (isset($_POST["check_connection"])) {
    $server_ip = $_POST["ip_address"];
    $url = "http://" . $server_ip;

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

    $response = curl_exec($ch);

    if ($response === false) {
        $response = false;
    } else {
        $response = true;
    }

    curl_close($ch);

    echo json_encode($response);
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

            $profile_data = [
                $user['id'],
                $user['name'],
                $user['username'],
                $user['password'],
                $user['image'],
                $attendee['first_name'],
                $attendee['middle_name'],
                $attendee['last_name'],
                $attendee['birthday'],
                $attendee['mobile_number'],
                $attendee['email'],
                $attendee['address'],
                $attendee['student_number'],
                $attendee['course'],
                $attendee['year'],
                $attendee['section']
            ];

            $query_3 = "SELECT * FROM `events` WHERE `status` = 'Current'";
            $result_3 = $model->query($query_3);

            $current_event_data = array_fill(0, 5, "no_data");

            if ($result_3->num_rows > 0) {
                $current_event_data = [];

                $event = $result_3->fetch_assoc();

                $query_4 = "SELECT * FROM `events` WHERE `status` = 'Current' AND FIND_IN_SET('" . $user['id'] . "', `attendees`)";
                $result_4 = $model->query($query_4);

                $invitation_status = "NOT INVITED";

                if ($result_4->num_rows > 0) {
                    $invitation_status = "INVITED";
                }

                $event_datetime = new DateTime($event["date"]);

                $current_event_data = [
                    $event["id"],
                    $event["name"],
                    $event_datetime->format('F j, Y'),
                    $event_datetime->format('h:i A'),
                    $invitation_status
                ];
            }

            $query_5 = "SELECT `name` FROM `events` WHERE `status` = 'Upcoming' AND FIND_IN_SET('" . $user['id'] . "', `attendees`)";
            $result_5 = $model->query($query_5);

            $invited_events_data = ["no_data"];

            if ($result_5->num_rows > 0) {
                $invited_events_data = [];

                $invited_events = $result_5->fetch_all(MYSQLI_ASSOC);

                foreach ($invited_events as $invited_event) {
                    array_push($invited_events_data, $invited_event["name"]);
                }
            }

            $merged_data = array_merge($profile_data, $current_event_data, $invited_events_data);

            $response = implode('|', $merged_data);
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

                    $response =  $attendance["time_in"] . "|" . $current_time . "|" . "OUT";
                } else {
                    $response = "have_taken_attendance";
                }
            } else {
                $query_4 = "INSERT INTO `attendance` (`student_id`, `event_id`, `time_in`, `status`, `created_at`, `updated_at`) VALUES ('" . $attendee . "', '" . $event_id . "', '" . $current_time . "', 'temp', '" . $current_datetime . "', '" . $current_datetime . "')";
                $model->query($query_4);

                $response =  $current_time . "|" . "Not Yet Available" . "|" . "IN";
            }
        } else {
            $response = "not_invited";
        }
    } else {
        $response = "no_event";
    }

    echo json_encode($response);
}

if (isset($_POST["get_attendance_data"])) {
    $attendee_id = $_POST["attendee_id"];
    $today = date('Y-m-d');

    $response = false;

    $query = "SELECT `id` FROM `events` WHERE `status` = 'Current'";
    $result = $model->query($query);

    if ($result->num_rows > 0) {
        $event_id = $result->fetch_assoc()["id"];

        $query_2 = "SELECT * FROM `attendance` WHERE DATE(`created_at`) = '" . $today . "' AND `student_id` = '" . $attendee_id . "' AND `event_id` = '" . $event_id . "'";
        $result_2 = $model->query($query_2);

        if ($result_2->num_rows > 0) {
            $attendance = $result_2->fetch_assoc();

            $response =  $attendance["time_in"] . "|" . $attendance["time_in"] . "|" . $attendance["status"];
        }
    }

    echo json_encode($response);
}
