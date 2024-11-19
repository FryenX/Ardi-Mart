<?= $this->extend('templates/menu'); ?>

<?= $this->section('title'); ?>
<h3><i class="fa fa-users"></i> Users</h3>
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
            <button type="button" class="btn btn-primary addButton" id="addButton" onclick="window.location='<?= site_url('users/add') ?>'">
                <i class="fa fa-plus"></i> Add User
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
        <div class="table-responsive">
            <?= form_open('users/index'); ?>
            <?= csrf_field(); ?>
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Role / Username" value="<?= $search ? $search : '' ?>" name="searchUser" autofocus>
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit" name="searchUserBtn" id="button-addon2">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
            <?= form_close(); ?>
            <table class="table table-bordered table-hover display">
                <thead>
                    <tr>
                        <th colspan="1">No</th>
                        <th colspan="8">User</th>
                        <th colspan="3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $num = 1 + (($pagenumber - 1) * 10);
                    foreach ($query as $row):
                    ?>
                        <tr>
                            <td colspan="1"><?= $num++ ?></td>
                            <td colspan="8"><strong><?= $row['info'] ?></strong> - <?= $row['name'] ?></td>
                            <td colspan="3">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-success"
                                    onclick="window.location='/users/edit/<?= $row['uuid'] ?>'"
                                    <?= ($row['info'] === 'Admin') ? 'disabled' : '' ?>>
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-danger"
                                    onclick="deleteItem('<?= $row['uuid'] ?>', '<?= $row['name'] ?>')"
                                    <?php if ($row['info'] === 'Admin' || $row['uuid'] == session()->get('uuid')): ?>
                                    disabled
                                    <?php endif; ?>>
                                    <i class="fa fa-trash"></i>
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-warning text-white"
                                    onclick="window.location='/users/changePassword/<?= $row['uuid'] ?>'"
                                    <?= ($row['info'] === 'Admin') ? 'disabled' : '' ?>>
                                    <i class="fa fa-lock"></i>
                                    Change Password
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="text-center">
                <?= $pager_user->links('users', 'paging_data'); ?>
            </div>
        </div>
    </div>
</div>
<div id="viewmodal" style="display: none;"></div>
<script>
    function deleteItem(id, name) {
        Swal.fire({
            title: "Delete this user?",
            html: `Are you sure want to delete <strong>${name}</strong>`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes!",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "post",
                    url: "<?= site_url('users/delete') ?>",
                    data: {
                        uuid : id
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
                    },
                    error: function(xhr, thrownError) {
                        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                });
            }
        });
    }
</script>
<?= $this->endSection() ?>