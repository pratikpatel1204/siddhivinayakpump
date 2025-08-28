<?php $__env->startSection('title', config('app.name') . ' || Redeem Reward History'); ?>
<?php $__env->startSection('content'); ?>
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

</style>
<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card mb-0">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <h4 class="card-title mb-0">Redeem Reward History</h4>
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
                            <table id="red_his" class="table table-striped table-bordered zero-configuration">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Employee Name</th>
                                        <th>Name</th>
                                        <th>Address</th>
                                        <th>Village/City</th>
                                        <th>District</th>
                                        <th>State</th>
                                        <th>Type</th>
                                        <th>Mobile No</th>
                                        <th>Vehicle No</th>
                                        <th>Service</th>
                                        <th>Used Reward Points</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $redeemHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($loop->iteration); ?></td>
                                        <td><?php echo e($history->emp?->name ?? 'N/A'); ?></td>
                                        <td><?php echo e($history->name); ?></td>
                                        <td><?php echo e($history->address); ?></td>
                                        <td><?php echo e($history->village_city); ?></td>
                                        <td><?php echo e($history->district); ?></td>
                                        <td><?php echo e($history->state); ?></td>
                                        <td><?php echo e($history->type); ?></td>
                                        <td><?php echo e($history->mobile_no); ?></td>
                                        <td><?php echo e($history->vehicle_no); ?></td>
                                        <td><?php echo e(ucwords(str_replace('_', ' ', $history->service))); ?></td>
                                        <td><?php echo e($history->used_reward_points); ?></td>
                                        <td><?php echo e($history->created_at->format('d-m-Y H:i:s')); ?></td>
                                        <td><?php echo e($history->updated_at->format('d-m-Y H:i:s')); ?></td>
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
<script>
    $(document).ready(function() {
        $('#red_his').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'excelHtml5'
                    , text: 'Export to Excel'
                    , className: 'btn btn-success'
                }
                , {
                    extend: 'pdfHtml5'
                    , text: 'Export to PDF'
                    , className: 'btn btn-danger'
                }
            ]
        });
    });
</script>
<?php if(session('success')): ?>
<script>
    toastr.success("<?php echo e(session('success')); ?>", "Success");

</script>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.main-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\siddhivinayakpump\resources\views/redeem/reward-history.blade.php ENDPATH**/ ?>