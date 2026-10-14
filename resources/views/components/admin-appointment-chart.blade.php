<div class="chart-container">

    <canvas id="adminAppointmentChart" width="600" height="400"></canvas>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function () {
    const ctx = document.getElementById('adminAppointmentChart').getContext('2d');

    $.ajax({
        url: '/api/doctors-patients',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            const labels = response.statuses.map(status => 
                status.charAt(0).toUpperCase() + status.slice(1)
            );

            const data = response.counts;
            data[3] = 5;
console.log(data, labels)
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Appointment Status Count',
                        data: data,
                        backgroundColor: '#007bff',
                        borderRadius: 8,
                        barThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                // ✅ This ensures only whole numbers appear
                                stepSize: 1,
                                precision: 0,
                                callback: function(value) {
                                    return Number.isInteger(value) ? value : '';
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        },
        error: function (xhr, status, error) {
            console.error('Error fetching data:', error);
        }
    });
});
</script>
