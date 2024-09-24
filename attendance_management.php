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
    $_SESSION["title"] = "Attendance Management";
    $_SESSION["current_page"] = "attendance_management";

    $attendances = null;

    $query = "SELECT `attendance`.*, `attendees`.*, `events`.`name` AS `event_name`, `events`.`created_at` FROM `attendance` JOIN `attendees` ON `attendance`.`student_id` = `attendees`.`account_id` JOIN `events` ON `attendance`.`event_id` = `events`.`id` GROUP BY `events`.`name` ORDER BY `events`.`created_at` DESC";

    $result = $model->query($query);

    if ($result->num_rows > 0) {
        $attendances = $result->fetch_all(MYSQLI_ASSOC);
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
                    <h1 class="m-0">Attendance Management</h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-striped datatable">
                        <thead>
                            <tr>
                                <th>Event Name</th>
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
                                        <td><?= $attendance["event_name"] ?></td>
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
    </section>
</div>

<?php include_once "footer.php" ?>