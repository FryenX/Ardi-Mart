<?= $this->extend('templates/menu') ?>

<?= $this->section('title') ?>
<h3><i class="fa fa-user"></i> Change Password</h3>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<script src="<?= base_url('assets/plugins/autoNumeric.js') ?>"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <button type="button" class="btn btn-warning text-white addButton" id="addButton" onclick="window.location='<?= site_url('users') ?>'">
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
        <input type="hidden" name="uuid" id="uuid" value="<?= $uuid ?>">
        <div class="form-group row" style="height: 50px;">
            <label for="oldPassword" class="col-sm-4 col-form-label is-invalid">Old Password</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" placeholder="Input Your Previous Password" id="oldPassword" name="oldPassword" autofocus>
                <div id="errorOldPassword" class="invalid-feedback" style="display: none;">
                </div>
                <div class="valid-feedback" style="display: none;">
                </div>
            </div>
        </div>
        <div class="form-group row" style="height: 50px;">
            <label for="newPassword" class="col-sm-4 col-form-label is-invalid">New Password</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" placeholder="Input New Password" id="newPassword" name="newPassword" autofocus>
                <div id="errorNewPassword" class="invalid-feedback" style="display: none;">
                </div>
                <div class="valid-feedback" style="display: none;">
                </div>
            </div>
        </div>
        <div class="form-group row" style="height: 50px;">
            <label for="confirmPassword" class="col-sm-4 col-form-label is-invalid">Confirm Password</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" placeholder="Confirm Password" id="confirmPassword" name="confirmPassword" autofocus>
                <div id="errorConfirmPassword" class="invalid-feedback" style="display: none;">
                </div>
                <div class="valid-feedback" style="display: none;">
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label for="saveUsers" class="col-sm-4 col-form-label"></label>
            <div class="col-sm-4">
                <button type="submit" class="btn btn-success" id="saveUsers">
                    Update
                </button>
            </div>
        </div>
        <?= form_close() ?>
    </div>
</div>

<div id="viewModal" style="display: none;"></div>

<script>
    $('#saveUsers').click(function(e) {
        e.preventDefault();

        let form = $('#formSave')[0];

        let data = new FormData(form);

        $.ajax({
            type: "post",
            url: "<?= site_url('users/updatePassword') ?>",
            data: data,
            dataType: "json",
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            cache: false,
            beforeSend: function() {
                $('#saveUsers').prop('disabled', true)
                $('#saveUsers').html('<i class="fa fa-spin fa-spinner"></i>')
            },
            complete: function() {
                $('#saveUsers').prop('disabled', false)
                $('#saveUsers').html('Update')
            },
            success: function(response) {
                if (response.error) {
                    let dataError = response.error;
                    if (dataError.errorOldPassword) {
                        $('#errorOldPassword').html(dataError.errorOldPassword).show();
                        $('#oldPassword').addClass('is-invalid');
                    } else {
                        $('#errorOldPassword').fadeOut();
                        $('#oldPassword').removeClass('is-invalid').addClass('is-valid');
                    }
                    if (dataError.errorNewPassword) {
                        $('#errorNewPassword').html(dataError.errorNewPassword).show();
                        $('#newPassword').addClass('is-invalid');
                    } else {
                        $('#errorNewPassword').fadeOut();
                        $('#newPassword').removeClass('is-invalid').addClass('is-valid');
                    }
                    if (dataError.errorConfirmPassword) {
                        $('#errorConfirmPassword').html(dataError.errorConfirmPassword).show();
                        $('#confirmPassword').addClass('is-invalid');
                    } else {
                        $('#errorConfirmPassword').fadeOut();
                        $('#confirmPassword').removeClass('is-invalid').addClass('is-valid');
                    }
                } else {
                    if (response.success) {
                        Swal.fire({
                            icon: "success",
                            title: "Success!",
                            html: response.success
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location = '<?= site_url('users') ?>';
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            html: response.failed,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#oldPassword').select();
                            }
                        });
                    }
                }
            },
            error: function(xhr, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        });
    })
</script>
<?= $this->endSection(); ?>