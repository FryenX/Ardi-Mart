<link rel="stylesheet" href="<?= base_url('assets') ?>/plugins/chart.js/Chart.min.css">
<script src="<?= base_url('assets') ?>/plugins/chart.js/Chart.bundle.min.js"></script>

<canvas id="salesData" style="height: 50vh; width: 80vh;"></canvas>

<?php
$allMonths = [
    'January',
    'February',
    'March',
    'April',
    'May',
    'June',
    'July',
    'August',
    'September',
    'October',
    'November',
    'December'
];

$months = "";
$total = "";

// Create a map of month => transactions from the database
$monthData = [];
foreach ($chart as $row) {
    $monthData[$row->month] = $row->transactions;
}

// Loop through all months and ensure default values if data is missing
foreach ($allMonths as $month) {
    $months .= "'$month'" . ",";
    $transactions = isset($monthData[$month]) ? $monthData[$month] : 0; // Default to 0
    $total .= "'$transactions'" . ",";
}

// Trim trailing commas
$months = rtrim($months, ",");
$total = rtrim($total, ",");
?>

<script>
    var ctx = document.getElementById('salesData').getContext('2d');
    var chartColors = [
        'rgb(0, 123, 255)', 'rgb(0, 123, 255)', 'rgb(0, 123, 255)',
        'rgb(0, 123, 255)', 'rgb(0, 123, 255)', 'rgb(0, 123, 255)',
        'rgb(0, 123, 255)', 'rgb(0, 123, 255)', 'rgb(0, 123, 255)',
        'rgb(0, 123, 255)', 'rgb(0, 123, 255)', 'rgb(0, 123, 255)'
    ];

    var chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [<?= $months ?>],
            datasets: [{
                label: 'Total Transactions',
                backgroundColor: chartColors,
                borderColor: chartColors,
                borderWidth: 1,
                data: [<?= $total ?>]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    labels: {
                        generateLabels: function(chart) {
                            return chart.data.labels.map((label, i) => ({
                                text: label,
                                fillStyle: chartColors[i],
                                hidden: false,
                                index: i
                            }));
                        }
                    }
                }
            },
            scales: {
                xAxes: [{
                    title: {
                        display: true,
                        text: 'Months'
                    }
                }],
                yAxes: [{
                    title: {
                        display: true,
                        text: 'Transactions'
                    },
                    ticks: {
                        beginAtZero: true,
                        min: 0,
                        max: 200
                    }
                }]
            },
            onClick: function(evt, elements) {
                if (elements.length > 0) {
                    const clickedElementIndex = elements[0].index;
                    const clickedMonth = chart.data.labels[clickedElementIndex];

                    showWeeklyGraph(clickedMonth);
                }
            }
        }
    });
</script>