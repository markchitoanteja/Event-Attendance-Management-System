jQuery(document).ready(function () {
    $("#login_form").submit(function () {
        const username = $("#login_username").val();
        const password = $("#login_password").val();
        const remember_me = $("#login_remember_me").prop("checked");

        $("#login_notification").addClass("d-none");
        $("#notification").addClass("d-none");
        $("#login_submit").attr("disabled", true);
        $("#login_submit").text("Please wait...");

        var formData = new FormData();

        formData.append('login', true);
        formData.append('username', username);
        formData.append('password', password);
        formData.append('remember_me', remember_me);

        $.ajax({
            url: 'server',
            data: formData,
            type: 'POST',
            dataType: 'JSON',
            processData: false,
            contentType: false,
            success: function (response) {
                if (!response) {
                    $("#login_notification").removeClass("d-none");

                    $("#login_submit").removeAttr("disabled");
                    $("#login_submit").text("Login");
                } else {
                    location.reload();
                }
            },
            error: function (_, _, error) {
                console.error(error);
            }
        });
    })
})