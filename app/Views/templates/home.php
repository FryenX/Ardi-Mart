<?= $this->extend('templates/menu') ?>

<?= $this->section('content') ?>
<div class="alert alert-info alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <h5><i class="icon fas fa-info"></i>Welcome!</h5>
    This is Dasboard
</div>
<?= $this->endSection() ?>