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
            <a href="<?= base_url('transactions') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?= number_format($profit, "2", ".", ",")  ?></h3>

                <p>Margin Profit</p>
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
            <div class="card-header ui-sortable-handle bg-primary" style="cursor: move;">
                <h1 class="card-title d-flex" style="font-size: 28px;">
                    <i class="fas fa-chart-pie mr-1"></i>
                    Sales
                </h1>
                <div class="card-tools">
                    <select class="custom-select" style="height: 30px; line-height: 15px;" id="year" name="year">
                    </select>
                </div>
            </div>
            <div class="card-body">
                <div class="tab-content p-0">
                    <div class="chart tab-pane active" id="sales-chart" style="position: relative; ">

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
                        <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" data-offset="-52">
                            <i class="fas fa-bars"></i>
                        </button>
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
                <!-- /. tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body pt-0">
                <!--The calendar -->
                <div id="calendar" style="width: 100%">
                    <div class="bootstrap-datetimepicker-widget usetwentyfour">
                        <ul class="list-unstyled">
                            <li class="show">
                                <div class="datepicker">
                                    <div class="datepicker-days" style="">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th class="prev" data-action="previous"><span class="fa fa-chevron-left" title="Previous Month"></span></th>
                                                    <th class="picker-switch" data-action="pickerSwitch" colspan="5" title="Select Month">December 2024</th>
                                                    <th class="next" data-action="next"><span class="fa fa-chevron-right" title="Next Month"></span></th>
                                                </tr>
                                                <tr>
                                                    <th class="dow">Su</th>
                                                    <th class="dow">Mo</th>
                                                    <th class="dow">Tu</th>
                                                    <th class="dow">We</th>
                                                    <th class="dow">Th</th>
                                                    <th class="dow">Fr</th>
                                                    <th class="dow">Sa</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td data-action="selectDay" data-day="12/01/2024" class="day weekend">1</td>
                                                    <td data-action="selectDay" data-day="12/02/2024" class="day active today">2</td>
                                                    <td data-action="selectDay" data-day="12/03/2024" class="day">3</td>
                                                    <td data-action="selectDay" data-day="12/04/2024" class="day">4</td>
                                                    <td data-action="selectDay" data-day="12/05/2024" class="day">5</td>
                                                    <td data-action="selectDay" data-day="12/06/2024" class="day">6</td>
                                                    <td data-action="selectDay" data-day="12/07/2024" class="day weekend">7</td>
                                                </tr>
                                                <tr>
                                                    <td data-action="selectDay" data-day="12/08/2024" class="day weekend">8</td>
                                                    <td data-action="selectDay" data-day="12/09/2024" class="day">9</td>
                                                    <td data-action="selectDay" data-day="12/10/2024" class="day">10</td>
                                                    <td data-action="selectDay" data-day="12/11/2024" class="day">11</td>
                                                    <td data-action="selectDay" data-day="12/12/2024" class="day">12</td>
                                                    <td data-action="selectDay" data-day="12/13/2024" class="day">13</td>
                                                    <td data-action="selectDay" data-day="12/14/2024" class="day weekend">14</td>
                                                </tr>
                                                <tr>
                                                    <td data-action="selectDay" data-day="12/15/2024" class="day weekend">15</td>
                                                    <td data-action="selectDay" data-day="12/16/2024" class="day">16</td>
                                                    <td data-action="selectDay" data-day="12/17/2024" class="day">17</td>
                                                    <td data-action="selectDay" data-day="12/18/2024" class="day">18</td>
                                                    <td data-action="selectDay" data-day="12/19/2024" class="day">19</td>
                                                    <td data-action="selectDay" data-day="12/20/2024" class="day">20</td>
                                                    <td data-action="selectDay" data-day="12/21/2024" class="day weekend">21</td>
                                                </tr>
                                                <tr>
                                                    <td data-action="selectDay" data-day="12/22/2024" class="day weekend">22</td>
                                                    <td data-action="selectDay" data-day="12/23/2024" class="day">23</td>
                                                    <td data-action="selectDay" data-day="12/24/2024" class="day">24</td>
                                                    <td data-action="selectDay" data-day="12/25/2024" class="day">25</td>
                                                    <td data-action="selectDay" data-day="12/26/2024" class="day">26</td>
                                                    <td data-action="selectDay" data-day="12/27/2024" class="day">27</td>
                                                    <td data-action="selectDay" data-day="12/28/2024" class="day weekend">28</td>
                                                </tr>
                                                <tr>
                                                    <td data-action="selectDay" data-day="12/29/2024" class="day weekend">29</td>
                                                    <td data-action="selectDay" data-day="12/30/2024" class="day">30</td>
                                                    <td data-action="selectDay" data-day="12/31/2024" class="day">31</td>
                                                    <td data-action="selectDay" data-day="01/01/2025" class="day new">1</td>
                                                    <td data-action="selectDay" data-day="01/02/2025" class="day new">2</td>
                                                    <td data-action="selectDay" data-day="01/03/2025" class="day new">3</td>
                                                    <td data-action="selectDay" data-day="01/04/2025" class="day new weekend">4</td>
                                                </tr>
                                                <tr>
                                                    <td data-action="selectDay" data-day="01/05/2025" class="day new weekend">5</td>
                                                    <td data-action="selectDay" data-day="01/06/2025" class="day new">6</td>
                                                    <td data-action="selectDay" data-day="01/07/2025" class="day new">7</td>
                                                    <td data-action="selectDay" data-day="01/08/2025" class="day new">8</td>
                                                    <td data-action="selectDay" data-day="01/09/2025" class="day new">9</td>
                                                    <td data-action="selectDay" data-day="01/10/2025" class="day new">10</td>
                                                    <td data-action="selectDay" data-day="01/11/2025" class="day new weekend">11</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="datepicker-months" style="display: none;">
                                        <table class="table-condensed">
                                            <thead>
                                                <tr>
                                                    <th class="prev" data-action="previous"><span class="fa fa-chevron-left" title="Previous Year"></span></th>
                                                    <th class="picker-switch" data-action="pickerSwitch" colspan="5" title="Select Year">2024</th>
                                                    <th class="next" data-action="next"><span class="fa fa-chevron-right" title="Next Year"></span></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="7"><span data-action="selectMonth" class="month">Jan</span><span data-action="selectMonth" class="month">Feb</span><span data-action="selectMonth" class="month">Mar</span><span data-action="selectMonth" class="month">Apr</span><span data-action="selectMonth" class="month">May</span><span data-action="selectMonth" class="month">Jun</span><span data-action="selectMonth" class="month">Jul</span><span data-action="selectMonth" class="month">Aug</span><span data-action="selectMonth" class="month">Sep</span><span data-action="selectMonth" class="month">Oct</span><span data-action="selectMonth" class="month">Nov</span><span data-action="selectMonth" class="month active">Dec</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="datepicker-years" style="display: none;">
                                        <table class="table-condensed">
                                            <thead>
                                                <tr>
                                                    <th class="prev" data-action="previous"><span class="fa fa-chevron-left" title="Previous Decade"></span></th>
                                                    <th class="picker-switch" data-action="pickerSwitch" colspan="5" title="Select Decade">2020-2029</th>
                                                    <th class="next" data-action="next"><span class="fa fa-chevron-right" title="Next Decade"></span></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="7"><span data-action="selectYear" class="year old">2019</span><span data-action="selectYear" class="year">2020</span><span data-action="selectYear" class="year">2021</span><span data-action="selectYear" class="year">2022</span><span data-action="selectYear" class="year">2023</span><span data-action="selectYear" class="year active">2024</span><span data-action="selectYear" class="year">2025</span><span data-action="selectYear" class="year">2026</span><span data-action="selectYear" class="year">2027</span><span data-action="selectYear" class="year">2028</span><span data-action="selectYear" class="year">2029</span><span data-action="selectYear" class="year old">2030</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="datepicker-decades" style="display: none;">
                                        <table class="table-condensed">
                                            <thead>
                                                <tr>
                                                    <th class="prev" data-action="previous"><span class="fa fa-chevron-left" title="Previous Century"></span></th>
                                                    <th class="picker-switch" data-action="pickerSwitch" colspan="5">2000-2090</th>
                                                    <th class="next" data-action="next"><span class="fa fa-chevron-right" title="Next Century"></span></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="7"><span data-action="selectDecade" class="decade old" data-selection="2006">1990</span><span data-action="selectDecade" class="decade" data-selection="2006">2000</span><span data-action="selectDecade" class="decade" data-selection="2016">2010</span><span data-action="selectDecade" class="decade active" data-selection="2026">2020</span><span data-action="selectDecade" class="decade" data-selection="2036">2030</span><span data-action="selectDecade" class="decade" data-selection="2046">2040</span><span data-action="selectDecade" class="decade" data-selection="2056">2050</span><span data-action="selectDecade" class="decade" data-selection="2066">2060</span><span data-action="selectDecade" class="decade" data-selection="2076">2070</span><span data-action="selectDecade" class="decade" data-selection="2086">2080</span><span data-action="selectDecade" class="decade" data-selection="2096">2090</span><span data-action="selectDecade" class="decade old" data-selection="2106">2100</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </li>
                            <li class="picker-switch accordion-toggle"></li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /.card-body -->
        </div>
    </section>
</div>

<script>
    $(document).ready(function() {
        showSaleYears();
        const currentYear = new Date().getFullYear();
        showSalesChart(currentYear);

        $('#year').change(function() {
            const selectedYear = $(this).val();
            showSalesChart(selectedYear);
        });
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
</script>
<script src="<?= base_url('assets') ?>/plugins/chart.js/Chart.min.js"></script>
<?= $this->endSection() ?>