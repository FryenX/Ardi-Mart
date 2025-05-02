<?= $this->extend('templates/menu'); ?>

<?= $this->section('title'); ?>
<h3><i class="fa fa-user"></i> Edit Profile</h3>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <button type="button" class="btn my-3 btn-warning text-white addButton" id="addButton"
            onclick="window.location='<?= base_url('/profile/' . $uuid) ?>'">
            <i class="fa fa-backward"></i> Back
        </button>

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
            <label for="name" class="col-sm-4 col-form-label is-invalid">Name</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" value="<?= $name ?>" id="name" name="name" autofocus>
                <div id="errorName" class="invalid-feedback" style="display: none;">
                </div>
                <div class="valid-feedback" style="display: none;">
                </div>
            </div>
        </div>
        <div class="form-group row" style="height: 50px;">
            <label for="username" class="col-sm-4 col-form-label is-invalid">Username</label>
            <div class="col-sm-8">
                <input type="text" value="<?= $username ?>" class="form-control" id="username" name="username">
                <div id="errorUserName" class="invalid-feedback" style="display: none;">
                </div>
                <div class="valid-feedback" style="display: none;">
                </div>
            </div>
        </div>
        <div class="form-group row" style="height: 50px;">
            <label for="email" class="col-sm-4 col-form-label is-invalid">Email</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" value="<?= $email ?>" id="email" name="email">
                <div id="errorEmail" class="invalid-feedback" style="display: none;">
                </div>
                <div class="valid-feedback" style="display: none;">
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label for="level" class="col-sm-4 col-form-label" style="height: 70px;">Level</label>
            <div class="col-sm-4">
                <input type="text" value="<?= $data_level ?>" readonly name="level" id="level" class="form-control">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-4 offset-sm-4">
                <label id="previewLabel" for="previewImage" style="display: none;">Preview Image</label>
                <img id="previewImage" src="" alt="Image Preview" style="display: none; max-width: 100%; border-box: height: auto; margin-top: 10px;">

                <label id="currentLabel" for="currentImage" style="display: block;">Current Image</label>
                <img id="currentImage" src="<?= base_url($image) ?>" class="img-fluid mb-3" alt="User Image">

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
            <div class="col-sm-4 offset-sm-4">
                <img id="previewImage" src="" alt="Image Preview" style="display: none; max-width: 100%; border-box: height: auto; margin-top: 10px;">
            </div>
        </div>
        <div class="form-group row">
            <label for="updateUsers" class="col-sm-4 col-form-label"></label>
            <div class="col-sm-4">
                <button type="submit" class="btn btn-success" id="updateUsers">
                    Update
                </button>
            </div>
        </div>
        <?= form_close() ?>
    </div>
</div>


<div id="viewModal" style="display: none;"></div>

<script>
    window.onload = function() {
        var currentImage = document.getElementById('currentImage');
        var currentLabel = document.getElementById('currentLabel');
        var currentImageSrc = currentImage.src;

        var img = new Image();
        img.onload = function() {};
        img.onerror = function() {
            currentImage.style.display = 'none';
            currentLabel.style.display = 'none';
        };
        img.src = currentImageSrc;

        var imageInput = document.getElementById('image');
        var previewImage = document.getElementById('previewImage');
        var previewLabel = document.getElementById('previewLabel');

        imageInput.addEventListener('change', function(event) {
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();

                currentImage.style.display = 'none';
                currentLabel.style.display = 'none';

                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                    previewLabel.style.display = 'block';
                };

                reader.readAsDataURL(file);
            } else {
                previewImage.style.display = 'none';
                previewLabel.style.display = 'none';

                currentImage.style.display = 'block';
                currentLabel.style.display = 'block';
            }
        });
    };

    $('#updateUsers').click(function(e) {
        e.preventDefault();

        let form = $('#formSave')[0];

        let data = new FormData(form);

        $.ajax({
            type: "post",
            url: "<?= site_url('users/updateProfile') ?>",
            data: data,
            dataType: "json",
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            cache: false,
            beforeSend: function() {
                $('#updateUsers').prop('disabled', true)
                $('#updateUsers').html('<i class="fa fa-spin fa-spinner"></i>')
            },
            complete: function() {
                $('#updateUsers').prop('disabled', false)
                $('#updateUsers').html('Update')
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
                            window.location = '<?= site_url('profile/' . $uuid) ?>';
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

<?= $this->endSection() ?>