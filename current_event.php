<?php
require_once "model.php";

$model = new Model('localhost', 'root', '', 'event_attendance_management_system');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_id"])) {
    $_SESSION["notification"] = [
        "type" => "alert-danger",
        "message" => "You must login first",
    ];

    header("location: login");

    exit;
} else {
    $_SESSION["title"] = "Current Event";
    $_SESSION["current_page"] = "current_event";

    $attendees = null;

    $query = "SELECT * FROM `attendees` ORDER BY `id` DESC";
    $result = $model->query($query);

    if ($result->num_rows > 0) {
        $attendees = $result->fetch_all(MYSQLI_ASSOC);
    }

    $current_event = null;

    $query_2 = "SELECT * FROM `events` WHERE `status`='Current'";
    $result_2 = $model->query($query_2);

    if ($result_2->num_rows > 0) {
        $current_event = $result_2->fetch_assoc();
    }

    $attendances = null;

    $current_date = date("Y-m-d");

    $query_3 = "SELECT `attendance`.*, `attendees`.* FROM `attendance` JOIN `attendees` ON `attendance`.`student_id` = `attendees`.`account_id` WHERE DATE(`attendance`.`created_at`) = '" . $current_date . "' ORDER BY `attendance`.`id` DESC";
    $result_3 = $model->query($query_3);


    if ($result_3->num_rows > 0) {
        $attendances = $result_3->fetch_all(MYSQLI_ASSOC);
    }
}
?>

<?php include_once "header.php" ?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Current Event</h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <?php if ($current_event): ?>
                <div class="row">
                    <div class="col-lg-9">
                        <div class="card">
                            <div class="card-body">
                                <table class="table table-bordered table-striped datatable">
                                    <thead>
                                        <tr>
                                            <th>Student Name</th>
                                            <th>Course, Year, and Section</th>
                                            <th>Time In</th>
                                            <th>Time Out</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($attendances): ?>
                                            <?php foreach ($attendances as $attendance): ?>
                                                <tr>
                                                    <td><?= trim($attendance["first_name"] . ' ' . (!empty($attendance["middle_name"]) ? substr($attendance["middle_name"], 0, 1) . '. ' : '') . $attendance["last_name"]) ?></td>
                                                    <td><?= $attendance["course"] . " " . $attendance["year"][0] . "-" . $attendance["section"] ?></td>
                                                    <td><?= $attendance["time_in"] ?></td>
                                                    <td><?= $attendance["time_out"] != "" ? $attendance["time_out"] : "Not Yet Available" ?></td>
                                                    <td class="text-center text-<?= $attendance["status"] == "In" ? "success" : "danger" ?>"><?= $attendance["status"] ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div id="qrcode" class="mb-3 w-100"></div>

                        <h4>Event Information</h4>

                        <div class="row mb-2">
                            <div class="col-lg-4">
                                <span>Event Name:</span>
                            </div>
                            <div class="col-lg-8">
                                <strong><?= $current_event["name"] ?></strong>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <span>Date and Time:</span>
                            </div>
                            <div class="col-lg-8">
                                <strong><?= date('F j, Y g:i A', strtotime($current_event["date"])); ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <h1 class="text-center text-muted mt-5 pt-5">There is no current event at the moment.</h1>
            <?php endif ?>
        </div>
    </section>
</div>

<?php include_once "footer.php" ?>