<?php $__env->startSection('title', config('app.name') . ' || All Report'); ?>
<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
<script src="https://cdn.jsdelivr.net/npm/moment/min/moment.min.js"></script>
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

</style>
<div class="content-body mb-3">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="align-items-center d-flex justify-content-between">
                            <div>
                                <h4 class="card-title mb-0 flex-grow-1">Reward Report</h4>
                            </div>
                            <div class="d-flex">
                                <input type="text" id="rewarddate" placeholder="Select Date Range">
                                <button type="button" class="btn btn-primary" id="applyFilter">Apply</button>                                
                            </div>                            
                            <div class="form-group mb-0">
                                <select name="employee" id="employee" class="form-control">
                                    <option value="0">All</option>
                                    <?php $__currentLoopData = $employee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($emp->id); ?>"><?php echo e($emp->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-striped" id="reward_table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Type</th>
                                        <th>Name</th>
                                        <th>Village/City</th>
                                        <th>District</th>
                                        <th>Vehicle No</th>
                                        <th>Mobile No</th>
                                        <th>Service</th>
                                        <th>Used Reward Points</th>
                                        <th>Employee Name</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $redeemHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($loop->iteration); ?></td>
                                        <td><?php echo e($history->type); ?></td>
                                        <td><?php echo e($history->name); ?></td>
                                        <td><?php echo e($history->village_city); ?></td>
                                        <td><?php echo e($history->district); ?></td>
                                        <td><?php echo e($history->vehicle_no); ?></td>
                                        <td><?php echo e($history->mobile_no); ?></td>
                                        <td><?php echo e($history->service); ?></td>
                                        <td><?php echo e($history->used_reward_points); ?></td>
                                        <td><?php echo e($history->emp?->name ?? 'N/A'); ?></td>
                                        <td><?php echo e($history->updated_at); ?></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid mb-5">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="align-items-center d-flex justify-content-between">
                            <div>
                                <h4 class="card-title mb-0 flex-grow-1">Gift Report</h4>
                            </div>
                            <div class="d-flex">
                                <input type="text" id="giftdate" placeholder="Select Date Range">
                                <button type="button" class="btn btn-primary" id="applyFiltergift">Apply</button>                                
                            </div>                            
                            <div class="form-group mb-0">
                                <select name="giftemployees" id="giftemployees" class="form-control">
                                    <option value="0">All</option>
                                    <?php $__currentLoopData = $employee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($emp->id); ?>"><?php echo e($emp->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <hr>                       
                        <div class="table-responsive">
                            <table class="table table-striped" id="gift_table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Type</th>
                                        <th>Name</th>
                                        <th>Village/City</th>
                                        <th>District</th>
                                        <th>Vehicle No</th>
                                        <th>Mobile No</th>
                                        <th>Used Reward Points</th>
                                        <th>Employee Name</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $giftHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ghistory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($loop->iteration); ?></td>
                                        <td><?php echo e($ghistory->type); ?></td>
                                        <td><?php echo e($ghistory->name); ?></td>
                                        <td><?php echo e($ghistory->village_city); ?></td>
                                        <td><?php echo e($ghistory->district); ?></td>
                                        <td><?php echo e($ghistory->vehicle_no); ?></td>
                                        <td><?php echo e($ghistory->mobile_no); ?></td>
                                        <td><?php echo e($ghistory->used_reward_points); ?></td>
                                        <td><?php echo e($ghistory->emp->name); ?></td>
                                        <td><?php echo e($ghistory->updated_at); ?></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if(session('success')): ?>
<script>
    toastr.success("<?php echo e(session('success')); ?>", "Success");

</script>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    $(document).ready(function() {
    // Common configuration for both tables
        var tableConfig = {
            searching: true,
            ordering: true,
            paging: true,
            info: true,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export to Excel',
                    className: 'btn btn-success'
                },
                {
                    extend: 'pdfHtml5',
                    text: 'Export to PDF',
                    className: 'btn btn-danger'
                }
            ]
        };
    
        // Initialize DataTable for reward_table
        $('#reward_table').DataTable(tableConfig);
    
        // Initialize DataTable for gift_table
        $('#gift_table').DataTable(tableConfig);
    });

    $('#rewarddate').daterangepicker({
        singleDatePicker: false, // Enables selection of only a single date
        locale: {
            format: 'YYYY-MM-DD'
        }
        , startDate: moment(), // Set the start date to today
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
    $(document).ready(function() {
        // Function to trigger AJAX call
        function fetchFilteredData() {
            var selectedDate = $('#rewarddate').val();
            var selectedemployee = $('#employee').val();
            var dateRange = selectedDate.split(' - ');
            var startDate = dateRange[0];
            var endDate = dateRange[1];
            $.ajax({
                url: "<?php echo e(route('all.report.reward.filter')); ?>"
                , type: 'GET'
                , data: {
                    date: selectedDate
                    , employee: selectedemployee
                    , startDate: startDate
                    , endDate: endDate
                }
                , success: function(response) {
                    console.log(response)
                    var table = $('#reward_table').DataTable();
                    table.clear().draw();
                    response.data.forEach(function(item, index) {
                        table.row.add([
                            index + 1
                            , item.type
                            , item.name
                            , item.village_city
                            , item.district
                            , item.vehicle_no
                            , item.mobile_no
                            , item.service
                            , item.used_reward_points
                            , item.emp.name
                            , item.updated_at
                        ]).draw(false);
                    });
                }
                , error: function(xhr) {
                    console.error('Error fetching data:', xhr);
                }
            });
        }
        // fetchFilteredData();
        $('#applyFilter').on('click', function() {
            fetchFilteredData();
        });
        $('#employee').on('change', function() {
            fetchFilteredData();
        });
    });

</script>
<script>
    $(document).ready(function() {
        // Function to trigger AJAX call
        function fetchFilteredgift() {
            var selectedDateg = $('#giftdate').val();
            var selectedemployee = $('#giftemployees').val();
            var dateRangeg = selectedDateg.split(' - ');
            var startDateg = dateRangeg[0];
            var endDateg = dateRangeg[1];
            $.ajax({
                url: "<?php echo e(route('all.report.gift.filter')); ?>"
                , type: 'GET'
                , data: {
                    date: selectedDateg
                    , employee: selectedemployee
                    , startDate: startDateg
                    , endDate: endDateg
                }
                , success: function(response) {
                    console.log(response)
                    var gtable = $('#gift_table').DataTable();
                    gtable.clear().draw();
                    response.data.forEach(function(item, index) {
                        gtable.row.add([
                            index + 1
                            , item.type
                            , item.name
                            , item.village_city
                            , item.district
                            , item.vehicle_no
                            , item.mobile_no
                            , item.used_reward_points
                            , item.emp.name
                            , item.updated_at
                        ]).draw(false);
                    });
                }
                , error: function(xhr) {
                    console.error('Error fetching data:', xhr);
                }
            });
        }
        // fetchFilteredgift();
        $('#applyFiltergift').on('click', function() {
            fetchFilteredgift();
        });
        $('#giftemployees').on('change', function() {
            fetchFilteredgift();
        });
    });

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.main-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\siddhivinayakpump\resources\views/report/all-report.blade.php ENDPATH**/ ?>