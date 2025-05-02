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
                            <div style="height: 60px;">
                                <div class="form-outline mb-4 d-flex">
                                    We've been sending a reset token to your email please check your email
                                    by clicking on link below
                                </div>
                            </div>
                            <div class="justify-content-between d-flex">
                                <div class="form-check d-flex justify-content-end ">
                                </div>
                                <div class="mb-3">
                                </div>
                            </div>
                            <a href="https://mail.google.com" target="_blank">
                                <button class="btn btn-primary btn-lg btn-block" id="login">Open Gmail</button>
                            </a>
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