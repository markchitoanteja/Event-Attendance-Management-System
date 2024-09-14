<?php
require_once "../check_device.php";

if ($device == "mobile") {
    header('HTTP/1.1 403 Forbidden');

    exit;
} else {
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
        $_SESSION["title"] = "Attendee Directory";
        $_SESSION["current_page"] = "attendee_directory";

        $attendees = null;

        $query = "SELECT * FROM `attendees` ORDER BY `id` DESC";
        $result = $model->query($query);

        if ($result->num_rows > 0) {
            $attendees = $result->fetch_all(MYSQLI_ASSOC);
        }
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
                    <h1 class="m-0">Attendee Directory</h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#new_attendee_modal">
                            <i class="fas fa-plus mr-1"></i>
                            New Attendee
                        </button>
                    </div>
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
                                <th>Student Number</th>
                                <th>Student Name</th>
                                <th>Course, Year, and Section</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($attendees): ?>
                                <?php foreach ($attendees as $attendee): ?>
                                    <tr>
                                        <td><?= $attendee["student_number"] ?></td>
                                        <td><?= trim($attendee["first_name"] . ' ' . (!empty($attendee["middle_name"]) ? substr($attendee["middle_name"], 0, 1) . '. ' : '') . $attendee["last_name"]) ?></td>
                                        <td><?= $attendee["course"] . " " . $attendee["year"][0] . "-" . $attendee["section"] ?></td>
                                        <td class="text-center">
                                            <i class="fas fa-pencil-alt text-primary mr-1 edit_attendee" role="button" title="Edit Attendee Information" attendee_id="<?= $attendee["account_id"] ?>"></i>
                                            <i class="fas fa-trash-alt text-danger delete_attendee" role="button" title="Delete Attendee" attendee_id="<?= $attendee["account_id"] ?>"></i>
                                        </td>
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