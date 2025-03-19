$(document).ready(function () {

    $.get('/get-item-data-for-graph', function (res) {
        var pieChartItemsOverviewOptions = {
            chart: {
                type: 'pie', // Set the chart type to pie
            },
            colors: ["#247BA0", "#FFD700", "#FF1654"], // Customize the colors
            series: [
                res.inWarehousesCount,
                res.onSitesCount,
                res.damagesCount
            ], // Pie chart data (as an array of numbers)
            labels: ['In Warehouse', 'On Site', 'Damages'], // Labels for the slices
            title: {
                text: 'Items',
                align: 'center'
            },
            tooltip: {
                y: {
                    formatter: function (value) {
                        return value + ' items'; // Customize the tooltip to show the value with "units"
                    }
                }
            }
        };
        
        // Create the pie chart
        var pieChartItems = new ApexCharts(document.querySelector("#pie-chart-item-overview"), pieChartItemsOverviewOptions);
        pieChartItems.render();
    })

    var lineChartOptions = {
        chart: {
            type: 'line',
        },
        colors: ["#FF1654", "#247BA0"],
        series: [
            {
                name: 'sales',
                data: [30,40,35,50,49,60,70,91,125]
            },
            {
                name: 'sales 1',
                data: [20,31,35,66,77,34,78,77,100]
            }
        ],
        xaxis: {
            categories: [1991,1992,1993,1994,1995,1996,1997, 1998,1999]
        }
    }

    var lineChart = new ApexCharts(document.querySelector("#lineChart"), lineChartOptions);

    lineChart.render();

    var barChartOptions = {
        chart: {
            type: 'bar'
        },
        series: [{
            name: 'sales',
            data: [30,40,35,50,49,60,70,91,125]
        }],
        xaxis: {
            categories: [1991,1992,1993,1994,1995,1996,1997, 1998,1999]
        }
    }

    var barChart = new ApexCharts(document.querySelector("#barChart"), barChartOptions);

    barChart.render();

    // search filter

    $(".selectTechnicianSearchBar").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $(".selectTechnicianSearchBarResult").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
})
