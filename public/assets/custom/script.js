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

    // Bar chart onsite

    $.get('/get-item-data-for-stats', function (res) {

        let onsiteItems = [];
        let onsiteCounts = [];
    
        let damagedItems = [];
        let damagedCounts = [];
    
        let onsiteAggregated = {}; // Object to store aggregated onsite items
        let damagedAggregated = {}; // Object to store aggregated damaged items
    
        // Ensure res.final exists and is an array
        if (Array.isArray(res.final)) {
            res.final.forEach(element => {
                // Loop through onsite items if they exist
                if (Array.isArray(element.onsites)) {
                    element.onsites.forEach(onsite => {
                        if (onsiteAggregated[onsite.name]) {
                            onsiteAggregated[onsite.name] += onsite.count;  // Add count if item already exists
                        } else {
                            onsiteAggregated[onsite.name] = onsite.count;  // Otherwise, initialize it
                        }
                    });
                }
    
                // Loop through damage items if they exist
                if (Array.isArray(element.damages)) {
                    element.damages.forEach(damage => {
                        if (damagedAggregated[damage.name]) {
                            damagedAggregated[damage.name] += damage.count;  // Add count if item already exists
                        } else {
                            damagedAggregated[damage.name] = damage.count;  // Otherwise, initialize it
                        }
                    });
                }
            });
        }
    
        // Now convert the aggregated data into arrays for the chart
        onsiteItems = Object.keys(onsiteAggregated);
        onsiteCounts = Object.values(onsiteAggregated);
    
        damagedItems = Object.keys(damagedAggregated);
        damagedCounts = Object.values(damagedAggregated);
    
        // Onsite Bar Chart
        var barChartOnsiteOptions = {
            chart: {
                type: 'bar',
                height: 300
            },
            colors: ['#35A9DA'],
            series: [{
                name: 'sales',
                data: onsiteCounts
            }],
            xaxis: {
                categories: onsiteItems
            }
        }
    
        var barChartOnsite = new ApexCharts(document.querySelector("#barChartOnsite"), barChartOnsiteOptions);
        barChartOnsite.render();
    
        // Damages Bar Chart
        var barChartDamagesOptions = {
            chart: {
                type: 'bar',
                height: 300
            },
            colors: ['#FF1654'],
            series: [{
                name: 'sales',
                data: damagedCounts
            }],
            xaxis: {
                categories: damagedItems
            }
        }
    
        var barChartDamages = new ApexCharts(document.querySelector("#barChartDamages"), barChartDamagesOptions);
        barChartDamages.render();
    });
    

    const searchBox = document.getElementById('searchBox');
    const mapIframe = document.getElementById('googleMap');

    // Function to update the iframe src with the searched place
    function updateMapSearch(query) {
        const baseURL = "https://www.google.com/maps/embed/v1/place?q=";
        const apiKey = "AIzaSyBJyFU3OF64Fn1tPHkP37DifH4V0uhuU8w"; // Replace with your Google Maps API Key
        const newURL = `${baseURL}${encodeURIComponent(query)}&key=${apiKey}`;
        mapIframe.src = newURL;

        // Add To Inout

        $('.mapCoordinatesData').val(newURL);
    }

    $('.searchBoxInput').on('keyup', function () {
        const query = searchBox.value.trim();
        if (query) {
            updateMapSearch(query);
        }
    })

    // serial numbers

    $.get('/get-item-serial-numbers', function (res) {
        
        // Dummy data for serial numbers (you can fetch this data from a server if needed)
var serialNumbers = res.serial_numbers;

// Function to update the quantity input field
function updateQuantity() {
    var quantity = $('#tagContainer .badge').length;  // Get the number of tags
    $('#quantity').val(quantity);  // Set the quantity input to the number of tags
}

// Function to update the hidden input field with the tags
function updateSerialNumbers() {
    var tags = [];
    $('#tagContainer .badge').each(function () {
        tags.push($(this).text());
    });
    $('#serial_numbers').val(tags.join(', '));
}

// Function to check if the serial number is already added
function isSerialNumberDuplicate(serialNumber) {
    var existingSerials = [];
    $('#tagContainer .badge').each(function() {
        existingSerials.push($(this).text());
    });
    return existingSerials.includes(serialNumber);
}

$('#tagInput').on('input', function() {
    var searchTerm = $(this).val().toLowerCase();
    var filteredSerials = serialNumbers.filter(function(serial) {
        return serial.toLowerCase().includes(searchTerm);
    });

    $('#serialDropdown').empty();

    if (filteredSerials.length > 0) {
        filteredSerials.forEach(function(serial) {
            $('#serialDropdown').append('<li><a class="dropdown-item" href="#">' + serial + '</a></li>');
        });
        $('#serialDropdown').show();
    } else {
        $('#serialDropdown').hide(); // Hide if no matches
    }
});

// Handle serial number selection from the dropdown
$(document).on('click', '#serialDropdown .dropdown-item', function() {
    var serialNumber = $(this).text();

    // Check if the serial number is already added
    if (isSerialNumberDuplicate(serialNumber)) {
        alert("Serial number already chosen.");
        return;
    }

    // Add the serial number as a tag
    var tagElement = $('<span class="badge bg-success me-2 mb-2" style="cursor: pointer;"></span>').text(serialNumber);
    
    $('#tagContainer').append(tagElement);
    updateSerialNumbers();
    updateQuantity();
    $('#tagInput').val('');
    $('#serialDropdown').hide();
});

// Add serial number manually when clicking 'Add Serial Number'
$('#addTagBtn').click(function () {
    var tag = $('#tagInput').val().trim();

    // Check if the input field is not empty and the serial number isn't already chosen
    if (tag !== "") {
        if (isSerialNumberDuplicate(tag)) {
            alert("Serial number already chosen.");
            return;
        }

        // Create a new tag element
        var tagElement = $('<span class="badge bg-success me-2 mb-2" style="cursor: pointer;"></span>').text(tag);
        
        // Append the tag to the tag container
        $('#tagContainer').append(tagElement);
        updateSerialNumbers();
        updateQuantity();
        $('#tagInput').val('');
    }
});

// Optionally: Allow clicking on a tag to remove it
$(document).on('click', '.badge', function () {
    $(this).remove(); // Remove the clicked tag
    updateSerialNumbers();
    updateQuantity();
});

    })
    
    // Toggle serial number ===================================================================================

    // Initially disable the quantity field if serial numbers are provided
    toggleQuantityField();

    // Toggle between "Has Serial Number" and "No Serial Number"
    $('#serialNumberToggle').change(function () {
        toggleQuantityField();
    });

    $('#serialNumberToggle').is(':checked')

    function toggleQuantityField() {
        var hasSerialNumbers = $('#serialNumberToggle').is(':checked'); // Check if the toggle is on
        
        if (hasSerialNumbers) {
            // If serial numbers are present, make quantity input readonly
            $('#quantity').prop('readonly', true);
            $('#serialNumberLabel').text('Has Serial Number');
            $('.serialNumberCustomToggle').slideDown();
        } else {
            // If no serial numbers, allow input in quantity
            $('#quantity').prop('readonly', false);
            $('#serialNumberLabel').text('No Serial Number');
            $('.serialNumberCustomToggle').slideUp();
        }
    }

})
