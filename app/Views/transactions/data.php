<?= $this->extend('templates/menu'); ?>

<?= $this->section('title'); ?>
<h3><i class="fa fa-table"></i> Data Transactions</h3>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url('assets') ?>/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url('assets') ?>/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<script src="<?= base_url('assets') ?>/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url('assets') ?>/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?= base_url('assets') ?>/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?= base_url('assets') ?>/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fa fa-file-invoice"></i> Transactions
        </h3>

        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <table id="transactionsData" class="table table-bordered table-hover display">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Invoice</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Discount <i>(%)</i></th>
                    <th>Discount <i>(IDR)</i></th>
                    <th>Gross Total</th>
                    <th>Net Total</th>
                    <th>Payment Amount</th>
                    <th>Change</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
<div id="viewmodal" style="display: none;"></div>
<script>
    $(document).ready(function() {
        showTransactionsData();
    });

    function showTransactionsData() {
        var table = $('#transactionsData').DataTable({
            "processing": true,
            "serverSide": true,
            "autoWidth": false,
            "responsive": true,
            "order": [],
            "ajax": {
                "url": "<?= site_url('transactions/showTransactionsData') ?>",
                "type": "POST"
            },
            "columnDefs": [{
                "targets": [0, 2],
                "orderable": false,
            }, ],
        });
    }
</script>
<?= $this->endSection() ?>