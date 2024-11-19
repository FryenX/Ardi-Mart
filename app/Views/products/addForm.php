<?= $this->extend('templates/menu') ?>

<?= $this->section('title') ?>
<h3><i class="fa fa-users"></i> Add Products</h3>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<script src="<?= base_url('assets/plugins/autoNumeric.js') ?>"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <button type="button" class="btn btn-warning text-white addButton" id="addButton" onclick="window.location='<?= site_url('products') ?>'">
                <i class="text-white fa fa-backward"></i> Back
            </button>
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
        <?= form_open_multipart('', ['id' => 'formSave']) ?>
        <?= csrf_field() ?>
        <div class="form-group row" style="height: 50px;">
            <label for="barcode" class="col-sm-4 col-form-label is-invalid">Barcode</label>
            <div class="col-sm-4">
                <input type="text" class="form-control" id="barcode" name="barcode" autofocus>
                <div id="errorBarcode" class="invalid-feedback" style="display: none;">
                </div>
                <div class="valid-feedback" style="display: none;">
                </div>
            </div>
        </div>
        <div class="form-group row" style="height: 50px;">
            <label for="name" class="col-sm-4 col-form-label is-invalid">Name</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="name" name="name">
                <div id="errorName" class="invalid-feedback" style="display: none;">
                </div>
                <div class="valid-feedback" style="display: none;">
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label for="unit" class="col-sm-4 col-form-label" style="height: 70px;">Units</label>
            <div class="col-sm-4">
                <select name="unit" id="unit" class="form-control"></select>
                <div id="errorUnit" class="invalid-feedback" style="display: none;">
                </div>
            </div>
            <div class="col-sm-4">
                <button class="btn btn-primary" id="addUnit">
                    <i class="fa fa-plus-circle"></i>
                </button>
            </div>
        </div>
        <div class="form-group row">
            <label for="category" class="col-sm-4 col-form-label" style="height: 70px;">Category</label>
            <div class="col-sm-4">
                <select name="category" id="category" class="form-control"></select>
                <div id="errorCategory" class="invalid-feedback" style="display: none;">
                </div>
            </div>
            <div class="col-sm-4">
                <button class="btn btn-primary" id="addCategory">
                    <i class="fa fa-plus-circle"></i>
                </button>
            </div>
        </div>
        <div class="form-group row" style="height: 50px;">
            <label for="stocks" class="col-sm-4 col-form-label is-invalid">Stocks</label>
            <div class="col-sm-8">
                <input type="text" value="0" class="form-control" id="stocks" name="stocks">
                <div id="errorStocks" class="invalid-feedback" style="display: none;">
                </div>
                <div class="valid-feedback" style="display: none;">
                </div>
            </div>
        </div>
        <div class="form-group row" style="height: 50px;">
            <label for="purchase_price" class="col-sm-4 col-form-label is-invalid">Purchase Price (IDR)</label>
            <div class="col-sm-4">
                <input type="text" style="text-align: right;" class="form-control" id="purchase_price" name="purchase_price">
                <div id="errorPurchasePrice" class="invalid-feedback" style="display: none;">
                </div>
                <div class="valid-feedback" style="display: none;">
                </div>
            </div>
        </div>
        <div class="form-group row" style="height: 50px;">
            <label for="sell_price" class="col-sm-4 col-form-label is-invalid">Sell Price (IDR)</label>
            <div class="col-sm-4">
                <input type="text" style="text-align: right;" class="form-control" id="sell_price" name="sell_price">
                <div id="errorSellPrice" class="invalid-feedback" style="display: none;">
                </div>
                <div class="valid-feedback" style="display: none;">
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-4 offset-sm-4">
                <label id="previewLabel" for="previewImage" style="display: none;">Preview Image</label>
                <img id="previewImage" src="" alt="Image Preview" style="display: none; max-width: 100%; border-box: height: auto; margin-top: 10px;">
            </div>
        </div>
        <div class="form-group row">
            <label for="image" class="col-sm-4 col-form-label">Upload Image (<i>optional</i>)</label>
            <div class="col-sm-4">
                <input type="file" class="form-control-file" id="image" name="image" style="text-align: right;">
                <div id="errorUploadImage" class="invalid-feedback" style="display: none;">
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label for="saveProducts" class="col-sm-4 col-form-label"></label>
            <div class="col-sm-4">
                <button type="submit" class="btn btn-success" id="saveProducts">
                    Save
                </button>
            </div>
        </div>
        <?= form_close() ?>
    </div>
</div>

<div id="viewModal" style="display: none;"></div>

