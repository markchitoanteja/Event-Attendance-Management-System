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
        $_SESSION["title"] = "Events Management";
        $_SESSION["current_page"] = "events_management";

        $events = null;

        $query = "SELECT * FROM `events` ORDER BY `status` DESC, `date` ASC";
        $result = $model->query($query);

        if ($result->num_rows > 0) {
            $events = $result->fetch_all(MYSQLI_ASSOC);
        }

        $attendees = null;

        $query_2 = "SELECT * FROM `attendees` ORDER BY `first_name` ASC";
        $result_2 = $model->query($query_2);

        if ($result_2->num_rows > 0) {
            $attendees = $result_2->fetch_all(MYSQLI_ASSOC);
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
                    <h1 class="m-0">Events Management</h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#new_event_modal">
                            <i class="fas fa-plus mr-1"></i>
                            New Event
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
                                <th>Event Name</th>
                                <th>Date and Time</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($events): ?>
                                <?php foreach ($events as $event): ?>
                                    <tr>
                                        <td><?= $event["name"] ?></td>
                                        <td><?= (new DateTime($event["date"]))->format('F j, Y g:i A') ?></td>
                                        <td class="<?= $event["status"] == "Upcoming" ? "text-primary" : "text-danger" ?>"><?= $event["status"] ?></td>
                                        <td class="text-center">
                                            <i class="fas fa-pencil-alt text-success mr-2 edit_event" role="button" title="Edit Event Information" event_id="<?= $event["id"] ?>"></i>
                                            <i class="fas fa-trash-alt text-danger delete_event" role="button" title="Delete Event" event_id="<?= $event["id"] ?>"></i>
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