<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 20px;
            margin: 20px auto;
            max-width: 1000px; /* Adjust max-width as needed */
        }
        .chart-container {
            flex: 1 1 calc(50% - 10px);
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        canvas {
            width: 100% !important;
            height: auto !important;
        }
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            .chart-container {
                flex: 1 1 100%;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="chart-container">
        <h6 class="mb-4">School Population</h6>
        <canvas id="school-chart"></canvas>
    </div>
    <div class="chart-container">
        <h6 class="mb-4">Fees</h6>
        <canvas id="fees-chart"></canvas>
    </div>
</div>

<script>
$(document).ready(function() {
    $.get('fetch_data.php', function(data) {
        try {
            console.log(data); // Log data to check if it's being fetched correctly
            if (!data || Object.keys(data).length === 0) {
                throw new Error("No data received or data format is incorrect");
            }

            const years = Object.keys(data); // Extract years
            const fees = years.map(year => data[year].fees); // Extract fees
            const school = years.map(year => data[year].school); // Extract school populations

            if (years.length === 0 || fees.length === 0 || school.length === 0) {
                throw new Error("Data arrays are empty");
            }

            // School Population Chart
            var ctxSchool = $("#school-chart").get(0).getContext("2d");
            var schoolChart = new Chart(ctxSchool, {
                type: "line",
                data: {
                    labels: years,
                    datasets: [{
                        label: "School Population",
                        fill: false,
                        backgroundColor: "rgba(255, 99, 132, 0.2)",
                        borderColor: "rgba(255, 99, 132, 1)",
                        data: school,
                        borderWidth: 2,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            ticks: {
                                font: {
                                    size: 14
                                },
                                maxRotation: 45,
                                minRotation: 45
                            }
                        },
                        y: {
                            ticks: {
                                font: {
                                    size: 14
                                }
                            }
                        }
                    },
                    layout: {
                        padding: {
                            left: 20,
                            right: 20,
                            top: 20,
                            bottom: 20
                        }
                    }
                }
            });

            // Fees Chart
            var ctxFees = $("#fees-chart").get(0).getContext("2d");
            var feesChart = new Chart(ctxFees, {
                type: "line",
                data: {
                    labels: years,
                    datasets: [{
                        label: "Fees",
                        fill: false,
                        backgroundColor: "rgba(0, 156, 255, 0.2)",
                        borderColor: "rgba(0, 156, 255, 1)",
                        data: fees,
                        borderWidth: 2,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            ticks: {
                                font: {
                                    size: 14
                                },
                                maxRotation: 45,
                                minRotation: 45
                            }
                        },
                        y: {
                            ticks: {
                                font: {
                                    size: 14
                                }
                            }
                        }
                    },
                    layout: {
                        padding: {
                            left: 20,
                            right: 20,
                            top: 20,
                            bottom: 20
                        }
                    }
                }
            });
        } catch (error) {
            console.error("Error processing data: ", error);
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.error("Failed to fetch data: ", textStatus, errorThrown);
    });
});
</script>
</body>
</html>
