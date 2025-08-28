
<?php $__env->startSection('title', config('app.name') . ' || Add Customer'); ?>
<?php $__env->startSection('content'); ?>
<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Add Customer</h4>
                        <div class="basic-form">
                            <form id="editrateForm" action="<?php echo e(route('save.customer')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="trx_id">Trx ID</label>
                                        <input type="text" class="form-control" id="trx_id" name="trx_id" value="<?php echo e(old('trx_id')); ?>" placeholder="Enter Trx ID" required>
                                        <?php $__errorArgs = ['trx_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <small class="text-danger"><?php echo e($message); ?></small>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="type">Type</label>
                                        <select class="form-control" id="type" name="type" required>
                                            <option value="">Select Type</option>
                                            <option value="Regular" <?php echo e(old('type') == 'Regular' ? 'selected' : ''); ?>>Regular</option>
                                            <option value="Commercial" <?php echo e(old('type') == 'Commercial' ? 'selected' : ''); ?>>Commercial</option>
                                            <option value="Tractor" <?php echo e(old('type') == 'Tractor' ? 'selected' : ''); ?>>Tractor</option>
                                        </select>
                                        <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <small class="text-danger"><?php echo e($message); ?></small>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="date_time">Date Time</label>
                                        <input type="datetime-local" class="form-control" id="date_time" name="date_time" value="<?php echo e(old('date_time')); ?>" required>
                                        <?php $__errorArgs = ['date_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <small class="text-danger"><?php echo e($message); ?></small>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="pump">Pump</label>
                                        <input type="number" class="form-control" id="pump" name="pump" value="<?php echo e(old('pump')); ?>" placeholder="Enter Pump" required>
                                        <?php $__errorArgs = ['pump'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <small class="text-danger"><?php echo e($message); ?></small>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="rdb_nozzle">RDB Nozzle</label>
                                        <input type="number" class="form-control" id="rdb_nozzle" name="rdb_nozzle" value="<?php echo e(old('rdb_nozzle')); ?>" placeholder="Enter RDB Nozzle" required>
                                        <?php $__errorArgs = ['rdb_nozzle'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <small class="text-danger"><?php echo e($message); ?></small>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="product">Product</label>
                                        <input type="text" class="form-control" id="product" name="product" value="<?php echo e(old('product')); ?>" placeholder="Enter Product" required>
                                        <?php $__errorArgs = ['product'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <small class="text-danger"><?php echo e($message); ?></small>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="unit_price">Unit Price</label>
                                        <input type="number" step="0.01" class="form-control" id="unit_price" name="unit_price" value="<?php echo e(old('unit_price')); ?>" placeholder="Enter Unit Price" required>
                                        <?php $__errorArgs = ['unit_price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <small class="text-danger"><?php echo e($message); ?></small>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="payment">Payment</label>
                                        <input type="text" class="form-control" id="payment" name="payment" value="<?php echo e(old('payment')); ?>" placeholder="Enter Payment Method" required>
                                        <?php $__errorArgs = ['payment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <small class="text-danger"><?php echo e($message); ?></small>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="volume">Volume</label>
                                        <input type="number" step="0.01" class="form-control" id="volume" name="volume" value="<?php echo e(old('volume')); ?>" placeholder="Enter Volume" required>
                                        <?php $__errorArgs = ['volume'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <small class="text-danger"><?php echo e($message); ?></small>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="amount">Amount</label>
                                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="<?php echo e(old('amount')); ?>" placeholder="Enter Amount" required>
                                        <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <small class="text-danger"><?php echo e($message); ?></small>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="print_id">Print ID</label>
                                        <input type="text" class="form-control" id="print_id" name="print_id" value="<?php echo e(old('print_id')); ?>" placeholder="Enter Print ID" required>
                                        <?php $__errorArgs = ['print_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <small class="text-danger"><?php echo e($message); ?></small>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="vehicle_no">Vehicle No</label>
                                        <input type="text" class="form-control" id="vehicle_no" name="vehicle_no" value="<?php echo e(old('vehicle_no')); ?>" placeholder="Enter Vehicle No" required>
                                        <?php $__errorArgs = ['vehicle_no'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <small class="text-danger"><?php echo e($message); ?></small>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="mobile_no">Mobile No</label>
                                        <input type="text" class="form-control" id="mobile_no" name="mobile_no" value="<?php echo e(old('mobile_no')); ?>" placeholder="Enter Mobile No" required>
                                        <?php $__errorArgs = ['mobile_no'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <small class="text-danger"><?php echo e($message); ?></small>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>                                   
                                </div>
                                <button type="submit" class="btn btn-dark">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if(session('success')): ?>
<script>
    toastr.success("<?php echo e(session('success')); ?>", "Success", {
        closeButton: true
        , progressBar: true
    });

</script>
<?php endif; ?>
<?php if(session('error')): ?>
<script>
    toastr.error("<?php echo e(session('error')); ?>", "Error", {
        closeButton: true
        , progressBar: true
    });

</script>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.main-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\siddhivinayakpump\resources\views/customer-master/add.blade.php ENDPATH**/ ?>