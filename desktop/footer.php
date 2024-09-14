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

        <?php if ($_SESSION["current_page"] == "attendee_directory"): ?>
            <!-- New Attendee Modal -->
            <div class="modal fade" id="new_attendee_modal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="overlay loading d-none">
                            <i class="fas fa-2x fa-sync fa-spin"></i>
                        </div>
                        <div class="modal-header">
                            <h5 class="modal-title">New Attendee</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="javascript:void(0)" id="new_attendee_form">
                            <div class="modal-body">
                                <div class="text-center mb-3">
                                    <img id="new_attendee_image_display" class="rounded-circle" src="../static/uploads/default-user-image.png" alt="User Image" style="width: 100px; height: 100px;">
                                </div>
                                <div class="form-group text-center">
                                    <label for="new_attendee_image">Upload Image</label>
                                    <input type="file" class="form-control-file" id="new_attendee_image" accept="image/*" required>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="new_attendee_student_number">Student Number</label>
                                            <input type="text" class="form-control" id="new_attendee_student_number" required>
                                            <small class="text-danger d-none" id="error_new_attendee_student_number">Student Number is already in use</small>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="new_attendee_course">Course</label>
                                            <select id="new_attendee_course" class="custom-select" required>
                                                <option value selected disabled></option>
                                                <option value="BSIT">Bachelor of Science in Information Technology</option>
                                                <option value="BSA">Bachelor of Science in Agrculture</option>
                                                <option value="BSCrim">Bachelor of Science in Criminology</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label for="new_attendee_year">Year</label>
                                            <select id="new_attendee_year" class="custom-select" required>
                                                <option value selected disabled></option>
                                                <option value="1st">1st Year</option>
                                                <option value="2nd">2nd Year</option>
                                                <option value="3rd">3rd Year</option>
                                                <option value="4th">4th Year</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label for="new_attendee_section">Section</label>
                                            <input type="text" class="form-control" id="new_attendee_section" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="new_attendee_first_name">First Name</label>
                                            <input type="text" class="form-control" id="new_attendee_first_name" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="new_attendee_middle_name">Middle Name</label>
                                            <input type="text" class="form-control" id="new_attendee_middle_name">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="new_attendee_last_name">Last Name</label>
                                            <input type="text" class="form-control" id="new_attendee_last_name" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="new_attendee_birthday">Birthday</label>
                                            <input type="date" class="form-control" id="new_attendee_birthday" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="new_attendee_mobile_number" class="required">Mobile Number</label>
                                            <input type="text" class="form-control" id="new_attendee_mobile_number" data-inputmask='"mask": "9999 999 9999"' data-mask required>
                                            <small class="text-danger d-none" id="error_new_attendee_mobile_number">Invalid Mobile Number</small>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="new_attendee_email">Email</label>
                                            <input type="email" class="form-control" id="new_attendee_email" require>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="new_attendee_address">Address</label>
                                            <textarea id="new_attendee_address" class="form-control" required></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="new_attendee_username">Username</label>
                                            <input type="text" class="form-control" id="new_attendee_username" required>
                                            <small class="text-danger d-none" id="error_new_attendee_username">Username is already in use</small>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="new_attendee_password">Password</label>
                                            <input type="password" class="form-control" id="new_attendee_password" required>
                                            <small class="text-danger d-none" id="error_new_attendee_password">Passwords do not match</small>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="new_attendee_confirm_password">Confirm Password</label>
                                            <input type="password" class="form-control" id="new_attendee_confirm_password" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" id="new_attendee_submit">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Update Attendee Modal -->
            <div class="modal fade" id="update_attendee_modal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="overlay loading">
                            <i class="fas fa-2x fa-sync fa-spin"></i>
                        </div>
                        <div class="modal-header">
                            <h5 class="modal-title">Update Attendee</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="javascript:void(0)" id="update_attendee_form">
                            <div class="modal-body">
                                <div class="text-center mb-3">
                                    <img id="update_attendee_image_display" class="rounded-circle" src="../static/uploads/default-user-image.png" alt="User Image" style="width: 100px; height: 100px;">
                                </div>
                                <div class="form-group text-center">
                                    <label for="update_attendee_image">Upload Image</label>
                                    <input type="file" class="form-control-file" id="update_attendee_image" accept="image/*">
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="update_attendee_student_number">Student Number</label>
                                            <input type="text" class="form-control" id="update_attendee_student_number" required>
                                            <small class="text-danger d-none" id="error_update_attendee_student_number">Student Number is already in use</small>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="update_attendee_course">Course</label>
                                            <select id="update_attendee_course" class="custom-select" required>
                                                <option value="BSIT">Bachelor of Science in Information Technology</option>
                                                <option value="BSA">Bachelor of Science in Agrculture</option>
                                                <option value="BSCrim">Bachelor of Science in Criminology</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label for="update_attendee_year">Year</label>
                                            <select id="update_attendee_year" class="custom-select" required>
                                                <option value="1st">1st Year</option>
                                                <option value="2nd">2nd Year</option>
                                                <option value="3rd">3rd Year</option>
                                                <option value="4th">4th Year</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label for="update_attendee_section">Section</label>
                                            <input type="text" class="form-control" id="update_attendee_section" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="update_attendee_first_name">First Name</label>
                                            <input type="text" class="form-control" id="update_attendee_first_name" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="update_attendee_middle_name">Middle Name</label>
                                            <input type="text" class="form-control" id="update_attendee_middle_name">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="update_attendee_last_name">Last Name</label>
                                            <input type="text" class="form-control" id="update_attendee_last_name" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="update_attendee_birthday">Birthday</label>
                                            <input type="date" class="form-control" id="update_attendee_birthday" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="update_attendee_mobile_number" class="required">Mobile Number</label>
                                            <input type="text" class="form-control" id="update_attendee_mobile_number" data-inputmask='"mask": "9999 999 9999"' data-mask required>
                                            <small class="text-danger d-none" id="error_update_attendee_mobile_number">Invalid Mobile Number</small>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="update_attendee_email">Email</label>
                                            <input type="email" class="form-control" id="update_attendee_email" require>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="update_attendee_address">Address</label>
                                            <textarea id="update_attendee_address" class="form-control" required></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="update_attendee_username">Username</label>
                                            <input type="text" class="form-control" id="update_attendee_username" required>
                                            <small class="text-danger d-none" id="error_update_attendee_username">Username is already in use</small>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="update_attendee_password">Password</label>
                                            <input type="password" class="form-control" id="update_attendee_password" placeholder="Password is hidden">
                                            <small class="text-danger d-none" id="error_update_attendee_password">Passwords do not match</small>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="update_attendee_confirm_password">Confirm Password</label>
                                            <input type="password" class="form-control" id="update_attendee_confirm_password" placeholder="Password is hidden">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="hidden" id="update_attendee_id">
                                <input type="hidden" id="update_attendee_old_student_number">
                                <input type="hidden" id="update_attendee_old_username">
                                <input type="hidden" id="update_attendee_old_password">
                                <input type="hidden" id="update_attendee_old_image">

                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" id="update_attendee_submit">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif ?>

        <script>
            var mode = "<?= isset($_SESSION["mode"]) ? $_SESSION["mode"] : "light" ?>";
            var user_id = "<?= $_SESSION["user_id"] ?>";
            var notification = <?= isset($_SESSION["notification"]) ? json_encode($_SESSION["notification"]) : json_encode(null) ?>;
        </script>

        <script src="../static/plugins/jquery/jquery.min.js"></script>
        <script src="../static/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="../static/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
        <script src="../static/plugins/sweetalert2/js/sweetalert2.min.js"></script>
        <script src="../static/plugins/datatables/js/dataTables.min.js"></script>
        <script src="../static/plugins/datatables/js/dataTables.bootstrap4.min.js"></script>
        <script src="../static/plugins/inputmask/inputmask.min.js"></script>
        <script src="../static/plugins/select2/js/select2.full.min.js"></script>
        <script src="../static/dist/js/adminlte.min.js"></script>
        <script src="../static/dist/js/main.js?v=1.1.6"></script>
        </body>

        </html>

        <?php unset($_SESSION["notification"]) ?>