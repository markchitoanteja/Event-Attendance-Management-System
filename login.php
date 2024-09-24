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

    $current_datetime = date('Y-m-d H:i:s');

    $query = "UPDATE `events` SET `status` = 'Done' WHERE `date` < '$current_datetime' AND `status` != 'Done'";
    $model->query($query);

    $query_2 = "SELECT `ip_address` FROM `settings`";
    $result_2 = $model->query($query_2);

    $ip_address = $result_2->fetch_assoc()["ip_address"];

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION["user_id"])) {
        header("location: dashboard");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" href="static/dist/img/favicon.ico" type="image/x-icon">

    <title>Event Attendance Management System | Login</title>

    <link rel="stylesheet" href="static/dist/css/fonts.min.css">
    <link rel="stylesheet" href="static/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="static/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="static/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="static/dist/css/login.css?v=1.0.2">
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <?php if (isset($_SESSION["notification"])): ?>
            <div class="alert <?= $_SESSION["notification"]["type"] ?> text-center" id="notification"><?= $_SESSION["notification"]["message"] ?></div>
        <?php endif ?>

        <div class="alert alert-danger text-center d-none" id="login_notification">Invalid Username or Password</div>

        <div class="card glass-card">
            <div class="card-header text-center">
                <img src="static/dist/img/logo.png" alt="ESSU Logo" style="width: 128px; height: 128px;">

                <div class="h3 mt-3">
                    <b>Event Attendance Management System</b>
                </div>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Sign in to continue</p>

                <form action="javascript:void(0)" id="login_form">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Username" id="login_username" value="<?= isset($_SESSION["username"]) ? $_SESSION["username"] : null ?>" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-2">
                        <input type="password" class="form-control" placeholder="Password" id="login_password" value="<?= isset($_SESSION["password"]) ? $_SESSION["password"] : null ?>" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="icheck-primary">
                                <input type="checkbox" id="login_remember_me">
                                <label for="login_remember_me">
                                    Remember Me
                                </label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3" id="login_submit">Login</button>

                    <span>
                        Are you a student?
                        <a href="javascript:void(0)" id="download_app">Download Mobile App</a>
                    </span>
                </form>
            </div>
        </div>
    </div>

    <!-- Download App Modal -->
    <div class="modal fade" id="download_app_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Loading Overlay -->
                <div class="overlay loading d-none">
                    <i class="fas fa-2x fa-sync fa-spin"></i>
                </div>
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title">Download App</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- Modal Body -->
                <div class="modal-body text-center">
                    <!-- Success Content -->
                    <div id="ip_ok" class="d-none">
                        <p>Scan the QR code below to download the app:</p>
                        <div class="row mb-4">
                            <div class="col-lg-12 d-flex justify-content-center">
                                <div style="width: 300px; height: 300px;" id="qrcode"></div>
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            <p class="mb-1">Or copy the link below to download the app:</p>
                            <input type="url" class="form-control" id="download_app_download_link">
                        </div>
                    </div>
                    <!-- Error Content -->
                    <div id="ip_not_ok" class="d-none">
                        <div class="text-center py-3">
                            <h4 class="text-danger">
                                The system is unable to establish a connection with the provided IP address.
                                Please verify the IP address and update it in the system settings.
                            </h4>
                        </div>
                    </div>
                </div>
                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        var ip_address = "<?= $ip_address ?>";
    </script>

    <script src="static/plugins/jquery/jquery.min.js"></script>
    <script src="static/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="static/plugins/qrcode/qrcode.min.js"></script>
    <script src="static/dist/js/adminlte.min.js"></script>
    <script src="static/dist/js/login.js?v=1.0.6"></script>
</body>

</html>

<?php unset($_SESSION["notification"]) ?>