<?= $this->extend('templates/menu'); ?>

<?= $this->section('title'); ?>
<h3><i class="fa fa-table"></i> Products</h3>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<?php

use Picqer\Barcode\BarcodeGeneratorSVG; ?>
<link rel="stylesheet" href="<?= base_url('assets') ?>/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url('assets') ?>/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<script src="<?= base_url('assets') ?>/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url('assets') ?>/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?= base_url('assets') ?>/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?= base_url('assets') ?>/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <button type="button" class="btn btn-primary addButton" id="addButton" onclick="window.location='<?= site_url('products/add') ?>'">
                <i class="fa fa-plus"></i> Add Products
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
            <?= form_open('products/index'); ?>
            <?= csrf_field(); ?>
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Search Barcode / Products" value="<?= $search ? $search : '' ?>" name="searchProduct" autofocus>
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit" name="searchProductBtn" id="button-addon2">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
            <?= form_close(); ?>
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Barcode</th>
                        <th>Product Name</th>
                        <th>Categories</th>
                        <th>Units</th>
                        <th>Image</th>
                        <th>Purchase Price (IDR)</th>
                        <th>Sell Price (IDR)</th>
                        <th>Stock</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require '../vendor/autoload.php';
                    $num = 1 + (($pagenumber - 1) * 10);
                    foreach ($query as $row):
                    ?>
                        <tr>
                            <td><?= $num++ ?></td>
                            <td>
                                <?php
                                $generator = new BarcodeGeneratorSVG();
                                $fileName = "barcode" . $num . ".svg";
                                file_put_contents($fileName, $generator->getBarcode($row['barcode'], $generator::TYPE_EAN_13, 2, 50));
                                ?>

                                <!-- Barcode Image -->
                                <img src="<?= $fileName ?>" alt="Barcode" style="display: block; margin: 0 auto;">

                                <!-- Barcode Text -->
                                <span style="display: block; text-align: center; margin-top: 5px;">
                                    <?= $row['barcode'] ?>
                                </span>
                            </td>
                            <td><?= $row['name'] ?></td>
                            <td><?= $row['category_name'] ?></td>
                            <td><?= $row['unit_name'] ?></td>
                            <td class="text-center justify-content-center"><img src="<?= $row['image'] ?>" class="img-fluid text-center" style="width: 200px;" alt=""></td>
                            <td style="text-align: right;"><?= number_format($row['sell_price'], 2, ",", ".") ?></td>
                            <td style="text-align: right;"><?= number_format($row['purchase_price'], 2, ",", ".")  ?></td>
                            <td style="text-align: right;"><?= number_format($row['stocks'], 2, ",", ".") ?></td>
                            <td>
                                <button class="btn btn-outline-success" onclick="window.location='products/edit/<?= $row['barcode'] ?>'">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-outline-danger" onclick="deleteItem('<?= $row['barcode'] ?>', '<?= $row['name'] ?>')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="d-flex justify-content-center">
                <?= $pager_products->links('products', 'paging_data'); ?>
            </div>
        </div>
    </div>
</div>
<div id="viewmodal" style="display: none;"></div>
<script>
    function deleteItem(code, name) {
        Swal.fire({
            title: "Delete this Product?",
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
                    url: "<?= site_url('products/delete') ?>",
                    data: {
                        barcode: code
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