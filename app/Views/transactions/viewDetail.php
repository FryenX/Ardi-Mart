<table class="table table-striped table-sm table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Code</th>
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
            foreach($dataDetail->getResultArray() AS $row):
                ?>
                <tr>
                    <td><?= $num++ ?></td>
                    <td><?= $row['barcode'] ?></td>
                    <td><?= $row['product'] ?></td>
                    <td><?= $row['qty'] ?></td>
                    <td style="text-align: right;"><?= number_format($row['sell_price'], 0, ",", ".") ?></td>
                    <td style="text-align: right;"><?= number_format($row['sub_total'], 0, ",", ".") ?></td>
                </tr>
            <?php
            endforeach;
        ?>
    </tbody>
</table>