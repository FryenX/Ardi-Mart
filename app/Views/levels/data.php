<?= $this->extend('templates/menu') ?>

<?= $this->section('title') ?>
<h3><i class="fa fa-share"></i> Levels</h3>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<?php
    $userLevel = session()->get('level_info');
?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <button class="btn btn-primary addButton" id="addButton" <?= ($userLevel != 'Admin') ? 'disabled' : '' ?>><i class="fa fa-plus" style="margin-right: 5px;"></i> Add Level</button>
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
        <table class="jsgrid-table cd" style="margin-bottom: 10px;">
            <thead class="jsgrid-header-row">
                <tr class="jsgrid-row">
                    <th style="width: 50px;" class="jsgrid-cell">No</th>
                    <th class="jsgrid-cell">Levels</th>
                    <th class="jsgrid-cell">Action</th>
                </tr>
            </thead>

            <tbody class="jsgrid-grid-body">
                <?php $num = 1 + (($pagenumber - 1) * 10);
                foreach ($levels as $row) :
                ?>
                    <tr class="jsgrid-row">
                        <td class="jsgrid-cell"><?= $num++; ?></td>
                        <td class="jsgrid-cell"><?= $row['info'] ?></td>
                        <td class="jsgrid-cell">
                            <button type="button" class="btn btn-sm btn-success" <?= ($userLevel != 'Admin') ? 'disabled' : '' ?> onclick="edit('<?= $row['id'] ?>')">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" <?= ($userLevel != 'Admin') ? 'disabled' : '' ?> onclick="deleteItem('<?= $row['id'] ?>', '<?= $row['info'] ?>')">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="float-center">
            <?= $pager_levels->links('levels', 'paging_data'); ?>
        </div>
    </div>
</div>

<div id="viewModal" style="display: none;"></div>

<script>
    $(document).ready(function() {
        $('#addButton').click(function(e) {
            e.preventDefault();

            $.ajax({
                url: "<?= site_url('levels/add') ?>",
                dataType: "json",
                type: 'post',
                data: {
                    action: 0
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



    function edit(id) {
        $.ajax({
            type: "post",
            url: "<?= site_url('levels/edit') ?>",
            data: {
                id: id,
            },
            dataType: "json",
            success: function(response) {
                if (response.data) {
                    $('#viewModal').html(response.data).show();
                    $('#modalFormEdit').on('shown.bs.modal', function(event) {
                        $('#info').focus();
                    })
                    $('#modalFormEdit').modal('show');
                }
            },
            error: function(xhr, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        });
    }

    function deleteItem(id, name) {
        Swal.fire({
            title: "Delete this Level?",
            html: `Are you sure want to delete <strong>${name}</strong>?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes!",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "post",
                    url: "<?= site_url('levels/delete') ?>",
                    data: {
                        id: id
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

<?= $this->endSection(); ?>