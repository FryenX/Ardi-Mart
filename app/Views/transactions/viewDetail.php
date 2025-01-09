<table class="table table-striped table-sm table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Barcode</th>
            <th>Product</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Sub Total</th>
            <th>#</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $num = 1;
        foreach ($dataDetail->getResultArray() as $row):
        ?>
            <tr>
                <td><?= $num++ ?></td>
                <td><?= $row['barcode'] ?></td>
                <td><?= $row['product'] ?></td>
                <td><?= $row['qty'] ?></td>
                <td style="text-align: right;"><?= number_format($row['sell_price'], 0, ",", ".") ?></td>
                <td style="text-align: right;"><?= number_format($row['sub_total'], 0, ",", ".") ?></td>
                <td>
                    <button type="button" class="btn btn-danger" onclick="deleteItem('<?= $row['id'] ?>', '<?= $row['product'] ?>')">
                        <i class="fa fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
        <?php
        endforeach;
        ?>
    </tbody>
</table>

<script>
    function deleteItem(id, name) {
        Swal.fire({
            title: "Delete this Item?",
            html: `Are you sure want to delete <strong>${name}</strong>`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "post",
                    url: "<?= site_url('transactions/deleteItem') ?>",
                    data: {
                        id: id,
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: "Success!",
                                html: response.success,
                                icon: "success"
                            });
                            detailTransactionsData();
                            empty();
                        }
                    }
                });
            }
        });
    }
</script>