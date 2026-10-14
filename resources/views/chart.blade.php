@extends('layout.app')


<style>
    .filterSelect1 {
        width: 20px;
    }

    @media screen and (max-width: 767px) {
        .first-chart {
            padding-top: 30px !important;
        }

        .users {
            margin-top: 10px !important;
            font-size: 14px !important;
        }
    }
</style>





@section('content')
    <div class="page-wrapper">
        <div class="content">

            <div class="row first-chart">
                <div class="col-12 col-md-12 col-lg-12 col-xl-12">
                    <div class="card">
                        <div class="card-body" style="padding: 30px;">
                            <div class="chart-title">
                                <div class="row ">
                                    <div class="col-6">
                                        <h3 class="users">Total Staffs</h3>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex justify-content-end mb-3">
                                            <select id="chartFilter" class="form-control ">
                                                <option value="year">This Year</option>
                                                <option value="month">This Month</option>
                                                <option value="week">This Week</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>




                            </div>
                            <canvas id="linegraph"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-12 col-lg-12 col-xl-12">
                    <div class="card">
                        <div class="card-body" style="padding: 30px;">
                            <div class="chart-title1 mb-5">
                                <!--  -->
                                <div class="row">
                                    <div class="col-6">
                                        <h3 class="users">Module Summary</h3>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex justify-content-end mb-3">
                                            <select id="chartFilter1" class="form-control ">
                                                <option value="year">This Year</option>
                                                <option value="month">This Month</option>
                                                <option value="week">This Week</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <canvas id="barchart"></canvas>

                        </div>
                    </div>
                </div>
            </div>




        </div>
    </div>

    <!-- Include Chart.js -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <script>




        function fetchChartDatastaff(filter = 'year') {
            $.ajax({
                url: "{{ url('/api/chart-data') }}",
                method: "GET",
                headers: { "Authorization": "Bearer " + token },
                data: {
                    user_id: userId,
                    filter: filter
                },
                success: function (response) {
                    $('.chart-title h3').text('Total Users');
                    renderCombinedChart(response.chartData, filter);

                },
                error: function (xhr, status, error) {
                    console.error("Error fetching chart data:", error);
                }
            });
        }

        function fetchChartData(filter = 'year') {
            $.ajax({
                url: "{{ url('/api/chart-data') }}",
                method: "GET",
                headers: { "Authorization": "Bearer " + token },
                data: {
                    user_id: userId,
                    filter: filter
                },
                success: function (response) {
                    $('.chart-title h3').text('Total Users');

                    renderBarChart(response.chartData, filter);
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching chart data:", error);
                }
            });
        }

        function renderCombinedChart(data, filter) {
            const ctx = document.getElementById("linegraph").getContext("2d");

            const monthNames = {
                "01": "January",
                "02": "February",
                "03": "March",
                "04": "April",
                "05": "May",
                "06": "June",
                "07": "July",
                "08": "August",
                "09": "September",
                "10": "October",
                "11": "November",
                "12": "December"
            };

            // Map labels to full month names if filter is 'year'
            const chartLabels = (filter === 'year')
                ? data.labels.map(label => monthNames[label] || label)
                : data.labels;

            new Chart(ctx, {
                type: "line",
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: "Patients",
                            data: data.patients.map(num => Math.round(num)),
                            borderColor: "rgba(164, 243, 164, 0.41)",
                            backgroundColor: "rgba(0, 255, 0, 0.1)",
                            fill: true
                        },
                        {
                            label: "Doctors",
                            data: data.doctors.map(num => Math.round(num)),
                            borderColor: "rgba(129, 129, 218, 0.41)",
                            backgroundColor: "rgba(0, 0, 255, 0.1)",
                            fill: true
                        },
                        {
                            label: "Receptionists",
                            data: data.receptionists.map(num => Math.round(num)),
                            borderColor: "rgba(221, 120, 120, 0.41)",
                            backgroundColor: "rgba(255, 0, 0, 0.1)",
                            fill: true
                        }
                    ]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                precision: 0,
                                autoSkip: false
                            }
                        },
                        x: {
                            type: 'category'
                            // No need for callback here anymore
                        }
                    }
                }
            });
        }


        function renderBarChart(data, filter) {
            const ctx = document.getElementById("barchart").getContext("2d");

            const monthNames = {
                "01": "January",
                "02": "February",
                "03": "March",
                "04": "April",
                "05": "May",
                "06": "June",
                "07": "July",
                "08": "August",
                "09": "September",
                "10": "October",
                "11": "November",
                "12": "December"
            };

            // Convert labels to full month names if year filter is selected
            const chartLabels = (filter === 'year')
                ? data.labels.map(label => monthNames[label] || label)
                : data.labels;

            // Destroy old instance if exists
            if (window.barChartInstance) {
                window.barChartInstance.destroy();
            }

            // Create new chart
            window.barChartInstance = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: "Inventory",
                            data: data.inventory,
                            backgroundColor: "#cfece0"
                        },
                        {
                            label: "Appointments",
                            data: data.appointments,
                            backgroundColor: "#3498db7d"
                        },
                        {
                            label: "Treatments",
                            data: data.treatments,
                            backgroundColor: "#fed9cf"
                        }
                    ]
                },
                options: {
                    scales: {
                        x: {
                            type: 'category'
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                precision: 0,
                                autoSkip: false
                            }
                        }
                    }
                }
            });
        }



        $(document).ready(function () {
            fetchChartData(); // Default load (this year)
            fetchChartDatastaff();
            $('#chartFilter').on('change', function () {
                fetchChartDatastaff($(this).val());
            });
            $('#chartFilter1').on('change', function () {
                fetchChartData($(this).val());
            });
        });








    </script>




@endsection