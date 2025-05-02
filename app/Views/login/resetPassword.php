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
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <button type="button" class="btn btn-warning" onclick="redirectTo()">
                                    <i class="fa fa-backward"></i> Back
                                </button>
                                <h3 class="mb-0 text-center" style="font-weight: 900; font-size: 2rem;">Change Password</h3>
                            </div>
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?= $error; ?></div>
                            <?php else: ?>
                                <?= form_open('', ['id' => 'formChange']) ?>
                                <?= csrf_field() ?>
                                <input type="hidden" name="uuid" value="<?= $uuid ?>" id="uuid" readonly>
                                <input type="hidden" name="token" value="<?= $token ?>" id="token" readonly>
                                <div style="height: 70px;">
                                    <div class="form-outline mb-4 d-flex">
                                        <div class="input-group">
                                            <input type="password" name="newPassword" id="newPassword" class="form-control" placeholder="Enter Your New Password">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-default" id="toggleNewPassword" style="border-radius: 0 5px 5px 0;">
                                                    <i class="bi bi-eye-slash"></i>
                                                </button>
                                            </div>
                                            <div id="errorNewPassword" class="invalid-feedback" style="display: none;">
                                            </div>
                                            <div class="valid-feedback" style="display: none;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style="height: 70px;">
                                    <div class="form-outline mb-4 d-flex">
                                        <div class="input-group">
                                            <input type="password" name="confirmPassword" id="confirmPassword" class="form-control" placeholder="Please Confirm Your Password">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-default" id="toggleConfirmPassword" style="border-radius: 0 5px 5px 0;">
                                                    <i class="bi bi-eye-slash"></i>
                                                </button>
                                            </div>
                                            <div id="errorConfirmPassword" class="invalid-feedback" style="display: none;">
                                            </div>
                                            <div class="valid-feedback" style="display: none;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-primary btn-lg btn-block" id="change" type="button">Change</button>
                                <?= form_close() ?>
                            <?php endif ?>
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
        $('#change').click(function(e) {
            e.preventDefault();

            let form = $('#formChange')[0];

            let data = new FormData(form);

            $.ajax({
                type: "post",
                url: "<?= site_url('login/newPassword') ?>",
                data: data,
                dataType: "json",
                enctype: 'multipart/form-data',
                processData: false,
                contentType: false,
                cache: false,
                beforeSend: function() {
                    $('#change').prop('disabled', true)
                    $('#change').html('<i class="fa fa-spin fa-spinner"></i>')
                },
                complete: function() {
                    $('#change').prop('disabled', false)
                    $('#change').html('Change')
                },
                success: function(response) {
                    if (response.error) {
                        let dataError = response.error;
                        if (dataError.errorNewPassword) {
                            $('#errorNewPassword').html(dataError.errorNewPassword).show();
                            $('#newPassword').addClass('is-invalid');
                        } else {
                            $('#errorNewPassword').fadeOut();
                            $('#newPassword').removeClass('is-invalid').addClass('is-valid');
                        }
                        if (dataError.errorConfirmPassword) {
                            $('#errorConfirmPassword').html(dataError.errorConfirmPassword).show();
                            $('#confirmPassword').addClass('is-invalid');
                        } else {
                            $('#errorConfirmPassword').fadeOut();
                            $('#confirmPassword').removeClass('is-invalid').addClass('is-valid');
                        }
                    } else {
                        if (response.success) {
                            Swal.fire({
                                icon: "success",
                                title: "Success!",
                                html: response.success
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location = '<?= site_url('users') ?>';
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Oops...",
                                html: response.failed,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $('#oldPassword').select();
                                }
                            });
                        }
                    }
                },
                error: function(xhr, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            });
        })

        document.getElementById('toggleNewPassword').addEventListener('click', function() {
            var passwordField = document.getElementById('newPassword');
            var passwordFieldType = passwordField.type;

            if (passwordFieldType === 'password') {
                passwordField.type = 'text';
                this.innerHTML = '<i class="bi bi-eye"></i>';
            } else {
                passwordField.type = 'password';
                this.innerHTML = '<i class="bi bi-eye-slash"></i>';
            }
        });

        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            var passwordField = document.getElementById('confirmPassword');
            var passwordFieldType = passwordField.type;

            if (passwordFieldType === 'password') {
                passwordField.type = 'text';
                this.innerHTML = '<i class="bi bi-eye"></i>';
            } else {
                passwordField.type = 'password';
                this.innerHTML = '<i class="bi bi-eye-slash"></i>';
            }
        });

        function redirectTo() {
            const uuid = '<?= esc(session()->get('uuid')) ?>'; // Get the uuid from the session

            if (uuid) {
                // If the session exists, redirect to the profile page with uuid
                window.location = '<?= site_url('profile/') ?>' + uuid;
            } else {
                // If session does not exist, redirect to the login page
                window.location = '<?= site_url('login') ?>';
            }
        }
    </script>
</body>

</html>