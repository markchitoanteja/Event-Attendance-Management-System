<?php
$userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);

$isMobile = strpos($userAgent, 'mobile') !== false;

if ($isMobile) {
    header('HTTP/1.1 403 Forbidden');
    echo "Access forbidden: This website is only accessible from desktop devices.";

    exit;
} else {
    require_once "model.php";

    $model = new Model('localhost', 'root', '', 'event_attendance_management_system');

    $query = "SELECT * FROM `users` WHERE id = '" . $_SESSION["user_id"] . "'";
    $result = $model->query($query);

    $admin = $result->fetch_assoc();

    $current_datetime = date('Y-m-d H:i:s');

    $query_2 = "UPDATE `events` SET `status` = 'Done' WHERE `date` < '$current_datetime' AND `status` != 'Done'";
    $model->query($query_2);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" href="static/dist/img/favicon.ico" type="image/x-icon">

    <title>Event Attendance Management System | <?= $_SESSION["title"] ?></title>

    <link rel="stylesheet" href="static/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="static/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <link rel="stylesheet" href="static/plugins/sweetalert2/css/sweetalert2.min.css">
    <link rel="stylesheet" href="static/plugins/datatables/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="static/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="static/plugins/select2/css/select2-bootstrap4.min.css">
    <link rel="stylesheet" href="static/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="static/dist/css/fonts.min.css">
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed <?= isset($_SESSION["mode"]) && $_SESSION["mode"] == "dark" ? "dark-mode" : null ?>">
    <div class="wrapper">
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__wobble" src="static/dist/img/logo.png" alt="ESSU Logo" height="60" width="60">
            <span>ESSU Can-Avid</span>
        </div>

        <nav class="main-header navbar navbar-expand <?= isset($_SESSION["mode"]) && $_SESSION["mode"] == "dark" ? "navbar-dark" : "navbar-light" ?>">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="javascript:void(0)" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="javascript:void(0)">
                        <i class="fas fa-cog"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="javascript:void(0)" class="dropdown-item" id="ip_address">
                            <i class="fas fa-server mr-2"></i> IP Address
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:void(0)" class="dropdown-item" id="account_settings">
                            <i class="fas fa-user mr-2"></i> Account Settings
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:void(0)" class="dropdown-item" id="mode">
                            <i class="fas fa-<?= isset($_SESSION["mode"]) && $_SESSION["mode"] == "dark" ? "sun" : "moon" ?> mr-2"></i> <?= isset($_SESSION["mode"]) && $_SESSION["mode"] == "dark" ? "Light Mode" : "Dark Mode" ?>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:void(0)" class="dropdown-item logout">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </a>
                    </div>
                </li>
            </ul>
        </nav>

        <aside class="main-sidebar sidebar-<?= isset($_SESSION["mode"]) && $_SESSION["mode"] == "dark" ? "dark" : "light" ?>-primary elevation-4">
            <a href="" class="brand-link">
                <img src="static/dist/img/logo.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">Event Attendance</span>
            </a>

            <div class="sidebar">
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="static/uploads/<?= $admin["image"] ?>" class="img-circle elevation-2" style="width: 33.59; aspect-ratio: 1/1;" alt="User Image">
                    </div>
                    <div class="info">
                        <span class="d-block text-truncate"><?= $admin["name"] ?></span>
                    </div>
                </div>

                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="dashboard" class="nav-link <?= $_SESSION["current_page"] == "dashboard" ? "active" : null ?>">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="current_event" class="nav-link <?= $_SESSION["current_page"] == "current_event" ? "active" : null ?>">
                                <i class="nav-icon fas fa-bolt"></i>
                                <p>Current Event</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="attendance_management" class="nav-link <?= $_SESSION["current_page"] == "attendance_management" ? "active" : null ?>">
                                <i class="nav-icon fas fa-user-check"></i>
                                <p>Attendance Management</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="events_management" class="nav-link <?= $_SESSION["current_page"] == "events_management" ? "active" : null ?>">
                                <i class="nav-icon fas fa-calendar-plus"></i>
                                <p>Events Management</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="attendee_directory" class="nav-link <?= $_SESSION["current_page"] == "attendee_directory" ? "active" : null ?>">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Attendee Directory</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="javascript:void(0)" class="nav-link logout">
                                <i class="nav-icon fas fa-sign-out-alt"></i>
                                <p>Logout</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>