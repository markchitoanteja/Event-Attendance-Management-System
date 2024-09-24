<?php
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
    require_once "model.php";

    $model = new Model('localhost', 'root', '', 'event_attendance_management_system');

    $_SESSION["title"] = "Dashboard";
    $_SESSION["current_page"] = "dashboard";

    $logs = null;

    $query = "SELECT * FROM `logs` ORDER BY `id` DESC";
    $result = $model->query($query);

    if ($result->num_rows > 0) {
        $logs = $result->fetch_all(MYSQLI_ASSOC);

        $totalLogs = count($logs);
        $logsPerPage = 5;
        $totalPages = ceil($totalLogs / $logsPerPage);

        $currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $currentPage = max(1, min($totalPages, $currentPage));
        $offset = ($currentPage - 1) * $logsPerPage;
        $currentLogs = array_slice($logs, $offset, $logsPerPage);
    }

    // Total Events
    $query_2 = "SELECT COUNT(*) as `total_events` FROM `events`";
    $result_2 = $model->query($query_2);
    $total_events = $result_2->fetch_assoc()["total_events"];

    // Upcoming Events
    $query_3 = "SELECT COUNT(*) as `total_upcoming_events` FROM `events` WHERE `status` = 'Upcoming'";
    $result_3 = $model->query($query_3);
    $total_upcoming_events  = $result_3->fetch_assoc()["total_upcoming_events"];

    // Registered Attendees
    $query_4 = "SELECT COUNT(*) as `total_registered_attendees` FROM `attendees`";
    $result_4 = $model->query($query_4);
    $total_registered_attendees  = $result_4->fetch_assoc()["total_registered_attendees"];

    // Check-ins Today
    $current_date = date("Y-m-d");

    $query_5 = "SELECT COUNT(*) as `total_check_ins_today` FROM `attendance` WHERE DATE(`created_at`) = '" . $current_date . "'";
    $result_5 = $model->query($query_5);
    $total_check_ins_today = $result_5->fetch_assoc()["total_check_ins_today"];
}
?>

<?php include_once "header.php" ?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <!-- Total Events -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $total_events ?></h3>
                            <p>Total Events</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-calendar-alt"></i>
                        </div>
                        <a href="events_management" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <!-- Upcoming Events -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= $total_upcoming_events ?></h3>
                            <p>Upcoming Events</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-calendar-check"></i>
                        </div>
                        <a href="events_management" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <!-- Attendees Registered -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $total_registered_attendees ?></h3>
                            <p>Registered Attendees</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-users"></i>
                        </div>
                        <a href="attendee_directory" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <!-- Check-ins Today -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= $total_check_ins_today ?></h3>
                            <p>Check-ins Today</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-user-check"></i>
                        </div>
                        <a href="current_event" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <!-- Calendar and Recent Activities -->
            <div class="row">
                <!-- Recent Activities -->
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-clock"></i> Recent Activities</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                <?php if ($currentLogs): ?>
                                    <?php foreach ($currentLogs as $log): ?>
                                        <li class="list-group-item">
                                            <i class="<?= $log['icon']; ?> mr-2"></i>
                                            <?= $log['event']; ?>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li class="list-group-item text-muted">No event logs available yet.</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <div class="card-footer d-flex justify-content-end pb-0 mb-0 pt-3">
                            <nav>
                                <ul class="pagination">
                                    <li class="page-item <?= $currentPage === 1 ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?= max(1, $currentPage - 1); ?>">Previous</a>
                                    </li>

                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?= $i == $currentPage ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <li class="page-item <?= $currentPage === $totalPages ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?= min($totalPages, $currentPage + 1); ?>">Next</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include_once "footer.php" ?>