<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION["user_id"])) {
    header("location: dashboard");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" href="../static/dist/img/favicon.ico" type="image/x-icon">

    <title>Event Attendance Management System | Login</title>

    <link rel="stylesheet" href="../static/dist/css/fonts.min.css">
    <link rel="stylesheet" href="../static/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../static/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="../static/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../static/dist/css/login.css?v=1.0.1">
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <?php if (isset($_SESSION["notification"])): ?>
            <div class="alert <?= $_SESSION["notification"]["type"] ?> text-center" id="notification"><?= $_SESSION["notification"]["message"] ?></div>
        <?php endif ?>

        <div class="alert alert-danger text-center d-none" id="login_notification">Invalid Username or Password</div>

        <div class="card glass-card">
            <div class="card-header text-center">
                <img src="../static/dist/img/logo.png" alt="ESSU Logo" style="width: 128px; height: 128px;">

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

                    <button type="submit" class="btn btn-primary w-100" id="login_submit">Login</button>
                </form>
            </div>
        </div>
    </div>

    <script src="../static/plugins/jquery/jquery.min.js"></script>
    <script src="../static/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../static/dist/js/adminlte.min.js"></script>
    <script src="../static/dist/js/login.js?v=1.0.2"></script>
</body>

</html>

<?php unset($_SESSION["notification"]) ?>