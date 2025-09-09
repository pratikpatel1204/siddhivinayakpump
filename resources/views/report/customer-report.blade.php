@extends('layout.main-layout')
@section('title', config('app.name') . ' || Customer Report')
@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
<script src="https://cdn.jsdelivr.net/npm/moment/min/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.21/jspdf.plugin.autotable.min.js"></script>
<style>
    .dataTables_length select {
        border: 1px solid black;
    }

    .dataTables_filter input {
        border: 1px solid black;
    }

    .dataTables_filter input {
        height: 30px !important;
    }

    .form-control-sm {
        min-height: 30px;
    }

    .form-control {
        height: 30px;
    }

    .table th,
    .table td {
        padding: 5px;
    }

    #rewarddate {
        width: 100%;
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
        height: 35px;
    }

    #results {
        overflow: scroll;
    }

</style>
<div class="content-body mb-3">
    <div class="container-fluid mb-5">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="container">
                            <div class="row align-items-center g-2">
                                <div class="col-md-3 text-md-start text-center mb-2">
                                    <h4 class="card-title mb-0">Customer Report</h4>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-0">
                                        <input type="number" name="number" id="number" class="form-control" placeholder="Enter Mobile No" value="{{ request('number') }}">
                                        <span class="text-danger" id="numberError"></span>
                                    </div>
                                </div>
                                <div class="col-md-6 d-flex flex-column flex-md-row align-items-center gap-2">
                                    <div class="form-group mb-0 w-100">
                                        <input type="text" name="rewarddate" id="rewarddate" class="form-control" placeholder="Select Date Range" value="{{ request('rewarddate') }}">
                                        <span class="text-danger" id="dateError"></span>
                                    </div>
                                    <button type="button" class="btn btn-primary w-100 w-md-auto" id="search">Apply</button>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div id="results"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@if(session('success'))
<script>
    toastr.success("{{ session('success') }}", "Success");

</script>
@endif
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    $(document).ready(function() {
        $('#search').on('click', function() {
            $('#numberError').text('');
            $('#dateError').text('');
            var number = $('#number').val();
            var rewarddate = $('#rewarddate').val();

            if (number == '') {
                $('#numberError').text('Mobile number is required.');
                return;
            }
            if (rewarddate == '') {
                $('#dateError').text('Date range is required.');
                return;
            }

            $.ajax({
                url: "{{ route('search.customer.report') }}", // Replace with your route name
                method: 'POST'
                , data: {
                    number: number
                    , rewarddate: rewarddate
                    , _token: '{{ csrf_token() }}'
                }
                , success: function(response) {
                    toastr.success('Success: ' + response.message);
                    $('#results').empty();
                    if (response.reward_management && response.reward_management.length > 0) {
                        let table = `
                            <div id="pdfHide">
                                <button type="button" onclick="exportToExcel()" class="btn btn-primary">Download Excel</button>
                                <button type="button" onclick="exportToPDF()" class="btn btn-success">Download PDF</button> 
                            </div>  
                            <h4>Reward Management</h4>
                            <table class="table table-bordered data-table" id="table1">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Vehicle No</th>
                                        <th>Mobile No</th>                                        
                                        <th>Earned Points</th>
                                        <th>Used Points</th>
                                        <th>Pending Points</th>                                                                          
                                        <th>Expired Points</th>                                                                         
                                    </tr>
                                </thead>
                                <tbody>
                        `;
                        response.reward_management.forEach(function(item) {
                            table += `
                                <tr>                                   
                                    <td>${item.type}</td>
                                    <td>${item.vehicle_no}</td>
                                    <td>${item.mobile_no}</td>
                                    <td>${item.total_earned_points}</td>
                                    <td>${item.used_reward_points}</td>
                                    <td>${item.pending_reward_points}</td>  
                                    <td>${Math.max(0, item.total_expired_points - item.used_reward_points)}</td>                                  
                                </tr>
                            `;
                        });
                        table += `</tbody></table>`;
                        $('#results').append(table);
                    }
                    if (response.gifts && response.gifts.length > 0) {
                        let table = `
                            <h4>Gift Management</h4>
                            <table class="table table-bordered data-table" id="table2">
                                <thead>
                                    <tr>                                      
                                        <th>Type</th>
                                        <th>Vehicle No</th>
                                        <th>Mobile No</th>                                        
                                        <th>Earned Points</th>
                                        <th>Used Points</th>
                                        <th>Pending Points</th>                                                                           
                                    </tr>
                                </thead>
                                <tbody>
                        `;
                        response.gifts.forEach(function(item) {
                            table += `
                                <tr>                                   
                                    <td>${item.type}</td>
                                    <td>${item.vehicle_no}</td>
                                    <td>${item.mobile_no}</td>
                                    <td>${item.earned_points}</td>
                                    <td>${item.use_points}</td>
                                    <td>${item.pennding_points}</td>                                    
                                </tr>
                            `;
                        });
                        table += `</tbody></table>`;
                        $('#results').append(table);
                    }
                    if (response.customer && response.customer.length > 0) {
                        let cust = `
                            <h4>Customer History</h4>
                            <table class="table table-bordered data-table" id="table3">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Mobile No</th>
                                        <th>Vehicle No</th>
                                        <th>Amount</th>
                                        <th>Date</th>                                       
                                    </tr>
                                </thead>
                                <tbody>
                        `;
                        response.customer.forEach(function(item) {
                            cust += `
                                <tr>
                                    <td>${item.type}</td>
                                    <td>${item.mobile_no}</td>
                                    <td>${item.vehicle_no}</td>
                                    <td>${item.amount}</td>
                                    <td>${item.date_time}</td>
                                </tr>
                            `;
                        });
                        cust += `</tbody></table>`;
                        $('#results').append(cust);
                    }
                    if (response.redeem_history && response.redeem_history.length > 0) {
                        let redeemTable = `
                            <h4>Redeem History</h4>
                            <table class="table table-bordered data-table" id="table3">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Mobile No</th>
                                        <th>Vehicle No</th>
                                        <th>Type</th>
                                        <th>Service</th>
                                        <th>Used Points</th>
                                        <th>Name</th>
                                        <th>Address</th>
                                        <th>Village/City</th>
                                        <th>District</th>
                                        <th>State</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;
                        response.redeem_history.forEach(function(item) {
                            redeemTable += `
                                <tr>
                                    <td>${item.id}</td>
                                    <td>${item.mobile_no}</td>
                                    <td>${item.vehicle_no}</td>
                                    <td>${item.type}</td>
                                    <td>${item.service}</td>
                                    <td>${item.used_reward_points}</td>
                                    <td>${item.name}</td>
                                    <td>${item.address}</td>
                                    <td>${item.village_city}</td>
                                    <td>${item.district}</td>
                                    <td>${item.state}</td>
                                    <td>${new Date(item.updated_at).toLocaleString()}</td>
                                </tr>
                            `;
                        });
                        redeemTable += `</tbody></table>`;
                        $('#results').append(redeemTable);
                    }
                    if (response.gift_history && response.gift_history.length > 0) {
                        let giftTable = `
                            <h4>Gift History</h4>
                            <table class="table table-bordered data-table" id="table4">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Mobile No</th>
                                        <th>Vehicle No</th>
                                        <th>Type</th>
                                        <th>Used Points</th>
                                        <th>Name</th>
                                        <th>Address</th>
                                        <th>Village/City</th>
                                        <th>District</th>
                                        <th>State</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;
                        response.gift_history.forEach(function(item) {
                            giftTable += `
                                <tr>
                                    <td>${item.id}</td>
                                    <td>${item.mobile_no}</td>
                                    <td>${item.vehicle_no}</td>
                                    <td>${item.type}</td>
                                    <td>${item.used_reward_points}</td>
                                    <td>${item.name}</td>
                                    <td>${item.address}</td>
                                    <td>${item.village_city}</td>
                                    <td>${item.district}</td>
                                    <td>${item.state}</td>
                                    <td>${new Date(item.updated_at).toLocaleString()}</td>
                                </tr>
                            `;
                        });

                        giftTable += `</tbody></table>`;
                        $('#results').append(giftTable);
                    }
                    if ((!response.reward_management || response.reward_management.length === 0) &&
                        (!response.redeem_history || response.redeem_history.length === 0) &&
                        (!response.gift_history || response.gift_history.length === 0)) {
                        $('#results').append('<p>No data found for the specified mobile number and date range.</p>');
                    }
                }
                , error: function(xhr) {
                    var response = xhr.responseJSON;
                    if (response && response.error) {
                        toastr.error(response.error); // Show error message using Toastr
                    } else if (xhr.status === 404) {
                        toastr.error('No data found for the specified mobile number and date range.'); // Show not found error
                    } else {
                        toastr.error('An unexpected error occurred. Please try again later.'); // Show general error
                    }
                }
            });
        });
    });

