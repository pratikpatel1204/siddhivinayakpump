
<?php $__env->startSection('title', config('app.name') . ' || Reward Management'); ?>
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
                        <h4 class="card-title mb-0">Reward Management</h4>
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
                        <button type="button" class="btn btn-primary" id="clearAll">Clear All</button>
                        <button type="button" class="btn btn-success" id="whatsappBtn">WhatsApp</button>
                        <div class="table-responsive">
                            <table id="red_master" class="table table-striped table-bordered zero-configuration">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>
                                            <input type="checkbox" id="selectAll"> All
                                        </th>
                                        <th>Type</th>
                                        <th>Vehicle No</th>
                                        <th>Mobile No</th>
                                        <th>Earn Reward Points</th>
                                        <th>Used Reward Points</th>
                                        <th>Pending Reward Points</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $rewardmanag; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mreward): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($loop->iteration); ?></td>
                                        <td>
                                            <input type="checkbox" class="rowCheckbox" data-id="<?php echo e($mreward->id); ?>">
                                        </td>
                                        <td><?php echo e($mreward ->type); ?></td>
                                        <td><?php echo e($mreward->vehicle_no); ?></td>
                                        <td><?php echo e($mreward->mobile_no); ?></td>
                                        <td><?php echo e($mreward->earned_reward_points); ?></td>
                                        <td><?php echo e($mreward->used_reward_points); ?></td>
                                        <td><?php echo e($mreward->pending_reward_points); ?></td>
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
        $('#red_master').DataTable({
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
<script>
    $(document).ready(function() {
        // Select all checkboxes when the header checkbox is clicked
        $('#selectAll').on('change', function() {
            $('.rowCheckbox').prop('checked', $(this).prop('checked'));
        });

        // If any individual checkbox is unchecked, uncheck the "Select All" checkbox
        $('.rowCheckbox').on('change', function() {
            if (!$(this).prop('checked')) {
                $('#selectAll').prop('checked', false);
            }

            // If all checkboxes are checked, check the "Select All" checkbox
            if ($('.rowCheckbox:checked').length === $('.rowCheckbox').length) {
                $('#selectAll').prop('checked', true);
            }
        });

        // Clear all checkboxes when the "Clear All" button is clicked
        $('#clearAll').on('click', function() {
            $('.rowCheckbox, #selectAll').prop('checked', false);
        });
    });
</script>
<script>
    $(document).ready(function () {
        $("#whatsappBtn").click(function () {
            let selectedIds = [];

            $(".rowCheckbox:checked").each(function () {
                let id = $(this).data("id");

                // Add only non-null, valid IDs
                if (id) {
                    selectedIds.push(id);
                }
            });

            if (selectedIds.length > 0) {
                $.ajax({
                    url: "<?php echo e(route('send.whatsapp')); ?>", // Correct Laravel route syntax
                    method: "POST",
                    data: { 
                        ids: selectedIds,
                        _token: "<?php echo e(csrf_token()); ?>" // CSRF token for security
                    },
                    success: function (response) {
                        toastr.success("WhatsApp messages sent successfully!", "Success");
                        console.log(response);
                    },
                    error: function (xhr, status, error) {
                        toastr.error("Error sending WhatsApp messages.", "Error");
                        console.error(xhr.responseText);
                    }
                });
            } else {
                toastr.warning("No valid IDs selected.", "Warning");
            }
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.main-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/siddhivinayakpump.com/public_html/resources/views/reward-management/list.blade.php ENDPATH**/ ?>