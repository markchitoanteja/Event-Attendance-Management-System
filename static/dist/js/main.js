jQuery(document).ready(function () {
    if (notification) {
        Swal.fire({
            title: notification.title,
            text: notification.text,
            icon: notification.icon
        });
    }

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
                    $("#account_settings_image_display").attr("src", "static/uploads/admin/" + response.image);

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
            displayImage.attr('src', base_url + "public/dist/img/uploads/admin/" + $("#account_settings_old_image").val());
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
})