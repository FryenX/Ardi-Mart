<?= $this->extend('templates/menu'); ?>

<?= $this->section('title'); ?>
<h3><i class="fa fa-table"></i> Cashier Input</h3>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<script src="<?= base_url('assets/plugins/autoNumeric.js') ?>"></script>
<div class="card card-default color-palette-box">
    <div class="card-header">
        <h3 class="card-title">
            <button type="button" class="btn btn-warning btn-sm text-white"
                onclick="window.location='<?= site_url('transactions') ?>'"><i class="fa fa-backward"></i> Kembali</button>
        </h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="invoice">Invoice</label>
                    <input type="text" class="form-control form-control-sm" style="color:red;font-weight:bold;"
                        name="invoice" id="invoice" readonly>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" class="form-control form-control-sm" name="date" id="date" readonly
                        value="<?= date('Y-m-d'); ?>">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="customer">Customer</label>
                    <div class="input-group mb-3">
                        <input type="text" value="-" class="form-control form-control-sm" name="customer" id="customer"
                            readonly>
                        <input type="hidden" name="customer_id" id="customer_id" value="0">
                        <div class="input-group-append">
                            <button class="btn btn-sm btn-primary" type="button">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Action</label>
                    <div class="input-group">
                        <button class="btn btn-danger" type="button" id="deleteTransactions">
                            <i class="fa fa-trash-alt"></i>
                        </button>&nbsp;
                        <button class="btn btn-success" type="button" id="saveTransactions">
                            <i class="fa fa-save"></i>
                        </button>&nbsp;
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="barcode">Product Barcode</label>
                    <input type="text" class="form-control form-control-sm" name="barcode" id="barcode"
                        autofocus>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="product">Product</label>
                    <input type="text" style="font-weight: bold;" class="form-control form-control-sm" name="product" id="product"
                        readonly>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="qty">Quantity</label>
                    <input type="number" class="form-control form-control-sm" name="qty" id="qty" value="1" min="1">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="total">Total</label>
                    <input type="text" class="form-control form-control-lg" name="total" id="total"
                        style="text-align: right; color:blue; font-weight : bold; font-size:30pt;" value="0" readonly>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12" id="detailTransactionsData">

            </div>
        </div>
    </div>
</div>
<div id="viewModal" style="display: none;"></div>
<div id="viewModalPayment" style="display: none;"></div>

<script>
    $(document).ready(function() {
        createInvoice();
        sumTotal();
        detailTransactionsData();

        $('#barcode').keydown(function(e) {
            if (e.keyCode == 13) {
                e.preventDefault();
                checkCode();
                $('#qty').focus().select();
            }
        });

        $('#qty').keydown(function(e) {
            if (e.keyCode == 13) {
                e.preventDefault();
                checkCode();
            }
        });

        $('#qty').autoNumeric('init', {
            aSep: ',',
            aDec: '.',
            mDec: '2',
            vMin: '1',
        });
    })

    function createInvoice() {
        $.ajax({
            type: "post",
            url: "<?= site_url('transactions/createInvoice') ?>",
            data: {
                date: $('#date').val()
            },
            dataType: "json",
            success: function (response) {
                if(response.invoice) {
                    $('#invoice').val(response.invoice);
                }
            },
            error: function(xhr, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        });
    }

    function detailTransactionsData() {
        $.ajax({
            type: "post",
            url: "<?= site_url('transactions/dataDetail') ?>",
            data: {
                noInvoice: $('#invoice').val()
            },
            dataType: "json",
            beforeSend: function() {
                $('#detailTransactionsData').html('<i class="fa fa-spin fa-spinner"></i>')
            },
            success: function(response) {
                if (response.data) {
                    $('#detailTransactionsData').html(
                        response.data
                    );
                }
            },
            error: function(xhr, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        });
    }

    function checkCode() {
        let barcode = $('#barcode').val();

        if (barcode.length == 0) {
            $.ajax({
                url: "<?= site_url('transactions/viewProductData') ?>",
                dataType: "json",
                success: function(response) {
                    $('#viewModal').html(response.modal).show();
                    $('#productModal').modal('show');
                },
                error: function(xhr, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            });
        } else {
            $.ajax({
                type: "post",
                url: "<?= site_url('transactions/saveTemp') ?>",
                data: {
                    barcode: barcode,
                    name: $('#product').val(),
                    qty: $('#qty').val(),
                    invoice: $('#invoice').val()
                },
                dataType: "json",
                success: function(response) {
                    if (response.Data == 'Many') {
                        $.ajax({
                            url: "<?= site_url('transactions/viewProductData') ?>",
                            dataType: "json",
                            data: {
                                keyword: barcode
                            },
                            type: "post",
                            success: function(response) {
                                $('#viewModal').html(response.modal).show();
                                $('#productModal').modal('show');
                            },
                            error: function(xhr, thrownError) {
                                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                            }
                        });
                    }
                    if (response.success == 'Success') {
                        detailTransactionsData();
                        empty();
                    }
                    if (response.error) {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            html: response.error
                        });
                        detailTransactionsData();
                        empty();
                    }
                },
                error: function(xhr, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            });
        }
    }

    function empty() {
        $('#barcode').val('');
        $('#product').val('');
        $('#qty').val('1');
        $('#barcode').focus();

        sumTotal();
    }

    function sumTotal() {
        $.ajax({
            url: "<?= site_url('transactions/sumTotal') ?>",
            dataType: "json",
            data: {
                invoice: $('#invoice').val()
            },
            type: "post",
            success: function(response) {
                if (response.total) {
                    $('#total').val(response.total);
                }
            },
            error: function(xhr, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        });
    }

    function cancelTransactions() {
        let invoice = $('#invoice').val();
        Swal.fire({
            title: "Delete this Transaction?",
            html: `Are you sure want to delete <strong>${invoice}</strong>`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "post",
                    url: "<?= site_url('transactions/cancel') ?>",
                    data: {
                        invoice: invoice
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.success == 'Success') {
                            Swal.fire({
                                title: "Success!",
                                html: response.success,
                                icon: "success"
                            }).then((result) => {
                                window.location.reload();
                            });
                        }
                    },
                    error: function(xhr, thrownError) {
                        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                });
            }
        });
    }

    $('#deleteTransactions').click(function(e) {
        e.preventDefault();
        cancelTransactions();
    });

    $('#saveTransactions').click(function(e) {
        e.preventDefault();
        payment();
    });

    $('#qty').click(function(e) {
        if (e.keyCode == 27) {
            e.preventDefault();
            $('#barcode').focus();
        }
    });

    $(this).keydown(function(e) {
        if (e.keyCode == 27) {
            e.preventDefault();
            $('#barcode').focus();
        }
        if (e.keyCode == 115) {
            e.preventDefault();
            cancelTransactions();
        }
        if (e.keyCode == 119) {
            e.preventDefault();
            payment();
        }
    });

    function payment() {
        let invoice = $('#invoice').val();
        $.ajax({
            type: "post",
            url: "<?= site_url('transactions/payment') ?>",
            data: {
                invoice: invoice,
                date: $('#date').val(),
                customer: $('#customer').val()
            },
            dataType: "json",
            success: function(response) {
                if (response.error) {
                    Swal.fire({
                        icon: "warning",
                        title: "Error",
                        html: response.error
                    });
                } else if (response.data) {
                    $('#viewModalPayment').html(response.data).show();
                    $('#modalPayment').on('shown.bs.modal', function(event) {
                        $('#payment').focus();
                    })
                    $('#modalPayment').modal('show')
                }
            },
            error: function(xhr, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        });
    }
</script>

<?= $this->endSection() ?>