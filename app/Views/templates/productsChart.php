<link rel="stylesheet" href="<?= base_url('assets') ?>/plugins/chart.js/Chart.min.css">
<script src="<?= base_url('assets') ?>/plugins/chart.js/Chart.bundle.min.js"></script>

<canvas id="productsData" style="height: 30vh; width: 60vh;"></canvas>

<?php
$total = "";
$product_name = "";

foreach ($chart as $row):
    $name = $row->name;
    $product_name .= "'$name'" . ",";

    $products = $row->qty;
    $total .= "$products" . ",";
endforeach;
?>

<script>
    var ctx = document.getElementById('productsData').getContext('2d');

    // Define the labels and data from PHP
    var labels = [<?= rtrim($product_name, ',') ?>]; // Trim trailing comma
    var data = [<?= rtrim($total, ',') ?>]; // Trim trailing comma

    // Generate random colors in JavaScript
    var backgroundColors = labels.map(() => {
        return `#${Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0')}`;
    });

    // Doughnut chart configuration
    var productsData = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels, // Product names
            datasets: [{
                data: data, // Product quantities
                backgroundColor: backgroundColors, // Generated colors
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom', // Legend position
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + tooltipItem.raw; // Label and value
                        }
                    }
                }
            }
        }
    });
</script>