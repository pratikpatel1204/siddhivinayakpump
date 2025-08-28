
<?php $__env->startSection('title', config('app.name') . ' || Home'); ?>
<?php $__env->startSection('content'); ?>
<div class="content-body">
    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-1">
                    <a href="<?php echo e(route('show.redeem')); ?>" role="button">
                        <div class="card-body">
                            <h3 class="card-title text-white">Rewards Redeem</h3>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-2">
                    <a href="<?php echo e(route('redeem.reward.history')); ?>" role="button">
                        <div class="card-body">
                            <h3 class="card-title text-white">Rewards History</h3>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-3">
                    <a href="<?php echo e(route('add.customer')); ?>" role="button">
                        <div class="card-body">
                            <h3 class="card-title text-white">Create Coustomer</h3>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-4">
                    <a href="<?php echo e(route('show.gift')); ?>" role="button">
                        <div class="card-body">
                            <h3 class="card-title text-white">Gift Redeem</h3>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.main-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\siddhivinayakpump\resources\views/employee-dashboard.blade.php ENDPATH**/ ?>