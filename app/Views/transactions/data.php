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
        <div class="row">
            <div class="col-md-4">
                <h3 class="card-title">
                    <button type="button" class="btn btn-warning btn-sm mb-3"
                        onclick="window.location='<?= site_url('transactions') ?>'"><i class="fa fa-backward"></i> Kembali</button>
                    <br/>
                    <i class="fa fa-file-invoice"></i> Transactions
                </h3>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="startDate">Starting Date</label>
                    <input type="date" id="startDate" class="form-control" value="<?= date('Y-m-d'); ?>">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="endDate">Ending Date</label>
                    <input type="date" id="endDate" class="form-control">
                </div>
            </div>
            <div class="col-md-2 d-flex justify-content-end">
                <div class="card-tools d-flex">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
        <?php
        $userLevel = session()->get('level_info');
        ?>
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
                    <th>Payment Method</th>
                    <?php
                    if ($userLevel == 'Admin' || $userLevel == 'Manager') {
                        echo "<th>#</th>";
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <button class="btn btn-success" id="printBtn">Print Csv</button>
    </div>
</div>
<div id="viewmodal" style="display: none;"></div>
<script>
    $(document).ready(function() {
        showTransactionsData();
    });

    $(document).ready(function() {
        $('#printBtn').click(function(e) {
            e.preventDefault();

            var startDate = $('#startDate').val();
            var endDate = $('#endDate').val();

            window.location.href = "<?= site_url('transactions/exportToCSV') ?>?startDate=" + startDate + "&endDate=" + endDate;
        });
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
                "type": "POST",
                "data": function(d) {
                    var startDate = $('#startDate').val();
                    var endDate = $('#endDate').val();
                    d.startDate = startDate;
                    d.endDate = endDate;
                }
            },
            "columnDefs": [{
                "targets": [0, 2],
                "orderable": false,
            }],
        });

        $('#startDate').on('change', function() {
            table.ajax.reload();
        });
        $('#endDate').on('change', function() {
            table.ajax.reload();
        });
    }

    function deleteData(invoice) {
        Swal.fire({
            title: "Delete this transactions?",
            html: `Are you sure want to delete this data`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes!",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "post",
                    url: "<?= site_url('transactions/delete') ?>",
                    data: {
                        invoice: invoice
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: "Success!",
                                text: response.success,
                                icon: "success"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.reload();
                                }
                            });
                        }
                    }
                });
            }
        });
    }
</script>
<?= $this->endSection() ?>