<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | Log in (v2)</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('assets') ?>/plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="<?= base_url('assets') ?>/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url('assets') ?>/dist/css/adminlte.min.css?v=3.2.0">
    <script src="<?= base_url('assets') ?>/plugins/jquery/jquery.min.js"></script>
    <link rel="stylesheet" href="<?= base_url('assets') ?>/plugins/sweetalert2/sweetalert2.min.css">
    <script src="<?= base_url('assets') ?>/plugins/sweetalert2/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" integrity="sha512-dPXYcDub/aeb08c63jRq/k6GaKccl256JQy/AnOq7CAnEZ9FzSL9wSbcZkMp4R26vBsMLFYH4kQ67/bbV8XaCQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="<?= base_url('assets') ?>/index2.html" class="h1"><b>Ardi</b> - Mart</a>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Sign in to start your session</p>

                <?= form_open('', ['id' => 'formAuth']) ?>
                <?= csrf_field() ?>
                <div style="height: 70px;">
                    <div class="input-group mb-3">
                        <input type="text" name="username" id="username" class="form-control" placeholder="username">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <i class="fa fa-user"></i>
                            </div>
                        </div>
                        <div id="errorUsername" class="invalid-feedback" style="display: none;">
                        </div>
                        <div class="valid-feedback" style="display: none;">
                        </div>
                    </div>
                </div>
                <div style="height: 70px;">
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                        <div class="input-group-append">
                        </div>
                        <button type="button" class="btn btn-outline-secondary" id="togglePassword" style="border-radius: 0 5px 5px 0;">
                            <i class="bi bi-eye-slash"></i>
                        </button>
                        <div id="errorPassword" class="invalid-feedback" style="display: none;"></div>
                        <div class="valid-feedback" style="display: none;"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" id="rememberMe" value="1" name="rememberMe">
                            <label for="rememberMe">
                                Remember Me
                            </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-4">
                        <button type="submit" id="login" class="btn btn-primary btn-block">Login</button>
                    </div>
                    <!-- /.col -->
                </div>
                <?= form_close() ?>
                <p class="mb-1">
                    <a href="forgot-password.html">I forgot my password</a>
                </p>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.login-box -->

    <!-- jQuery -->
    <script src="<?= base_url('assets') ?>/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="<?= base_url('assets') ?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="<?= base_url('assets') ?>/dist/js/adminlte.min.js?v=3.2.0"></script>
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
                    $('#login').html('Login')
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
                                if (result.isConfirmed) {}
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