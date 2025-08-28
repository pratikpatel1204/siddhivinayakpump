
<?php $__env->startSection('title', config('app.name') . ' || expired-reward-point-list'); ?>
<?php $__env->startSection('content'); ?>
<style>
    .dataTables_length select {
        border: 1px solid black;
    }
    .dataTables_filter input {
        border: 1px solid black;
        height: 30px !important;
    }
    .form-control-sm { min-height: 30px; }
    .form-control { height: 30px; }
</style>

<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card mb-0">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <h4 class="card-title mb-0">Expired Reward Point List</h4>                      
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
                                        <th>Customer Type</th>                                        
                                        <th>Vehicle No</th>
                                        <th>Mobile No</th>
                                        <th>Expired Points</th>
                                        <th>Reward Created</th>
                                        <th>Reward Expired</th>
                                        <th>Expired On</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $expiredRewards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($loop->iteration); ?></td>
                                        <td><?php echo e($row->customer->type ?? '-'); ?></td>                                      
                                        <td><?php echo e($row->customer->vehicle_no ?? '-'); ?></td>
                                        <td><?php echo e($row->customer->mobile_no ?? '-'); ?></td>
                                        <td><?php echo e($row->expired_points); ?></td>
                                        <td><?php echo e($row->reward_created_date); ?></td>
                                        <td><?php echo e($row->reward_expired_date); ?></td>
                                        <td><?php echo e($row->expired_on); ?></td>
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

<script>
    $(document).ready(function() {
        var tableConfig = {
            searching: true,
            ordering: true,
            paging: true,
            info: true,
            dom: 'Bfrtip',
            buttons: [
                { extend: 'excelHtml5', text: 'Export to Excel', className: 'btn btn-success' },
                { extend: 'pdfHtml5', text: 'Export to PDF', className: 'btn btn-danger' }
            ]
        };
        $('#report_table').DataTable(tableConfig);
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.main-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\siddhivinayakpump\resources\views/report/expired-reward-point-list.blade.php ENDPATH**/ ?>