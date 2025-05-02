<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ardi Mart</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('assets') ?>/plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="<?= base_url('assets') ?>/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url('assets') ?>/dist/css/adminlte.min.css?v=3.2.0">
    <link rel="stylesheet" href="<?= base_url('assets') ?>/plugins/sweetalert2/sweetalert2.min.css">
    <script src="<?= base_url('assets') ?>/plugins/sweetalert2/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" integrity="sha512-dPXYcDub/aeb08c63jRq/k6GaKccl256JQy/AnOq7CAnEZ9FzSL9wSbcZkMp4R26vBsMLFYH4kQ67/bbV8XaCQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <section class="vh-100" style="background-color: #17a2b8;">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card shadow-2-strong" style="border-radius: 1rem;">
                        <div class="card-body p-5">
                            <h3 class="mb-5 text-center" style="font-weight: 900; font-size: 3.2rem;">Login</h3>
                            <?= form_open('', ['id' => 'formAuth']) ?>
                            <?= csrf_field() ?>
                            <div style="height: 70px;">
                                <div class="form-outline mb-4 d-flex">
                                    <div class="input-group">
                                        <input type="text" name="username" id="username" class="form-control" placeholder="Username">
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
                            </div>
                            <div style="height: 70px;">
                                <div class="form-outline mb-4 d-flex">
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-default" id="togglePassword" style="border-radius: 0 5px 5px 0;">
                                                <i class="bi bi-eye-slash"></i>
                                            </button>
                                        </div>
                                        <div id="errorPassword" class="invalid-feedback" style="display: none;">
                                        </div>
                                        <div class="valid-feedback" style="display: none;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Checkbox -->
                            <div class="justify-content-between d-flex">
                                <div class="form-check d-flex justify-content-start mb-4">
                                    <input class="form-check-input" type="checkbox" value="1" id="rememberMe" name="rememberMe" />
                                    <label class="form-check-label" for="rememberMe"> Remember Me</label>
                                </div>
                                <div>
                                    <a href="<?= base_url('login/credential') ?>">Change Password?</a>
                                </div>
                            </div>
                            <button class="btn btn-primary btn-lg btn-block" id="login" type="submit">Login</button>
                            <?= form_close() ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
                            $('#password').removeClass('is-invalid');
                        }
                    } else {
                        if (response.success) {
                            window.location = '<?= site_url('/') ?>'
                        } else {
                            Swal.fire({
                                toast: true,
                                position: 'center',
                                icon: 'error',
                                title: response.failed,
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                background: '#f8d7da',
                                iconColor: '#dc3545'
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