<div class="modal fade" id="modalAddForm" tabindex="-1" aria-labelledby="modalAddFormLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddFormLabel">Add Units</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?= form_open('units/saveData', ['id' => 'formSaveUnits']) ?>
            <input type="hidden" name="action" id="action" value="<?= $action; ?>">
            <div class="modal-body">
                <div class="form-group">
                    <label for="name">Units Name</label>
                    <input type="text" name="name" class="form-control" id="name">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="saveUnits">Add</button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#formSaveUnits').submit(function(e) {
            e.preventDefault();
            $.ajax({
                type: "post",
                url: $(this).attr('action'),
                data: $(this).serialize(),
                dataType: "json",
                beforeSend: function(e) {
                    $('#saveButton').prop('disabled', true)
                    $('#saveButton').html('<i class="fa fa-spin fa-spinner"></i>')
                },
                success: function(response) {
                    let action = $('#action').val();
                    if (response.success) {
                        if (action == 0) {
                            Swal.fire(
                                'Success!',
                                response.success,
                                'success'
                            ).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.reload();
                                }
                            });
                        } else {
                            $('#modalAddForm').modal('hide');
                            Swal.fire(
                                'Success!',
                                response.success,
                                'success'
                            ).then(() => {
                                showUnits();
                            });
                        }
                    }
                },
                error: function(xhr, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            });
            return false;
        });
    });
</script>