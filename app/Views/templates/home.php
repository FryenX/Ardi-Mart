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
            <a href="<?= base_url('transactions/data') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>Rp. <?= number_format($profit, "2", ".", ",")  ?></h3>

                <p>Margin Profit</p>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
            <a href="<?= base_url('transactions/data') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
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
                <h3><?= $product_data ?></h3>

                <p>Product Registered</p>
            </div>
            <div class="icon">
                <i class="ion ion-pricetags"></i>
            </div>
            <a href="<?= base_url('products') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-7 connectedSortable ui-sortable">
        <div class="card">
            <div class="card-header bg-warning ui-sortable-handle" style="cursor: move; display: flex; justify-content: space-between; align-items: center;">
                <!-- Left: Sales -->
                <h1 class="card-title" style="font-size: 24px;">
                    <i class="fas fa-chart-pie mr-1"></i>
                    Sales
                </h1>

                <div class="text-center" style="flex-grow: 1;">
                    <ul class="nav nav-pills justify-content-center" style="list-style: none; text-decoration: none;">
                        <li class="nav-item">
                            <a class="nav-link active" href="#sales-chart" data-toggle="tab">Sales</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#products-chart" data-toggle="tab">Products</a>
                        </li>
                    </ul>
                </div>

                <div class="card-tools">
                    <select class="custom-select" style="height: 100%; line-height: 20px;" id="year" name="year">
                        <!-- Option values go here -->
                    </select>
                </div>
            </div>

            <div class="card-body">
                <div class="tab-content p-0">
                    <div class="chart tab-pane active" id="sales-chart" style="position: relative; ">
                        <div class="chartjs-size-monitor">
                            <div class="chartjs-size-monitor-expand">
                                <div class=""></div>
                            </div>
                            <div class="chartjs-size-monitor-shrink">
                                <div class=""></div>
                            </div>
                        </div>
                    </div>
                    <div class="chart tab-pane" id="products-chart" style="position: relative;">
                        <div class="chartjs-size-monitor">
                            <div class="chartjs-size-monitor-expand">
                                <div class=""></div>
                            </div>
                            <div class="chartjs-size-monitor-shrink">
                                <div class=""></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section class="col-lg-5 connectedSortable ui-sortable">
        <div class="card bg-gradient-success">
            <div class="card-header border-0 ui-sortable-handle" style="cursor: move;">

                <h3 class="card-title">
                    <i class="far fa-calendar-alt"></i>
                    Calendar
                </h3>
                <!-- tools card -->
                <div class="card-tools">
                    <!-- button with a dropdown -->
                    <div class="btn-group">
                        <div class="dropdown-menu" role="menu">
                            <a href="#" class="dropdown-item">Add new event</a>
                            <a href="#" class="dropdown-item">Clear events</a>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item">View calendar</a>
                        </div>
                    </div>
                    <button type="button" class="btn btn-success btn-sm" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-success btn-sm" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="card-body pt-0 text-center">
                <div id="calendar" style="max-width: 900px; margin: 0 auto;"></div>
            </div>
        </div>
    </section>
</div>
<style>
    .fc-button {
        font-size: 0.675 !important;
        padding: 0.30rem 0.50rem !important;
    }
    .fc-col-header-cell {
        background-color: #28a745;
    }
    .fc {
        color: white;
    }
    .fc-daygrid-day-number {
        color: white !important;
    }

    .fc-toolbar-title {
        color: white !important;
    }
    .fc-col-header-cell-cushion {
        color: white;
    }
    .fc-col-header-cell-cushion:hover {
        color: white;
    }
    .fc-day-today {
        background-color: rgb(61, 138, 52) !important;
    }

    .fc-toolbar-chunk {
        display: flex;
        justify-content: center;
        margin-bottom: 0.5rem;
    }

    .fc-toolbar-title {
        font-size: 1.5rem;
        font-weight: bold;
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
    $(document).ready(function() {
        showSaleYears();
        const currentYear = new Date().getFullYear();
        showSalesChart(currentYear);
        showProductsChart(currentYear);

        $('#year').change(function() {
            const selectedYear = $(this).val();
            showSalesChart(selectedYear);
            showProductsChart(selectedYear);
        });

        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: '',
                center: 'title',
                right: ''
            },
            footerToolbar: {
                left: 'prev,next today',
                center: '',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            timeZone: 'GMT+8',
            height: 'auto',
        });

        calendar.render();
    });

    function showSaleYears() {
        $.ajax({
            url: "<?= site_url('main/fetchSaleYears') ?>",
            dataType: "json",
            success: function(response) {
                if (response.year) {
                    $('#year').html(response.year);

                    const currentYear = new Date().getFullYear();
                    $('#year').val(currentYear);
                }
            },
            error: function(xhr, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        });
    }

    function showSalesChart() {
        const year = $('#year').val() || new Date().getFullYear();
        $.ajax({
            type: "post",
            url: "<?= site_url('main/fetchSalesData') ?>",
            data: {
                year: year
            },
            dataType: "json",
            beforeSend: function() {
                $('#sales-chart').html('<i class="fa fa-spin fa-spinner"></i>');
            },
            success: function(response) {
                if (response.data) {
                    $('#sales-chart').html(response.data);
                }
            },
            error: function(xhr, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        });
    }

    function showProductsChart() {
        const year = $('#year').val() || new Date().getFullYear();
        $.ajax({
            type: "post",
            url: "<?= site_url('main/fetchProductsData') ?>",
            data: {
                year: year
            },
            dataType: "json",
            beforeSend: function() {
                $('#products-chart').html('<i class="fa fa-spin fa-spinner"></i>');
            },
            success: function(response) {
                if (response.data) {
                    $('#products-chart').html(response.data);
                }
            },
            error: function(xhr, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        });
    }
</script>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>
<script src="<?= base_url('assets') ?>/plugins/chart.js/Chart.min.js"></script>
<?= $this->endSection() ?>