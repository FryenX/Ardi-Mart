<?= $this->extend('templates/main') ?>

<?= $this->section('menu') ?>

<?php
$userLevel = session()->get('level_info');
?>

<li class="nav-item">
    <a href="<?= site_url('/') ?>" class="nav-link">
        <i class="nav-icon fa fa-tachometer-alt"></i>
        <p>
            Dashboard
        </p>
    </a>
</li>
<li class="nav-header">Master</li>
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
<li class="nav-item">
    <a href="<?= site_url('categories') ?>" class="nav-link">
        <i class="nav-icon fa fa-list"></i>
        <p>
            Categories
        </p>
    </a>
</li>
<li class="nav-item">
    <a href="<?= site_url('units') ?>" class="nav-link">
        <i class="nav-icon fa fa-list"></i>
        <p>
            Units
        </p>
    </a>
</li>
<li class="nav-item">
    <a href="<?= site_url('products') ?>" class="nav-link">
        <i class="nav-icon fa fa-table"></i>
        <p>
            Products
        </p>
    </a>
</li>
<?php
if ($userLevel == 'Admin' || $userLevel == 'Manager') {
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
<?= $this->endSection() ?>