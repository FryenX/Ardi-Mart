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
        <div class="card-tools d-flex">
            <input type="date" id="calendarDate" class="form-control" placeholder="Select Date" value="<?= date('Y-m-d'); ?>">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                <i class="fas fa-times"></i>
            </button>
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

            // Get the selected date value from the input field
            var selectedDate = $('#calendarDate').val();

            // Redirect to the export URL with the selected date as a query parameter
            window.location.href = "<?= site_url('transactions/exportToCSV') ?>?selectedDate=" + selectedDate;
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
                    var selectedDate = $('#calendarDate').val();
                    d.date = selectedDate;
                }
            },
            "columnDefs": [{
                "targets": [0, 2],
                "orderable": false,
            }],
        });

        $('#calendarDate').on('change', function() {
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