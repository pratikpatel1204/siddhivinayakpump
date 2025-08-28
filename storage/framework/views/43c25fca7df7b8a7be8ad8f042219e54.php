<?php $__env->startSection('title', config('app.name') . ' || Home'); ?>
<?php $__env->startSection('content'); ?>
<div class="content-body">
    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-1">
                    <div class="card-body">
                        <h3 class="card-title text-white">Total Redeem Reward</h3>
                        <div class="d-inline-block">
                            <h2 class="text-white"><?php echo e($totalUsedPoints); ?></h2>                           
                        </div>
                        <span class="float-right display-5 opacity-5"><i class="fa fa-shopping-cart"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-2">
                    <div class="card-body">
                        <h3 class="card-title text-white">Total Gift Point</h3>
                        <div class="d-inline-block">
                            <h2 class="text-white"><?php echo e($totalGiftPoints); ?></h2>
                        </div>
                        <span class="float-right display-5 opacity-5"><i class="fa fa-money"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-3">
                    <div class="card-body">
                        <h3 class="card-title text-white">Total Customers</h3>
                        <div class="d-inline-block">
                            <h2 class="text-white"><?php echo e($totalCustomers); ?></h2>
                        </div>
                        <span class="float-right display-5 opacity-5"><i class="fa fa-users"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-4">
                    <div class="card-body">
                        <h3 class="card-title text-white">Total Employee</h3>
                        <div class="d-inline-block">
                            <h2 class="text-white"><?php echo e($totalUsers); ?></h2>
                        </div>
                        <span class="float-right display-5 opacity-5"><i class="fa fa-heart"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.main-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/siddhivinayakpump.com/public_html/resources/views/admin-dashboard.blade.php ENDPATH**/ ?>