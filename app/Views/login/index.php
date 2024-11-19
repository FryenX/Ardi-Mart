<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | Blank Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('assets') ?>/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url('assets') ?>/dist/css/adminlte.min.css">

    <script src="<?= base_url('assets') ?>/plugins/jquery/jquery.min.js"></script>

    <link rel="stylesheet" href="<?= base_url('assets') ?>/plugins/sweetalert2/sweetalert2.min.css">
    <script src="<?= base_url('assets') ?>/plugins/sweetalert2/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="<?= base_url('assets') ?>/plugins/jsgrid/jsgrid.min.css">
    <link rel="stylesheet" href="<?= base_url('assets') ?>/plugins/jsgrid/jsgrid-theme.min.css">
    <link rel="stylesheet" href="<?= base_url('assets') ?>/plugins/login.css">
</head>

<body>
    <section class="vh-100">
        <div class="container-fluid h-custom">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-md-9 col-lg-6 col-xl-5">
                    <img src="<?= base_url('assets') ?>/upload/login/6514045.webp"
                        class="img-fluid" alt="Sample image">
                </div>
                <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                    <div class="d-flex flex-row align-items-center justify-content-center">
                        <h1 class="lead heading mb-5 me-3">LOGIN</h1>
                    </div>

                    <?= form_open('', ['id' => 'formAuth']) ?>
                    <?= csrf_field() ?>
                    <!-- Email input -->
                    <div data-mdb-input-init class="form-outline" style="height: 100px;">
                        <label class="form-label" for="username">Username</label>
                        <input type="text" id="username" name="username" class="form-control form-control-lg"
                            placeholder="Enter a valid username address" />
                        <div id="errorUsername" class="invalid-feedback" style="display: none;">
                        </div>
                        <div class="valid-feedback" style="display: none;">
                        </div>
                    </div>

                    <!-- Password input -->
                    <div class="form-outline mb-3" style="height: 120px;">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-group">
                            <input type="password" placeholder="Enter Your Password" class="form-control form-control-lg" id="password" name="password">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary" id="togglePassword" style="border-radius: 0 5px 5px 0;">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                            <div id="errorPassword" class="invalid-feedback" style="display: none;"></div>
                            <div class="valid-feedback" style="display: none;"></div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <!-- Checkbox -->
                        <div class="form-check mb-0">
                            <input class="form-check-input me-2 remember-me" type="checkbox" value="1" name="rememberMe" id="remember" />
                            <label class="form-check-labe remember-me" for="remember">
                                Remember me
                            </label>
                        </div>
                        <a href="#!" class="text-body forgot">Forgot password?</a>
                    </div>
                    <div class="text-center text-lg-start mt-4 pt-2">
                        <button type="submit" class="btn btn-primary btn-lg"
                            style="padding-left: 2.5rem; padding-right: 2.5rem;" id="login">
                            Login</button>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
        <div
            class="d-flex flex-column flex-md-row text-center text-md-start justify-content-between py-4 px-4 px-xl-5 bg-primary">
            <!-- Copyright -->
            <div class="text-white mb-3 mb-md-0">
                Copyright © 2020. All rights reserved.
            </div>
            <!-- Copyright -->
        </div>
    </section>

    <script>
        $('#login').click(function(e) {
            e.preventDefault();

            let form = $('#formAuth')[0];
            let data = new FormData(form);

            $.ajax({
                type: "post",
                url: "<?= site_url('login/auth') ?>",
                data: data,
                dataType: "json",
                processData: false,
                contentType: false,
                cache: false,
                beforeSend: function() {
                    $('#login').prop('disabled', true)
                    $('#login').html('<i class="fa fa-spin fa-spinner"></i>')
                },
                complete: function() {
                    $('#login').prop('disabled', false)
                    $('#login').html('Save')
                },
                success: function(response) {
                    if (response.error) {
                        let dataError = response.error;
                        if (dataError.errorUserName) {
                            $('#errorUsername').html(dataError.errorUserName).show();
                            $('#username').addClass('is-invalid');
                        } else {
                            $('#errorUsername').fadeOut();
                            $('#username').removeClass('is-invalid').addClass('is-valid');
                        }
                        if (dataError.errorPassword) {
                            $('#errorPassword').html(dataError.errorPassword).show();
                            $('#password').addClass('is-invalid');
                        } else {
                            $('#errorPassword').fadeOut();
                            $('#password').removeClass('is-invalid').addClass('is-valid');
                        }
                    } else {
                        if (response.success) {
                            Swal.fire({
                                icon: "success",
                                title: "Success!",
                                html: response.success
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location = '<?= site_url('/') ?>'
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Oops...",
                                html: response.failed,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location = '<?= site_url('login') ?>'
                                }
                            });
                        }

                    }
                },
                error: function(xhr, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            });
        });

        document.getElementById('togglePassword').addEventListener('click', function() {
            var passwordField = document.getElementById('password');
            var passwordFieldType = passwordField.type;

            if (passwordFieldType === 'password') {
                passwordField.type = 'text';
                this.innerHTML = '<i class="bi bi-eye"></i>';
            } else {
                passwordField.type = 'password';
                this.innerHTML = '<i class="bi bi-eye-slash"></i>';
            }
        });
    </script>
</body>

</html>