<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= lang('Errors.pageNotFound') ?></title>

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
</head>

<body class="hold-transition sidebar-mini">
    <!-- Site wrapper -->
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Navbar Search -->

                <!-- Messages Dropdown Menu -->

                <!-- Notifications Dropdown Menu -->
                <li class="nav-item">
                    <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="<?= base_url('/') ?>" class="brand-link">
                <img src="<?= base_url('assets') ?>/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">Ardi Mart</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user (optional) -->
                <div class="user-panel mt-3 mb-3 pb-3 d-flex align-items-center" style="display: flex; align-items: center;">
                    <div class="image" style="margin-right: 15px;">
                        <?php
                        $userImage = session()->get('image');

                        $filePath = FCPATH . $userImage;

                        if ($userImage && file_exists($filePath)) {
                            $imageSrc = base_url($userImage);
                        } else {
                            $imageSrc = base_url('assets/upload/users/default/default_user.png');
                        }
                        ?>

                        <img src="<?= esc($imageSrc) ?>" class="img-circle elevation-2" style="object-fit: cover; width: 35px; height: 35px" alt="User Image">
                    </div>
                    <div class="info">
                        <?php
                        $userName = session()->get('name');
                        ?>
                        <a href="#" class="d-block" style="font-size: 26px; font-weight: 900; line-height: 22px;"><?= $userName ?></a>
                    </div>
                </div>

                <!-- SidebarSearch Form -->

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <?php
                        $userLevel = session()->get('level_info');
                        ?>
                        <?php
                        if ($userLevel == 'Admin' || $userLevel == 'Manager') {
                            echo "<li class='nav-item'>
                                <a href='" . site_url('/') . "' class='nav-link'>
                                    <i class='nav-icon fa fa-tachometer-alt'></i>
                                        <p>
                                            Dashboard
                                        </p>
                                    </a>
                                </li>";
                        }
                        ?>
                        <?php
                        if ($userLevel == 'Admin' || $userLevel == 'Manager') {
                            echo "<li class='nav-header'>Master</li>";
                        }
                        ?>

                        <?php
                        if ($userLevel == 'Admin' || $userLevel == 'Manager') {
                            echo "<li class='nav-item'>
                                <a href='" . site_url('users') . "' class='nav-link'>
                                    <i class='nav-icon fa fa-users'></i>
                                    <p>
                                        Users
                                    </p>
                                </a>
                            </li>";
                        }
                        ?>
                        <?php
                        if ($userLevel == 'Admin' || $userLevel == 'Manager') {
                            echo "<li class='nav-item'>
                                <a href='" . site_url('categories') . "' class='nav-link'>
                                    <i class='nav-icon fa fa-list'></i>
                                    <p>
                                        Categories
                                    </p>
                                </a>
                            </li>";
                        }
                        ?>
                        <?php
                        if ($userLevel == 'Admin' || $userLevel == 'Manager') {
                            echo "<li class='nav-item'>
                                <a href='" . site_url('units') . "' class='nav-link'>
                                    <i class='nav-icon fa fa-list'></i>
                                    <p>
                                        Units
                                    </p>
                                </a>
                            </li>";
                        }
                        ?>
                        <?php
                        if ($userLevel == 'Admin' || $userLevel == 'Manager') {
                            echo "<li class='nav-item'>
                                <a href='" . site_url('products') . "' class='nav-link'>
                                    <i class='nav-icon fa fa-table'></i>
                                    <p>
                                        Products
                                    </p>
                                </a>
                            </li>";
                        }
                        ?>
                        <?php
                        if ($userLevel == 'Admin') {
                            echo "<li class='nav-item'>
                                <a href='" . site_url('levels') . "' class='nav-link'>
                                <i class='nav-icon fa fa-share'></i> 
                                    <p>
                                        Levels
                                    </p>
                                </a>
                            </li>";
                        }
                        ?>
                        <li class="nav-header">Transaction</li>
                        <li class="nav-item">
                            <a href="<?= site_url('transactions') ?>" class="nav-link">
                                <i class="nav-icon fa fa-table"></i>
                                <p>
                                    Sale
                                </p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

            <div class="p-2" style="position: absolute; bottom: 5px; width: 100%;">
                <form action="<?= site_url('login/logout') ?>" method="post">
                    <button type="submit" class="btn btn-danger" style="width: 100%; font-weight: bold;">Log Out</button>
                </form>
            </div>
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1></h1>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">

                <section class="content">
                    <div class="error-page">
                        <h2 class="headline text-warning"> 404</h2>

                        <div class="error-content">
                            <h3><i class="fas fa-exclamation-triangle text-warning"></i> Oops! <br /> <?php if (ENVIRONMENT !== 'production') : ?>
                                    <?= nl2br(esc($message)) ?>
                                <?php else : ?> <?= lang('Errors.sorryCannotFind') ?>
                                <?php endif; ?></h3>

                            <p>
                                We could not find the page you were looking for.
                                Meanwhile, you may <a href="<?= base_url('/') ?>">return to dashboard</a> or try using the search form.
                            </p>

                            <form action="" id="search" method="get">
                                <div class="input-group">
                                    <input type="text" id="search-input" name="search" class="form-control" placeholder="Search">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-danger"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>

            </section>
        </div>

        <footer class="main-footer">
            <div class="float-right d-none d-sm-block">
                <b>Version</b> 3.1.0
            </div>
            <strong>Copyright &copy; 2024 <a href="https://github.com/FryenX">Ardi Widana</a>.</strong> All rights reserved.
        </footer>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script>
        $(document).ready(function() {
            $('#search').submit(function(e) {
                e.preventDefault();

                var searchValue = $('#search-input').val();

                var url = "<?= base_url() ?>/" + searchValue

                window.location.href = url;
            });
        });
    </script>
    <!-- Bootstrap 4 -->
    <script src="<?= base_url('assets') ?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="<?= base_url('assets') ?>/dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="<?= base_url('assets') ?>/dist/js/demo.js"></script>
    <!-- Code injected by live-server -->
</body>

</html>