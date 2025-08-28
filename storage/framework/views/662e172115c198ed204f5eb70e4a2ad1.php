
<?php $__env->startSection('title', config('app.name') . ' || Report Reward'); ?>
<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
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

    #dateRangePicker {
        width: 100%;
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
        height: 35px;
    }

</style>
<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card mb-0">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <h4 class="card-title mb-0">Reward Report</h4>
                        <div id="custom-filter-container">
                            <input type="text" id="dateRangePicker" placeholder="Select Date Range">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="report_table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Type</th>
                                        <th>Name</th>
                                        <th>Village/City</th>
                                        <th>District</th>
                                        <th>Vehicle No</th>
                                        <th>Mobile No</th>
                                        <th>Earn Reward Points</th>
                                        <th>Used Reward Points</th>
                                        <th>Pending Reward Points</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $rewardmanag; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mreward): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($loop->iteration); ?></td>
                                        <td><?php echo e($mreward->type); ?></td>
                                        <td><?php echo e($mreward->name); ?></td>
                                        <td><?php echo e($mreward->village_city); ?></td>
                                        <td><?php echo e($mreward->district); ?></td>
                                        <td><?php echo e($mreward->vehicle_no); ?></td>
                                        <td><?php echo e($mreward->mobile_no); ?></td>
                                        <td><?php echo e($mreward->earned_reward_points); ?></td>
                                        <td><?php echo e($mreward->used_reward_points); ?></td>
                                        <td><?php echo e($mreward->pending_reward_points); ?></td>
                                        <td><?php echo e($mreward->updated_at ? $mreward->updated_at->format('d-m-Y H:i:s') : 'N/A'); ?></td>
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
        $('#report_table').DataTable(tableConfig);
    });

    $('#dateRangePicker').daterangepicker({
        locale: {
            format: 'YYYY-MM-DD'
        }
        , startDate: moment()
        , endDate: moment()
        , autoUpdateInput: true
    }, function(start, end) {
        fetchFilteredData(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
    });

    function fetchFilteredData(startDate, endDate) {
        $.ajax({
            url: "<?php echo e(route('report.reward.filter')); ?>"
            , type: "GET"
            , data: {
                start_date: startDate
                , end_date: endDate
            }
            , success: function(response) {
                console.log(response);
                var table = $('#report_table').DataTable();
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
                        , item.earned_reward_points
                        , item.used_reward_points
                        , item.pending_reward_points
                        , item.updated_at
                    ]).draw(false);
                });
            }
        });
    }

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.main-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/siddhivinayakpump.com/public_html/resources/views/report/reward-report.blade.php ENDPATH**/ ?>