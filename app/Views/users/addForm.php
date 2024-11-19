<?= $this->extend('templates/menu') ?>

<?= $this->section('title') ?>
<h3><i class="fa fa-users"></i> Add Users</h3>
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
        <div class="form-group row" style="height: 50px;">
            <label for="name" class="col-sm-4 col-form-label is-invalid">Name</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="name" name="name" autofocus>
                <div id="errorName" class="invalid-feedback" style="display: none;">
                </div>
                <div class="valid-feedback" style="display: none;">
                </div>
            </div>
        </div>
        <div class="form-group row" style="height: 50px;">
            <label for="username" class="col-sm-4 col-form-label is-invalid">Username</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="username" name="username" autofocus>
                <div id="errorUserName" class="invalid-feedback" style="display: none;">
                </div>
                <div class="valid-feedback" style="display: none;">
                </div>
            </div>
        </div>
        <div class="form-group row" style="height: 50px;">
            <label for="email" class="col-sm-4 col-form-label is-invalid">Email</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="email" name="email" autofocus>
                <div id="errorEmail" class="invalid-feedback" style="display: none;">
                </div>
                <div class="valid-feedback" style="display: none;">
                </div>
            </div>
        </div>
        <div class="form-group row" style="height: 50px;">
            <label for="password" class="col-sm-4 col-form-label">Password</label>
            <div class="col-sm-8 input-group">
                <input type="password" class="form-control" id="password" name="password">
                <div class="input-group-appended">
                    <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
                <div id="errorPassword" class="invalid-feedback" style="display: none;"></div>
                <div class="valid-feedback" style="display: none;"></div>
            </div>
        </div>
        <div class="form-group row" style="height: 50px;">
            <label for="password_confirm" class="col-sm-4 col-form-label">Confirm Password</label>
            <div class="col-sm-8 input-group">
                <input type="password" class="form-control" id="password_confirm" name="password_confirm">
                <div class="input-group-appended">
                    <button type="button" class="btn btn-outline-secondary" id="togglePasswordConfirm">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
                <div id="errorPasswordConfirm" class="invalid-feedback" style="display: none;"></div>
                <div class="valid-feedback" style="display: none;"></div>
            </div>
        </div>
        <div class="form-group row">
            <label for="level" class="col-sm-4 col-form-label" style="height: 70px;">Level</label>
            <div class="col-sm-4">
                <select name="level" id="level" class="form-control"></select>
                <div id="errorLevel" class="invalid-feedback" style="display: none;">
                </div>
            </div>
            <div class="col-sm-4">
                <button class="btn btn-primary" id="addLevels">
                    <i class="fa fa-plus-circle"></i>
                </button>
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
            <label for="saveUsers" class="col-sm-4 col-form-label"></label>
            <div class="col-sm-4">
                <button type="submit" class="btn btn-success" id="saveUsers">
                    Save
                </button>
            </div>
        </div>
        <?= form_close() ?>
    </div>
</div>

<div id="viewModal" style="display: none;"></div>

<script>
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

    function showLevels() {
        $.ajax({
            url: "<?= site_url('users/fetchDataLevels') ?>",
            dataType: "json",
            success: function(response) {
                if (response.data) {
                    $('#level').html(response.data);
                }
            },
            error: function(xhr, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        });
    }

    $(document).ready(function() {
        showLevels();
        $('#addLevels').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: "<?= site_url('levels/add') ?>",
                dataType: "json",
                type: "post",
                data: {
                    action: 1
                },
                success: function(response) {
                    if (response.data) {
                        $('#viewModal').html(response.data).show();
                        $('#modalAddForm').on('shown.bs.modal', function(event) {
                            $('#info').focus();
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

    $('#saveUsers').click(function(e) {
        e.preventDefault();

        let form = $('#formSave')[0];

        let data = new FormData(form);

        $.ajax({
            type: "post",
            url: "<?= site_url('users/saveData') ?>",
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
                $('#saveUsers').html('Save')
            },
            success: function(response) {
                if (response.error) {
                    let dataError = response.error;
                    if (dataError.errorName) {
                        $('#errorName').html(dataError.errorName).show();
                        $('#name').addClass('is-invalid');
                    } else {
                        $('#errorName').fadeOut();
                        $('#name').removeClass('is-invalid').addClass('is-valid');
                    }
                    if (dataError.errorUserName) {
                        $('#errorUserName').html(dataError.errorUserName).show();
                        $('#username').addClass('is-invalid');
                    } else {
                        $('#errorUserName').fadeOut();
                        $('#username').removeClass('is-invalid').addClass('is-valid');
                    }
                    if (dataError.errorEmail) {
                        $('#errorEmail').html(dataError.errorEmail).show();
                        $('#email').addClass('is-invalid');
                    } else {
                        $('#errorEmail').fadeOut();
                        $('#email').removeClass('is-invalid').addClass('is-valid');
                    }
                    if (dataError.errorPassword) {
                        $('#errorPassword').html(dataError.errorPassword).show();
                        $('#password').addClass('is-invalid');
                    } else {
                        $('#errorPassword').fadeOut();
                        $('#password').removeClass('is-invalid').addClass('is-valid');
                    }
                    if (dataError.errorPasswordConfirm) {
                        $('#errorPasswordConfirm').html(dataError.errorPasswordConfirm).show();
                        $('#password_confirm').addClass('is-invalid');
                    } else {
                        $('#errorPasswordConfirm').fadeOut();
                        $('#password_confirm').removeClass('is-invalid').addClass('is-valid');
                    }
                    if (dataError.errorLevel) {
                        $('#errorLevel').html(dataError.errorLevel).show();
                        $('#level').addClass('is-invalid');
                    } else {
                        $('#errorLevel').fadeOut();
                        $('#level').removeClass('is-invalid').addClass('is-valid');
                    }
                    if (dataError.errorUploadImage) {
                        $('#errorUploadImage').html(dataError.errorUploadImage).show();
                        $('#image').addClass('is-invalid');
                    } else {
                        $('#errorUploadImage').fadeOut();
                        $('#image').removeClass('is-invalid').addClass('is-valid');
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

    document.getElementById('togglePassword').addEventListener('click', function() {
        var passwordField = document.getElementById('password');
        var passwordFieldType = passwordField.type;

        if (passwordFieldType === 'password') {
            passwordField.type = 'text';
            this.innerHTML = '<i class="bi bi-eye"></i>';
        } else {
            passwordField.type = 'password';
            this.innerHTML = '<i class="bi bi-eye-slash"></i>';
        }
    });

    document.getElementById('togglePasswordConfirm').addEventListener('click', function() {
        var passwordField = document.getElementById('password_confirm');
        var passwordFieldType = passwordField.type;

        if (passwordFieldType === 'password') {
            passwordField.type = 'text';
            this.innerHTML = '<i class="bi bi-eye"></i>';
        } else {
            passwordField.type = 'password';
            this.innerHTML = '<i class="bi bi-eye-slash"></i>';
        }
    });
</script>
<?= $this->endSection(); ?>