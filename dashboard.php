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
    $_SESSION["title"] = "Dashboard";
    $_SESSION["current_page"] = "dashboard";
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
                            <h3>150</h3>
                            <p>Total Events</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-calendar-alt"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <!-- Upcoming Events -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>53</h3>
                            <p>Upcoming Events</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-calendar-check"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <!-- Attendees Registered -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>500</h3>
                            <p>Registered Attendees</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-users"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <!-- Check-ins Today -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>120</h3>
                            <p>Check-ins Today</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-user-check"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
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
                                <li class="list-group-item">
                                    <i class="fa fa-check-circle text-success"></i> 10 attendees checked in to "Annual Conference"
                                </li>
                                <li class="list-group-item">
                                    <i class="fa fa-times-circle text-danger"></i> "Workshop on AI" has been cancelled
                                </li>
                                <li class="list-group-item">
                                    <i class="fa fa-calendar-alt text-info"></i> "Marketing Meetup" scheduled for next week
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include_once "footer.php" ?>