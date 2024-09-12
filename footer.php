        <footer class="main-footer">
            <strong>Copyright &copy; 2024 <a href="login">Event Attendance Management System</a>.</strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> 1.0.0
            </div>
        </footer>
        </div>

        <!-- Account Settings Modal -->
        <div class="modal fade" id="account_settings_modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="overlay loading">
                        <i class="fas fa-2x fa-sync fa-spin"></i>
                    </div>
                    <div class="modal-header">
                        <h5 class="modal-title">Account Settings</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="javascript:void(0)" id="account_settings_form">
                        <div class="modal-body">
                            <div class="text-center mb-3">
                                <img id="account_settings_image_display" class="rounded-circle" alt="User Image" style="width: 100px; height: 100px;">
                            </div>
                            <div class="form-group text-center">
                                <label for="account_settings_image">Upload Image</label>
                                <input type="file" class="form-control-file" id="account_settings_image" accept="image/*">
                            </div>
                            <div class="form-group">
                                <label for="account_settings_name">Name</label>
                                <input type="text" class="form-control" id="account_settings_name" placeholder="Enter your name" required>
                            </div>
                            <div class="form-group">
                                <label for="account_settings_username">Username</label>
                                <input type="text" class="form-control" id="account_settings_username" placeholder="Enter your username" required>
                                <small class="text-danger d-none" id="error_account_settings_username">Username is already in use</small>
                            </div>
                            <div class="form-group">
                                <label for="account_settings_password">Password</label>
                                <input type="password" class="form-control" id="account_settings_password" placeholder="Password hidden for security purposes">
                                <small class="text-danger d-none" id="error_account_settings_password">Passwords do not match</small>
                            </div>
                            <div class="form-group">
                                <label for="account_settings_confirm_password">Confirm Password</label>
                                <input type="password" class="form-control" id="account_settings_confirm_password" placeholder="Password hidden for security purposes">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" id="account_settings_id">
                            <input type="hidden" id="account_settings_old_username">
                            <input type="hidden" id="account_settings_old_password">
                            <input type="hidden" id="account_settings_old_image">

                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="account_settings_submit">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            var mode = "<?= isset($_SESSION["mode"]) ? $_SESSION["mode"] : "light" ?>";
            var user_id = "<?= $_SESSION["user_id"] ?>";
            var notification = <?= isset($_SESSION["notification"]) ? json_encode($_SESSION["notification"]) : json_encode(null) ?>;
        </script>

        <script src="static/plugins/jquery/jquery.min.js"></script>
        <script src="static/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="static/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
        <script src="static/plugins/sweetalert2/js/sweetalert2.min.js"></script>
        <script src="static/plugins/datatables/js/dataTables.min.js"></script>
        <script src="static/plugins/datatables/js/dataTables.bootstrap4.min.js"></script>
        <script src="static/plugins/inputmask/inputmask.min.js"></script>
        <script src="static/plugins/select2/js/select2.full.min.js"></script>
        <script src="static/dist/js/adminlte.min.js"></script>
        <script src="static/dist/js/main.js?v=1.0.3"></script>
        </body>

        </html>

        <?php unset($_SESSION["notification"]) ?>