</script>
<script>
    $('#rewarddate').daterangepicker({
        singleDatePicker: false, // Enables selection of only a single date
        locale: {
            format: 'YYYY-MM-DD'
        }
        ,startDate: moment("2024-02-28", "YYYY-MM-DD"), // Set the start date to today
        endDate: moment(), // Set the end date to today
        autoUpdateInput: true // Automatically updates the input field with the selected date
    });
    $('#giftdate').daterangepicker({
        singleDatePicker: false, // Enables selection of only a single date
        locale: {
            format: 'YYYY-MM-DD'
        }
        , startDate: moment(), // Set the start date to today
        endDate: moment(), // Set the end date to today
        autoUpdateInput: true // Automatically updates the input field with the selected date
    });

</script>
<script>
    function exportToExcel() {
        let wb = XLSX.utils.book_new();
        let exportDiv = document.getElementById('results');
        let tables = exportDiv.getElementsByTagName('table');
        let allData = [];
        for (let i = 0; i < tables.length; i++) {
            let tableData = XLSX.utils.sheet_to_json(XLSX.utils.table_to_sheet(tables[i]), {
                header: 1
            });
            if (i > 0) {
                allData.push([""]);
            }
            allData = allData.concat(tableData);
        }
        let ws = XLSX.utils.aoa_to_sheet(allData);
        XLSX.utils.book_append_sheet(wb, ws, "All Tables");
        XLSX.writeFile(wb, "merged_tables.xlsx");
    }

    function exportToPDF() {
    const { jsPDF } = window.jspdf;
    let doc = new jsPDF('l', 'mm', 'a4'); // Landscape mode for wide tables

    document.getElementById('pdfHide').style.display = 'none'; // Hide buttons before export

    let tables = document.querySelectorAll('#results table'); // Select all tables inside the div
    let finalY = 10; // Initial Y position in PDF

    tables.forEach((table, index) => {
        doc.autoTable({
            html: table, // Auto extract table data
            startY: finalY,
            theme: 'grid',
            styles: { fontSize: 8, cellWidth: 'auto' }, // Adjust text size and column width
            headStyles: { fillColor: [0, 122, 204] }, // Header styling
            margin: { left: 5, right: 5 }
        });

        finalY = doc.lastAutoTable.finalY + 10; // Move to next table position
        if (finalY > 180) { // If it exceeds the page, add a new page
            doc.addPage();
            finalY = 10;
        }
    });

    doc.save("exported_tables.pdf");
    document.getElementById('pdfHide').style.display = 'block'; // Show buttons again
}


</script>
@endsection