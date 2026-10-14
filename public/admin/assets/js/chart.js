$(document).ready(function() {
	
	// Bar Chart



	// Line Chart

	// var lineChartData = {
	// 	labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
	// 	datasets: [{
	// 		label: "My First dataset",
	// 		backgroundColor: "rgba(248, 152, 132, 0.5)",
	// 		data: [100, 70, 20, 100, 60, 50, 70, 50, 50, 100, 50, 90]
	// 	}, {
	// 	label: "My Second dataset",
	// 	backgroundColor: "rgba(135, 206, 176,0.5)",
	// 	fill: true,
	// 	data: [28, 48, 40, 19, 86, 27, 20, 90, 50, 20, 90, 20]
	// 	}]
	// };
	// // rgba(135, 206, 176,0.5)",
	// // rgba(248, 152, 132, 0.5)",
	// var linectx = document.getElementById('linegraph').getContext('2d');
	// window.myLine = new Chart(linectx, {
	// 	type: 'line',
	// 	data: lineChartData,
	// 	options: {
	// 		responsive: true,
	// 		legend: {
	// 			display: false,
	// 		},
	// 		tooltips: {
	// 			mode: 'index',
	// 			intersect: false,
	// 		}
	// 	}
	// });
	// var barChartData = {
	// 	labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
	// 	datasets: [{
	// 		label: "Recommend",
	// 		backgroundColor: "rgba(248, 152, 132, 0.5)", // Light Red
	// 		borderRadius: 10, // Rounded bars
	// 		data: [95, 70, 20, 40, 60, 50, 70, 50, 50, 60, 50, 90]
	// 	}, {
	// 		label: "New Patient",
	// 		backgroundColor: "rgba(135, 206, 176, 0.5)", // Light Green
	// 		borderRadius: 10, // Rounded bars
	// 		data: [28, 48, 40, 19, 86, 27, 20, 90, 50, 20, 90, 20]
	// 	}]
	// };
	
	// var ctx = document.getElementById('bargraph').getContext('2d');
	// new Chart(ctx, {
	// 	type: 'bar',
	// 	data: barChartData,
	// 	options: {
	// 		responsive: true,
	// 		plugins: {
	// 			legend: {
	// 				display: true,
	// 				position: 'top'
	// 			},
	// 			tooltip: {
	// 				mode: 'index',
	// 				intersect: false
	// 			}
	// 		},
	// 		scales: {
	// 			x: {
	// 				stacked: true
	// 			},
	// 			y: {
	// 				stacked: true
	// 			}
	// 		}
	// 	}
	// });
	
	// // Bar Chart 2
	
    // barChart();
    
    $(window).resize(function(){
        barChart();
    });
    
    function barChart(){
        $('.bar-chart').find('.item-progress').each(function(){
            var itemProgress = $(this),
            itemProgressWidth = $(this).parent().width() * ($(this).data('percent') / 100);
            itemProgress.css('width', itemProgressWidth);
        });
    };
});