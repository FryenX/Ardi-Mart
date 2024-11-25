<?= $this->extend('templates/menu') ?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
<div class="alert alert-info alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <h5><i class="icon fas fa-info"></i> Alert!</h5>
    Welcome To Dashboard
</div>
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?= $new_transactions ?></h3>

                <p>New Orders</p>
            </div>
            <div class="icon">
                <i class="ion ion-bag"></i>
            </div>
            <a href="" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>53<sup style="font-size: 20px">%</sup></h3>

                <p>Bounce Rate</p>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?= $data_user ?></h3>

                <p>User Registrations</p>
            </div>
            <div class="icon">
                <i class="ion ion-person-add"></i>
            </div>
            <a href="<?= base_url('users') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>65</h3>

                <p>Unique Visitors</p>
            </div>
            <div class="icon">
                <i class="ion ion-pie-graph"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        userRegis();
        newTransactions();
    });

    function userRegis() {
        $.ajax({
            type: "post",
            url: "<?= site_url('main/userRegis') ?>",
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    $('#userCount').text(response.success);
                }
            }
        });
    }

    function newTransactions() {
        let date = $('#date').val();
        $.ajax({
            type: "post",
            url: "<?= site_url('main/newTransactions') ?>",
            data: {
                date: date
            },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    $('#newTransactions').text(response.success);
                }
            }
        });
    }
</script>
<?= $this->endSection() ?>