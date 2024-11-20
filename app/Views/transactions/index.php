<?= $this->extend('templates/menu'); ?>

<?= $this->section('title'); ?>
<h3><i class="fa fa-table"></i> Transactions</h3>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-3 col-12">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>Input</h3>

                <p>Cashier</p>
            </div>
            <div class="icon">
                <i class="fa fa-tasks"></i>
            </div>
            <a href="<?= site_url('transactions/input') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>Data</h3>

                <p>Transactions</p>
            </div>
            <div class="icon">
                <i class="fa fa-shopping-cart"></i>
            </div>
            <a href="<?= site_url('transactions/data') ?>" class="small-box-footer">Data Transactions <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>