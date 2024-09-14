jQuery(document).ready(function () {
    if (notification) {
        Swal.fire({
            title: notification.title,
            text: notification.text,
            icon: notification.icon
        });
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
                    $("#account_settings_image_display").attr("src", "../static/uploads/admin/" + response.image);

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
            displayImage.attr('src', "../static/uploads/admin/" + $("#account_settings_old_image").val());
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
            displayImage.attr('src', "../static/uploads/admin/default-user-image.png");
        }
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

        console.log(attendee_id);

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
})