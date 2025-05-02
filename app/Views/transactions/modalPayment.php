<script src="<?= base_url('assets/plugins/autoNumeric.js') ?>"></script>
<div class="modal fade" id="modalPayment" tabindex="-1" aria-labelledby="modalPaymentLabel" data-backdrop="static" data-keyboard="false" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPaymentLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?= form_open('transactions/saveData', ['id' => 'formPayment']) ?>
            <div class="modal-body">
                <input type="hidden" name="invoice" value="<?= $invoice ?>">
                <input type="hidden" name="customer" value="<?= $customer ?>">
                <input type="hidden" name="gross_total" id="gross_total" value="<?= $net_total ?>">
                <input type="hidden" name="invoiceDate" id="invoiceDate" value="<?= $invoiceDate ?>">
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="disc_percent">Disc(%)</label>
                            <input type="text" name="disc_percent" id="disc_percent" class="form-control">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="dis_idr">Disc(IDR)</label>
                            <input type="text" name="disc_idr" id="disc_idr" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="net_total">Total Payment</label>
                    <input type="text" class="form-control form-control-lg" name="net_total" id="net_total"
                        style="text-align: right; color:blue; font-weight : bold; font-size:30pt;" value="<?= $net_total ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Payment</label>
                    <div class="btn-group d-flex mb-3">
                        <button type="button" id="btnCash" class="btn btn-outline-primary flex-fill active">Cash</button>
                        <button type="button" id="btnTransfer" class="btn btn-outline-success flex-fill">Transfer</button>
                    </div>

                    <div id="cash-form" style="display: block;">
                        <label for="cash_amount">Enter Cash Amount</label>
                        <input type="text" class="form-control form-control-lg"
                            name="cash_amount" id="cash_amount"
                            style="text-align: right; color:red; font-weight: bold; font-size: 26pt;"
                            autocomplete="off">
                    </div>
                </div>
                <div class="form-group" id="change-wrapper">
                    <label for="change">Change</label>
                    <input type="text" class="form-control form-control-lg" name="change" id="change"
                        style="text-align: right; color:blue; font-weight : bold; font-size:30;" readonly autocomplete="off">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnCancel" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" id="btnSave" class="btn btn-success">Save</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>