<script>
    $('#purchase_price').autoNumeric('init', {
        aSep: ',',
        aDec: '.',
        mDec: '2',
    })
    $('#sell_price').autoNumeric('init', {
        aSep: ',',
        aDec: '.',
        mDec: '2',
    })
    $('#stocks').autoNumeric('init', {
        aSep: ',',
        aDec: '.',
        mDec: '2',
    })

    document.getElementById('image').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const previewImage = document.getElementById('previewImage');
        const previewLabel = document.getElementById('previewLabel');

        if (file) {
            const reader = new FileReader();

            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewLabel.style.display = 'block';
                previewImage.style.display = 'block';
            };

            reader.readAsDataURL(file);
        } else {
            previewImage.src = '';
            previewImage.style.display = 'none';
        }
    });

    function showUnits() {
        $.ajax({
            url: "<?= site_url('products/fetchDataUnits') ?>",
            dataType: "json",
            success: function(response) {
                if (response.data) {
                    $('#unit').html(response.data);
                }
            },
            error: function(xhr, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        });
    }

    $(document).ready(function() {
        showUnits();
        $('#addUnit').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: "<?= site_url('units/add') ?>",
                dataType: "json",
                type: "post",
                data: {
                    action: 1
                },
                success: function(response) {
                    if (response.data) {
                        $('#viewModal').html(response.data).show();
                        $('#modalAddForm').on('shown.bs.modal', function(event) {
                            $('#name').focus();
                        })
                        $('#modalAddForm').modal('show');
                    }
                },
                error: function(xhr, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            });
        });
    });

    function showCategories() {
        $.ajax({
            url: "<?= site_url('products/fetchDataCategories') ?>",
            dataType: "json",
            success: function(response) {
                if (response.data) {
                    $('#category').html(response.data);
                }
            },
            error: function(xhr, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        });
    }

    $(document).ready(function() {
        showCategories();
        $('#addCategory').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: "<?= site_url('categories/add') ?>",
                dataType: "json",
                type: "post",
                data: {
                    action: 1
                },
                success: function(response) {
                    if (response.data) {
                        $('#viewModal').html(response.data).show();
                        $('#modalAddForm').on('shown.bs.modal', function(event) {
                            $('#name').focus();
                        })
                        $('#modalAddForm').modal('show');
                    }
                },
                error: function(xhr, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            });
        });
    });

    $('#saveProducts').click(function(e) {
        e.preventDefault();

        let form = $('#formSave')[0];

        let data = new FormData(form);

        $.ajax({
            type: "post",
            url: "<?= site_url('products/saveData') ?>",
            data: data,
            dataType: "json",
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            cache: false,
            beforeSend: function() {
                $('#saveProducts').prop('disabled', true)
                $('#saveProducts').html('<i class="fa fa-spin fa-spinner"></i>')
            },
            complete: function() {
                $('#saveProducts').prop('disabled', false)
                $('#saveProducts').html('Save')
            },
            success: function(response) {
                if (response.error) {
                    let dataError = response.error;
                    if (dataError.errorBarcode) {
                        $('#errorBarcode').html(dataError.errorBarcode).show();
                        $('#barcode').addClass('is-invalid');
                    } else {
                        $('#errorBarcode').fadeOut();
                        $('#barcode').removeClass('is-invalid').addClass('is-valid');
                    }
                    if (dataError.errorName) {
                        $('#errorName').html(dataError.errorName).show();
                        $('#name').addClass('is-invalid');
                    } else {
                        $('#errorName').fadeOut();
                        $('#name').removeClass('is-invalid').addClass('is-valid');
                    }
                    if (dataError.errorUnit) {
                        $('#errorUnit').html(dataError.errorUnit).show();
                        $('#unit').addClass('is-invalid');
                    } else {
                        $('#errorUnit').fadeOut();
                        $('#unit').removeClass('is-invalid').addClass('is-valid');
                    }
                    if (dataError.errorCategory) {
                        $('#errorCategory').html(dataError.errorCategory).show();
                        $('#category').addClass('is-invalid');
                    } else {
                        $('#errorCategory').fadeOut();
                        $('#category').removeClass('is-invalid').addClass('is-valid');
                    }
                    if (dataError.errorStocks) {
                        $('#errorStocks').html(dataError.errorStocks).show();
                        $('#stocks').addClass('is-invalid');
                    } else {
                        $('#errorStocks').fadeOut();
                        $('#stocks').removeClass('is-invalid').addClass('is-valid');
                    }
                    if (dataError.errorPurchasePrice) {
                        $('#errorPurchasePrice').html(dataError.errorPurchasePrice).show();
                        $('#purchase_price').addClass('is-invalid');
                    } else {
                        $('#errorPurchasePrice').fadeOut();
                        $('#purchase_price').removeClass('is-invalid').addClass('is-valid');
                    }
                    if (dataError.errorSellPrice) {
                        $('#errorSellPrice').html(dataError.errorSellPrice).show();
                        $('#sell_price').addClass('is-invalid');
                    } else {
                        $('#errorSellPrice').fadeOut();
                        $('#sell_price').removeClass('is-invalid').addClass('is-valid');
                    }
                } else {
                    Swal.fire({
                        icon: "success",
                        title: "Success!",
                        html: response.success
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    });
                }
            },
            error: function(xhr, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        });
    })
</script>
<?= $this->endSection(); ?>