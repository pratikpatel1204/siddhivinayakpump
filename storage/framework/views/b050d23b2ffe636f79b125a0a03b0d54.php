
<?php $__env->startSection('title', config('app.name') . ' || Employee List'); ?>
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
                        <h4 class="card-title mb-0">Employee List</h4>
                        <a href="<?php echo e(route('add.employee')); ?>" class="btn btn-primary" role="button"><i class="fas fa-plus"></i> Employee</a>
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
                            <table class="table table-striped table-bordered zero-configuration">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Password</th>
                                        <th>Image</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($loop->iteration); ?></td>
                                        <td><?php echo e($user->name); ?></td>
                                        <td><?php echo e($user->email); ?></td>
                                        <td><?php echo e($user->mobile); ?></td>
                                        <td><?php echo e($user->showpassword); ?></td>
                                        <td>
                                            <img src="<?php echo e(asset($user->profile_image)); ?>" alt="Profile Image" width="50">
                                        </td>
                                        <td>
                                            <a href="<?php echo e(route('edit.employee', ['id' => Crypt::encryptString($user->id)])); ?>" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="javascript:void(0);" class="btn btn-danger btn-sm delete-btn" data-id="<?php echo e($user->id); ?>" role="button">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
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
        $('.delete-btn').on('click', function() {
            var employeeId = $(this).data('id'); 
            if (confirm('Are you sure you want to delete this employee?')) {
                $.ajax({
                    url: '<?php echo e(route("destroy.employee")); ?>',
                    type: 'post'
                    , data: {
                        _token: '<?php echo e(csrf_token()); ?>', 
                        id:employeeId,
                    }, 
                    success: function(response) {
                        toastr.success(response.message);
                        $('#employee-' + employeeId).remove();
                        window.location.reload();
                    }, error: function(xhr, status, error) {
                        toastr.error('Error deleting employee');
                    }
                });
            }
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.main-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\siddhivinayakpump\resources\views/employee/list.blade.php ENDPATH**/ ?>