<?= $this->extend('templates/menu'); ?>

<?= $this->section('title'); ?>
<h3><i class="fa fa-user"></i> Profile</h3>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<button type="button" class="btn my-3 btn-warning text-white addButton" id="addButton"
    onclick="window.location='<?= base_url('/') ?>'">
    <i class="fa fa-backward"></i> Back
</button>
<section>
    <div class="">
        <div class="row">
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <?php
                        $userImage = session()->get('image');

                        $filePath = FCPATH . $userImage;

                        if ($userImage && file_exists($filePath)) {
                            $imageSrc = base_url($userImage);
                        } else {
                            $imageSrc = base_url('assets/upload/users/default/default_user.png');
                        }
                        ?>
                        <img src="<?= esc($imageSrc) ?>"alt="avatar"
                            class="img-circle elevation-2" style="width: 150px; height: 150px; object-fit: cover;">
                        <h1 class="my-3 font-weight-bolder"><?= $name; ?></h1>
                        <div class="d-flex justify-content-center mb-2">
                            <button type="button" onclick="window.location='<?= site_url('/profile/edit/' . $uuid)  ?>'" class="btn-lg btn-success">Edit Profile</button>
                            <form action="<?= site_url('login/logout') ?>" method="POST">
                                <button type="submit" class="btn-lg btn-danger ml-2">Sign Out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Full Name</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0"><?= $name; ?></p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Email</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0"><?= $email; ?></p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Level</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0"><?= $level; ?></p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Username</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0"><?= $username; ?></p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Password</p>
                            </div>
                            <div class="col-sm-9 d-flex justify-content-between">
                                <p class="text-muted mb-0">***********</p>
                                <button onclick="window.location='<?= base_url('login/credential') ?>'" class="btn btn-warning text-white">Change Password</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>

</script>
<?= $this->endSection() ?>