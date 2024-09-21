jQuery(document).ready(function () {
    if (notification) {
        Swal.fire({
            title: notification.title,
            text: notification.text,
            icon: notification.icon
        });
    }

    if (attendee_data) {
        $("#display_attendee_modal").modal("show");
    }

    if (current_page == "current_event") {
        if (current_event_uuid) {
            generate_qr_code(current_event_uuid);

            setInterval(function () {
                check_attendance();
            }, 100);
        }
    }

    $('.select2').select2();

    $('[data-mask]').inputmask();

    $(".datatable").DataTable({
        "paging": true,
        "lengthChange": false,
        "ordering": false
    })

    $(".datatable-no-fiter").DataTable({
        "filter": false,
        "paging": true,
        "lengthChange": false,
        "ordering": false
    })

    $("#mode").click(function () {
        let new_mode = "";

        if (mode == "light") {
            new_mode = "dark";
        } else {
            new_mode = "light";
        }

        var formData = new FormData();

        formData.append('mode', new_mode);

        formData.append('change_mode', true);

        $.ajax({
            url: 'server',
            data: formData,
            type: 'POST',
            dataType: 'JSON',
            processData: false,
            contentType: false,
            success: function (response) {
                if (response) {
                    location.reload();
                }
            },
            error: function (_, _, error) {
                console.error(error);
            }
        });
    })

    $(".logout").click(function () {
        var formData = new FormData();

        formData.append('logout', true);

        $.ajax({
            url: 'server',
            data: formData,
            type: 'POST',
            dataType: 'JSON',
            processData: false,
            contentType: false,
            success: function (response) {
                if (response) {
                    location.href = "login";
                }
            },
            error: function (_, _, error) {
                console.error(error);
            }
        });
    })

    $("#account_settings").click(function () {
        $("#account_settings_modal").modal("show");

        var formData = new FormData();

        formData.append('id', user_id);

        formData.append('get_admin_data', true);

        $.ajax({
            url: 'server',
            data: formData,
            type: 'POST',
            dataType: 'JSON',
            processData: false,
            contentType: false,
            success: function (response) {
                if (response) {
                    $("#account_settings_name").val(response.name);
                    $("#account_settings_username").val(response.username);
                    $("#account_settings_image_display").attr("src", "static/uploads/" + response.image);

                    $("#account_settings_id").val(response.id);
                    $("#account_settings_old_username").val(response.username);
                    $("#account_settings_old_password").val(response.password);
                    $("#account_settings_old_image").val(response.image);

                    $(".loading").addClass("d-none");
                }
            },
            error: function (_, _, error) {
                console.error(error);
            }
        });
    })

    $("#account_settings_image").change(function (event) {
        var displayImage = $('#account_settings_image_display');
        var file = event.target.files[0];

        if (file) {
            var imageURL = URL.createObjectURL(file);

            displayImage.attr('src', imageURL);

            displayImage.on('load', function () {
                URL.revokeObjectURL(imageURL);
            });
        } else {
            displayImage.attr('src', "static/uploads/" + $("#account_settings_old_image").val());
        }
    })

    $("#account_settings_form").submit(function () {
        const id = $("#account_settings_id").val();
        const name = $("#account_settings_name").val();
        const username = $("#account_settings_username").val();
        let password = $("#account_settings_password").val();
        const confirm_password = $("#account_settings_confirm_password").val();
        const old_username = $("#account_settings_old_username").val();
        const old_password = $("#account_settings_old_password").val();
        const image_input = $("#account_settings_image")[0];
        const image = $("#account_settings_old_image").val();

        let errors = 0;
        let is_new_image = false;

        if (image_input.files.length > 0) {
            var image_file = image_input.files[0];

            is_new_image = true;
        }

        if (password != confirm_password) {
            $("#account_settings_password").addClass("is-invalid");
            $("#account_settings_confirm_password").addClass("is-invalid");
            $("#error_account_settings_password").removeClass("d-none");

            errors++;
        }

        if (!errors) {
            $("#account_settings_submit").text("Please wait...");
            $("#account_settings_submit").attr("disabled", true);
            $(".loading").removeClass("d-none");

            let is_new_password = true;
            let is_new_username = false;

            if (!password) {
                password = old_password;

                is_new_password = false;
            }

            if (username != old_username) {
                is_new_username = true;
            }

            var formData = new FormData();

            formData.append('id', id);
            formData.append('name', name);
            formData.append('username', username);
            formData.append('password', password);
            formData.append('is_new_username', is_new_username);
            formData.append('is_new_password', is_new_password);
            formData.append('image', image);
            formData.append('image_file', image_file);
            formData.append('is_new_image', is_new_image);

            formData.append('update_admin', true);

            $.ajax({
                url: 'server',
                data: formData,
                type: 'POST',
                dataType: 'JSON',
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response) {
                        setTimeout(function () {
                            location.reload();
                        }, 1500);
                    } else {
                        $("#account_settings_username").addClass("is-invalid");
                        $("#error_account_settings_username").removeClass("d-none");

                        $("#account_settings_submit").text("Save changes");
                        $("#account_settings_submit").removeAttr("disabled");
                        $(".loading").addClass("d-none");
                    }
                },
                error: function (_, _, error) {
                    console.error(error);
                }
            });
        }
    })

    $("#account_settings_password").keydown(function () {
        $("#account_settings_password").removeClass("is-invalid");
        $("#account_settings_confirm_password").removeClass("is-invalid");
        $("#error_account_settings_password").addClass("d-none");
    })

    $("#account_settings_username").keydown(function () {
        $("#account_settings_username").removeClass("is-invalid");
        $("#error_account_settings_username").addClass("d-none");
    })

    $("#account_settings_confirm_password").keydown(function () {
        $("#account_settings_password").removeClass("is-invalid");
        $("#account_settings_confirm_password").removeClass("is-invalid");
        $("#error_account_settings_password").addClass("d-none");
    })

    $("#new_attendee_form").submit(function () {
        const image = $("#new_attendee_image")[0].files[0];
        const student_number = $("#new_attendee_student_number").val();
        const course = $("#new_attendee_course").val();
        const year = $("#new_attendee_year").val();
        const section = $("#new_attendee_section").val();
        const first_name = $("#new_attendee_first_name").val();
        const middle_name = $("#new_attendee_middle_name").val();
        const last_name = $("#new_attendee_last_name").val();
        const birthday = $("#new_attendee_birthday").val();
        const mobile_number = $("#new_attendee_mobile_number").val().replace(/\D/g, '');
        const email = $("#new_attendee_email").val();
        const address = $("#new_attendee_address").val();
        const username = $("#new_attendee_username").val();
        const password = $("#new_attendee_password").val();
        const confirm_password = $("#new_attendee_confirm_password").val();

        let errors = 0

        if (password != confirm_password) {
            $("#new_attendee_password").addClass("is-invalid");
            $("#new_attendee_confirm_password").addClass("is-invalid");
            $("#error_new_attendee_password").removeClass("d-none");

            $("#new_attendee_password").focus();

            errors++;
        }

        if (mobile_number.length != 11) {
            $("#new_attendee_mobile_number").addClass("is-invalid");
            $("#error_new_attendee_mobile_number").removeClass("d-none");

            $("#new_attendee_mobile_number").focus();

            errors++;
        }

        if (!errors) {
            $(".loading").removeClass("d-none");
            $("#new_attendee_submit").text("Please wait...");
            $("#new_attendee_submit").attr("disabled", true);

            var formData = new FormData();

            formData.append('image', image);
            formData.append('student_number', student_number);
            formData.append('course', course);
            formData.append('year', year);
            formData.append('section', section);
            formData.append('first_name', first_name);
            formData.append('middle_name', middle_name);
            formData.append('last_name', last_name);
            formData.append('birthday', birthday);
            formData.append('mobile_number', mobile_number);
            formData.append('email', email);
            formData.append('address', address);
            formData.append('username', username);
            formData.append('password', password);

            formData.append('add_attendee', true);

            $.ajax({
                url: 'server',
                data: formData,
                type: 'POST',
                dataType: 'JSON',
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.username && response.student_number) {
                        setTimeout(function () {
                            location.reload();
                        }, 1500);
                    } else {
                        $(".loading").addClass("d-none");
                        $("#new_attendee_submit").text("Submit");
                        $("#new_attendee_submit").removeAttr("disabled");

                        if (!response.username) {
                            $("#new_attendee_username").addClass("is-invalid");
                            $("#error_new_attendee_username").removeClass("d-none");

                            $("#new_attendee_username").focus();
                        }

                        if (!response.student_number) {
                            $("#new_attendee_student_number").addClass("is-invalid");
                            $("#error_new_attendee_student_number").removeClass("d-none");

                            $("#new_attendee_student_number").focus();
                        }
                    }
                },
                error: function (_, _, error) {
                    console.error(error);
                }
            });
        }
    })

    $("#new_attendee_image").change(function (event) {
        var displayImage = $('#new_attendee_image_display');
        var file = event.target.files[0];

        if (file) {
            var imageURL = URL.createObjectURL(file);

            displayImage.attr('src', imageURL);

            displayImage.on('load', function () {
                URL.revokeObjectURL(imageURL);
            });
        } else {
            displayImage.attr('src', "static/uploads/default-user-image.png");
        }
    })

    $("#new_attendee_student_number").keydown(function () {
        $("#new_attendee_student_number").removeClass("is-invalid");
        $("#error_new_attendee_student_number").addClass("d-none");
    })

    $("#new_attendee_username").keydown(function () {
        $("#new_attendee_username").removeClass("is-invalid");
        $("#error_new_attendee_username").addClass("d-none");
    })

    $("#new_attendee_mobile_number").keydown(function () {
        $("#new_attendee_mobile_number").removeClass("is-invalid");
        $("#error_new_attendee_mobile_number").addClass("d-none");
    })

    $("#new_attendee_password").keydown(function () {
        $("#new_attendee_password").removeClass("is-invalid");
        $("#new_attendee_confirm_password").removeClass("is-invalid");
        $("#error_new_attendee_password").addClass("d-none");
    })

    $("#new_attendee_confirm_password").keydown(function () {
        $("#new_attendee_password").removeClass("is-invalid");
        $("#new_attendee_confirm_password").removeClass("is-invalid");
        $("#error_new_attendee_password").addClass("d-none");
    })

    $(document).on("click", ".delete_attendee", function () {
        const attendee_id = $(this).attr("attendee_id");

        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = new FormData();

                formData.append('attendee_id', attendee_id);

                formData.append('delete_attendee', true);

                $.ajax({
                    url: 'server',
                    data: formData,
                    type: 'POST',
                    dataType: 'JSON',
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response) {
                            location.reload();
                        }
                    },
                    error: function (_, _, error) {
                        console.error(error);
                    }
                });
            }
        });
    })

    $(document).on("click", ".edit_attendee", function () {
        const attendee_id = $(this).attr("attendee_id");

        $("#update_attendee_modal").modal("show");

        var formData = new FormData();

        formData.append('attendee_id', attendee_id);

        formData.append('get_attendee_data', true);

        $.ajax({
            url: 'server',
            data: formData,
            type: 'POST',
            dataType: 'JSON',
            processData: false,
            contentType: false,
            success: function (response) {
                if (response) {
                    $("#update_attendee_image_display").attr("src", "static/uploads/" + response.image);
                    $("#update_attendee_student_number").val(response.student_number);
                    $("#update_attendee_course").val(response.course);
                    $("#update_attendee_year").val(response.year);
                    $("#update_attendee_section").val(response.section);
                    $("#update_attendee_first_name").val(response.first_name);
                    $("#update_attendee_middle_name").val(response.middle_name);
                    $("#update_attendee_last_name").val(response.last_name);
                    $("#update_attendee_birthday").val(response.birthday);
                    $("#update_attendee_mobile_number").val(response.mobile_number);
                    $("#update_attendee_email").val(response.email);
                    $("#update_attendee_address").val(response.address);
                    $("#update_attendee_username").val(response.username);

                    $("#update_attendee_id").val(attendee_id);
                    $("#update_attendee_old_student_number").val(response.student_number);
                    $("#update_attendee_old_username").val(response.username);
                    $("#update_attendee_old_password").val(response.password);
                    $("#update_attendee_old_image").val(response.image);

                    $(".loading").addClass("d-none");
                }
            },
            error: function (_, _, error) {
                console.error(error);
            }
        });
    })

    $("#update_attendee_form").submit(function () {
        const image_input = $("#update_attendee_image")[0];
        const student_number = $("#update_attendee_student_number").val();
        const course = $("#update_attendee_course").val();
        const year = $("#update_attendee_year").val();
        const section = $("#update_attendee_section").val();
        const first_name = $("#update_attendee_first_name").val();
        const middle_name = $("#update_attendee_middle_name").val();
        const last_name = $("#update_attendee_last_name").val();
        const birthday = $("#update_attendee_birthday").val();
        const mobile_number = $("#update_attendee_mobile_number").val().replace(/\D/g, '');
        const email = $("#update_attendee_email").val();
        const address = $("#update_attendee_address").val();
        const username = $("#update_attendee_username").val();
        const password = $("#update_attendee_password").val();
        const confirm_password = $("#update_attendee_confirm_password").val();

        const id = $("#update_attendee_id").val();
        const old_student_number = $("#update_attendee_old_student_number").val();
        const old_username = $("#update_attendee_old_username").val();
        const old_password = $("#update_attendee_old_password").val();
        const old_image = $("#update_attendee_old_image").val();

        let errors = 0
        let is_new_student_number = false;
        let is_new_username = false;
        let is_new_password = false;
        let is_new_image = false;

        if (image_input.files.length > 0) {
            var image_file = image_input.files[0];

            is_new_image = true;
        }

        if (password && confirm_password) {
            is_new_password = true;
        }

        if (username != old_username) {
            is_new_username = true;
        }

        if (student_number != old_student_number) {
            is_new_student_number = true;
        }

        if (password != confirm_password) {
            $("#update_attendee_password").addClass("is-invalid");
            $("#update_attendee_confirm_password").addClass("is-invalid");
            $("#error_update_attendee_password").removeClass("d-none");

            $("#update_attendee_password").focus();

            errors++;
        }

        if (mobile_number.length != 11) {
            $("#update_attendee_mobile_number").addClass("is-invalid");
            $("#error_update_attendee_mobile_number").removeClass("d-none");

            $("#update_attendee_mobile_number").focus();

            errors++;
        }

        if (!errors) {
            $(".loading").removeClass("d-none");
            $("#update_attendee_submit").text("Please wait...");
            $("#update_attendee_submit").attr("disabled", true);

            var formData = new FormData();

            formData.append('image_file', image_file);
            formData.append('student_number', student_number);
            formData.append('course', course);
            formData.append('year', year);
            formData.append('section', section);
            formData.append('first_name', first_name);
            formData.append('middle_name', middle_name);
            formData.append('last_name', last_name);
            formData.append('birthday', birthday);
            formData.append('mobile_number', mobile_number);
            formData.append('email', email);
            formData.append('address', address);
            formData.append('username', username);
            formData.append('password', password);

            formData.append('id', id);
            formData.append('old_student_number', old_student_number);
            formData.append('old_username', old_username);
            formData.append('old_password', old_password);
            formData.append('old_image', old_image);

            formData.append('is_new_student_number', is_new_student_number);
            formData.append('is_new_username', is_new_username);
            formData.append('is_new_password', is_new_password);
            formData.append('is_new_image', is_new_image);

            formData.append('update_attendee', true);

            $.ajax({
                url: 'server',
                data: formData,
                type: 'POST',
                dataType: 'JSON',
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.username && response.student_number) {
                        setTimeout(function () {
                            location.reload();
                        }, 1500);
                    } else {
                        $(".loading").addClass("d-none");
                        $("#update_attendee_submit").text("Submit");
                        $("#update_attendee_submit").removeAttr("disabled");

                        if (!response.username) {
                            $("#update_attendee_username").addClass("is-invalid");
                            $("#error_update_attendee_username").removeClass("d-none");

                            $("#update_attendee_username").focus();
                        }

                        if (!response.student_number) {
                            $("#update_attendee_student_number").addClass("is-invalid");
                            $("#error_update_attendee_student_number").removeClass("d-none");

                            $("#update_attendee_student_number").focus();
                        }
                    }
                },
                error: function (_, _, error) {
                    console.error(error);
                }
            });
        }
    })

    $("#update_attendee_image").change(function (event) {
        var displayImage = $('#update_attendee_image_display');
        var old_image = $('#update_attendee_old_image').val();
        var file = event.target.files[0];

        if (file) {
            var imageURL = URL.createObjectURL(file);

            displayImage.attr('src', imageURL);

            displayImage.on('load', function () {
                URL.revokeObjectURL(imageURL);
            });
        } else {
            displayImage.attr('src', "static/uploads/" + old_image);
        }
    })

    $("#update_attendee_student_number").keydown(function () {
        $("#update_attendee_student_number").removeClass("is-invalid");
        $("#error_update_attendee_student_number").addClass("d-none");
    })

    $("#update_attendee_username").keydown(function () {
        $("#update_attendee_username").removeClass("is-invalid");
        $("#error_update_attendee_username").addClass("d-none");
    })

    $("#update_attendee_mobile_number").keydown(function () {
        $("#update_attendee_mobile_number").removeClass("is-invalid");
        $("#error_update_attendee_mobile_number").addClass("d-none");
    })

    $("#update_attendee_password").keydown(function () {
        $("#update_attendee_password").removeClass("is-invalid");
        $("#update_attendee_confirm_password").removeClass("is-invalid");
        $("#error_update_attendee_password").addClass("d-none");
    })

    $("#update_attendee_confirm_password").keydown(function () {
        $("#update_attendee_password").removeClass("is-invalid");
        $("#update_attendee_confirm_password").removeClass("is-invalid");
        $("#error_update_attendee_password").addClass("d-none");
    })

    $(".send_email").click(function () {
        const attendee_id = $(this).attr("attendee_id");

        $("#send_email_modal").modal("show");
        $(".loading").removeClass("d-none");

        var formData = new FormData();

        formData.append('attendee_id', attendee_id);

        formData.append('get_attendee_data', true);

        $.ajax({
            url: 'server',
            data: formData,
            type: 'POST',
            dataType: 'JSON',
            processData: false,
            contentType: false,
            success: function (response) {
                const subject = "Your Login Credentials for the ESSU Can-Avid Campus Mobile Application";
                const message = `Hi ` + response.name + `,\n\nI hope this email finds you well. Here are the credentials you can use to log in to the mobile application of the system.\n\nUsername: ` + response.username + `\nPassword: (type the password here)\n\nBest regards,\nESSU Can-Avid Campus`;

                $("#send_email_email").val(response.email);
                $("#send_email_name").val(response.name);
                $("#send_email_subject").val(subject);
                $("#send_email_message").val(message);

                $(".loading").addClass("d-none");
            },
            error: function (_, _, error) {
                console.error(error);
            }
        });
    })

    $("#send_email_form").submit(function () {
        const name = $("#send_email_name").val();
        const email = $("#send_email_email").val();
        const subject = $("#send_email_subject").val();
        const message = $("#send_email_message").val();

        if (/Password: \(type the password here\)/.test(message)) {
            $("#send_email_message").addClass("is-invalid");
            $("#error_send_email_message").removeClass("d-none");
        } else {
            $("#send_email_submit").text("Please wait...");
            $("#send_email_submit").attr("disabled", true);

            $(".loading").removeClass("d-none");

            var formData = new FormData();

            formData.append('name', name);
            formData.append('email', email);
            formData.append('subject', subject);
            formData.append('message', message);

            formData.append('send_email', true);

            $.ajax({
                url: 'server',
                data: formData,
                type: 'POST',
                dataType: 'JSON',
                processData: false,
                contentType: false,
                success: function () {
                    location.reload();
                },
                error: function (_, _, error) {
                    console.error(error);
                }
            });
        }
    })

    $("#send_email_message").keydown(function () {
        $("#send_email_message").removeClass("is-invalid");
        $("#error_send_email_message").addClass("d-none");
    })

    $("#new_event_form").submit(function () {
        const name = $("#new_event_name").val();
        const date = $("#new_event_date").val();
        const attendees = $("#new_event_attendees").val();

        const current_date = new Date();
        const selected_date = new Date(date);

        let status = "Upcoming";

        if (selected_date < current_date) {
            status = "Done";
        }

        $(".loading").removeClass("d-none");

        $("#new_event_submit").text("Please wait...");
        $("#new_event_submit").attr("disabled", true);

        var formData = new FormData();

        formData.append('name', name);
        formData.append('date', date);
        formData.append('attendees', attendees);
        formData.append('status', status);

        formData.append('add_event', true);

        $.ajax({
            url: 'server',
            data: formData,
            type: 'POST',
            dataType: 'JSON',
            processData: false,
            contentType: false,
            success: function (response) {
                if (response) {
                    setTimeout(function () {
                        location.reload();
                    }, 1500);
                }
            },
            error: function (_, _, error) {
                console.error(error);
            }
        });
    })

    $(document).on("click", ".edit_event", function () {
        const event_id = $(this).attr("event_id");

        $("#update_event_modal").modal("show");
        $(".loading").removeClass("d-none");

        var formData = new FormData();

        formData.append('event_id', event_id);

        formData.append('get_event_data', true);

        $.ajax({
            url: 'server',
            data: formData,
            type: 'POST',
            dataType: 'JSON',
            processData: false,
            contentType: false,
            success: function (response) {
                $("#update_event_id").val(response.id);
                $("#update_event_name").val(response.name);
                $("#update_event_date").val(response.date);
                $("#update_event_attendees").val(response.attendees.split(',')).trigger('change');

                $(".loading").addClass("d-none");
            },
            error: function (_, _, error) {
                console.error(error);
            }
        });
    })

    $(document).on("click", ".delete_event", function () {
        const event_id = $(this).attr("event_id");

        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = new FormData();

                formData.append('event_id', event_id);

                formData.append('delete_event', true);

                $.ajax({
                    url: 'server',
                    data: formData,
                    type: 'POST',
                    dataType: 'JSON',
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response) {
                            location.reload();
                        }
                    },
                    error: function (_, _, error) {
                        console.error(error);
                    }
                });
            }
        });
    })

    $("#update_event_form").submit(function () {
        const id = $("#update_event_id").val();
        const name = $("#update_event_name").val();
        const date = $("#update_event_date").val();
        const attendees = $("#update_event_attendees").val();

        const current_date = new Date();
        const selected_date = new Date(date);

        let status = "Upcoming";

        if (selected_date < current_date) {
            status = "Done";
        }

        $(".loading").removeClass("d-none");

        $("#update_event_submit").text("Please wait...");
        $("#update_event_submit").attr("disabled", true);

        var formData = new FormData();

        formData.append('id', id);
        formData.append('name', name);
        formData.append('date', date);
        formData.append('attendees', attendees);
        formData.append('status', status);

        formData.append('update_event', true);

        $.ajax({
            url: 'server',
            data: formData,
            type: 'POST',
            dataType: 'JSON',
            processData: false,
            contentType: false,
            success: function (response) {
                if (response) {
                    setTimeout(function () {
                        location.reload();
                    }, 1500);
                }
            },
            error: function (_, _, error) {
                console.error(error);
            }
        });
    })

    $(document).on("click", ".set_to_current", function () {
        const event_id = $(this).attr("event_id");

        Swal.fire({
            title: "Set this Event to Current?",
            text: "You are going to set this event to current.",
            icon: "info",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, set it!"
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = new FormData();

                formData.append('event_id', event_id);

                formData.append('set_to_current', true);

                $.ajax({
                    url: 'server',
                    data: formData,
                    type: 'POST',
                    dataType: 'JSON',
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response) {
                            location.reload();
                        }
                    },
                    error: function (_, _, error) {
                        console.error(error);
                    }
                });
            }
        });
    })

    $("#ip_address").click(function () {
        $("#ip_address_modal").modal("show");
        $(".loading").removeClass("d-none");

        var formData = new FormData();

        formData.append('get_settings_data', true);

        $.ajax({
            url: 'server',
            data: formData,
            type: 'POST',
            dataType: 'JSON',
            processData: false,
            contentType: false,
            success: function (response) {
                $("#ip_address_ip").val(response.ip_address);

                $(".loading").addClass("d-none");
            },
            error: function (_, _, error) {
                console.error(error);
            }
        });
    })

    $("#ip_address_form").submit(function () {
        const ip_address = $("#ip_address_ip").val();

        var valid_id = /^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/;

        if (valid_id.test(ip_address)) {
            $(".loading").removeClass("d-none");

            $("#ip_address_submit").text("Please wait...");
            $("#ip_address_submit").attr("disabled", true);

            var formData = new FormData();

            formData.append('ip_address', ip_address);

            formData.append('update_ip_address', true);

            $.ajax({
                url: 'server',
                data: formData,
                type: 'POST',
                dataType: 'JSON',
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response) {
                        setTimeout(function () {
                            location.reload();
                        }, 1500);
                    }
                },
                error: function (_, _, error) {
                    console.error(error);
                }
            });
        } else {
            $("#ip_address_ip").addClass("is-invalid");
            $("#error_ip_address_ip").removeClass("d-none");
        }
    })

    $("#ip_address_ip").keydown(function () {
        $("#ip_address_ip").removeClass("is-invalid");
        $("#error_ip_address_ip").addClass("d-none");
    })

    function generate_qr_code(event_uuid) {
        const qrcode = $("#qrcode");
        const containerWidth = qrcode.width();

        qrcode.empty();

        var _ = new QRCode(document.getElementById("qrcode"), {
            text: event_uuid,
            width: containerWidth,
            height: containerWidth,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    }

    function check_attendance() {
        var formData = new FormData();

        formData.append('check_attendance', true);

        $.ajax({
            url: 'server',
            data: formData,
            type: 'POST',
            dataType: 'JSON',
            processData: false,
            contentType: false,
            success: function (response) {
                if (response) {
                    location.reload();
                }
            },
            error: function (_, _, error) {
                console.error(error);
            }
        });
    }
})