<script>
    function paymentGateway() {
        $.ajax({
            type: "post",
            url: "<?= site_url('transactions/paymentTransfer') ?>",
            data: $('#formPayment').serialize(),
            dataType: "json",
            success: function(response) {
                if (response.error) {
                    Swal.fire({
                        icon: "warning",
                        title: "Error",
                        html: response.error
                    });
                } else {
                    snap.pay(response.snapToken, {
                        onSuccess: function(result) {
                            processTransaction(result, 'success', response);
                        },
                        onPending: function(result) {
                            processTransaction(result, 'pending', response);
                        },
                        onError: function(result) {
                            processTransaction(result, 'error', response);
                        }
                    });
                }
            },
            error: function(xhr, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        });
    }

    function processTransaction(dataObj, status, serverResponse) {
        console.log(JSON.stringify(dataObj, null, 2));

        let va_number = '-';
        let bank = '-';

        if (dataObj.payment_type !== 'qris' && dataObj.va_numbers && dataObj.va_numbers.length > 0) {
            va_number = dataObj.va_numbers[0].va_number;
            bank = dataObj.va_numbers[0].bank;
        }

        let disc_idr = $('#disc_idr').val();
        let disc_percent = $('#disc_percent').val();

        $.ajax({
            type: "post",
            url: "<?= site_url('transactions/saveData') ?>",
            data: {
                invoice: serverResponse.invoice,
                invoiceDate: serverResponse.invoiceDate,
                customer: serverResponse.customer,
                gross_total: serverResponse.gross_total,
                net_total: serverResponse.net_total,
                order_id: dataObj.order_id,
                payment_type: dataObj.payment_type,
                transaction_status: dataObj.transaction_status,
                va_number: va_number,
                bank: bank,
                disc_idr: disc_idr,
                disc_percent: disc_percent
            },
            dataType: "json",
            success: function(saveResp) {
                if (saveResp.success) {
                    $.ajax({
                        type: "post",
                        url: "<?= site_url('transactions/printInvoice') ?>",
                        data: {
                            invoice: saveResp.invoice
                        },
                        success: function(printResp) {
                            if (printResp.success) {
                                if (status === 'succ') {
                                    Swal.fire({
                                        title: "Printed!",
                                        html: 'Invoice printed successfully',
                                        icon: "success"
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                }
                            }
                        },
                        error: function(xhr, thrownError) {
                            alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                        }
                    });
                }
            },
            error: function(xhr, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        });
    }

    $(document).ready(function() {
        $(document).ready(function() {
            $('#btnCash').click(function(e) {
                e.preventDefault();

                $(this).addClass('active');
                $('#btnTransfer').removeClass('active');

                $('#cash-form').show();
                $('#change-wrapper').show();
                $('#btnSave').prop('disabled', false);
                $('#btnCancel').prop('disabled', false);
            });

            $('#btnTransfer').click(function(e) {
                e.preventDefault();

                $(this).addClass('active');
                $('#btnCash').removeClass('active');

                countDiscount();
                $('#cash-form').hide();
                $('#change-wrapper').hide();
                $('#btnSave').prop('disabled', true);
                $('#btnCancel').prop('disabled', true);
                paymentGateway();
            });
        });

        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
        $('#disc_percent').autoNumeric('init', {
            aSep: ',',
            aDec: '.',
            mDec: '2',
        });
        $('#disc_idr').autoNumeric('init', {
            aSep: ',',
            aDec: '.',
            mDec: '0',
        });
        $('#net_total').autoNumeric('init', {
            aSep: ',',
            aDec: '.',
            mDec: '0',
        });
        $('#cash_amount').autoNumeric('init', {
            aSep: ',',
            aDec: '.',
            mDec: '0',
        });
        $('#change').autoNumeric('init', {
            aSep: ',',
            aDec: '.',
            mDec: '0',
            vMin: '-999999999.99',
        });
        $('#disc_percent').keyup(function(e) {
            countDiscount();
        });
        $('#disc_idr').keyup(function(e) {
            countDiscount();
        });
        $('#cash_amount').keyup(function(e) {
            countChange();
        });

        $('#formPayment').submit(function(e) {
            e.preventDefault();

            let payment = $('input[name="payment"]:checked').val();
            let cash_amount = ($('#cash_amount').val() == "") ? 0 : $('#cash_amount').autoNumeric('get');
            let mid_trans = $('#mid_trans').val();
            let change = ($('#change').val() == "") ? 0 : $('#change').autoNumeric('get');

            if (parseFloat(cash_amount) == 0 || parseFloat(cash_amount) == "") {
                Toast.fire({
                    icon: "error",
                    title: "Please Input Payment Amount First",
                });
            } else if (parseFloat(change) < 0) {
                Toast.fire({
                    icon: "warning",
                    title: "The Payment Amount is Not Enough",
                });
            } else {
                $.ajax({
                    type: "post",
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function() {
                        $('#btnSave').prop('disabled', true)
                        $('#btnSave').html('<i class="fa fa-spin fa-spinner"></i>')
                    },
                    complete: function() {
                        $('#btnSave').prop('disabled', false)
                        $('#btnSave').html('Save')
                    },
                    success: function(response) {
                        if (response.success == 'Success') {
                            Swal.fire({
                                title: "Print Invoice?",
                                text: "You won't be able to revert this!",
                                icon: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#3085d6",
                                cancelButtonColor: "#d33",
                                confirmButtonText: "Yes, Print!"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $.ajax({
                                        type: "post",
                                        url: "<?= site_url('transactions/printInvoice') ?>",
                                        data: {
                                            invoice: response.invoice
                                        },
                                        success: function(response) {
                                            Swal.fire({
                                                title: "Printed!",
                                                html: 'Invoice printed successfully',
                                                icon: "success"
                                            }).then((result) => {
                                                window.location.reload();
                                            });
                                        },
                                        error: function(xhr, thrownError) {
                                            alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                                        }
                                    });
                                } else {
                                    window.location.reload();
                                }
                            });
                        }
                    },
                    error: function(xhr, thrownError) {
                        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                });
            }
            return false
        });
    });

    function countDiscount() {
        let gross_total = $('#gross_total').val();
        let disc_percent = ($('#disc_percent').val() == "") ? 0 : $('#disc_percent').autoNumeric('get');
        let disc_idr = ($('#disc_idr').val() == "") ? 0 : $('#disc_idr').autoNumeric('get');

        let result;
        result = parseFloat(gross_total) - (parseFloat(gross_total) * parseFloat(disc_percent) / 100) - parseFloat(disc_idr)

        $('#net_total').val(result);
        let net_total = $('#net_total').val();
        $('#net_total').autoNumeric('set', result);
    }

    function countChange() {
        let total_payment = $('#net_total').autoNumeric('get');
        let cash_amount = ($('#cash_amount').val() == "") ? 0 : $('#cash_amount').autoNumeric('get');

        change = parseFloat(cash_amount) - parseFloat(total_payment);

        $('#change').val(change);
        let changex = $('#change').val();
        $('#change').autoNumeric('set', changex);
    }
</script>