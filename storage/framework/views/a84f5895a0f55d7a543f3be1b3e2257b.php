
<?php $__env->startSection('title', config('app.name') . ' || Create Rate'); ?>
<?php $__env->startSection('content'); ?>
<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Create Rate</h4>
                        <div class="basic-form">
                            <form id="customerUploadForm" enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>
                                <div class="form-group">
                                    <label for="customerFile">Upload Customer Excel File</label>
                                    <input type="file" class="form-control-file" id="customerFile" name="customerFile" accept=".xlsx,.xls,.csv">
                                    <small class="text-danger" id="customerFileError"></small>
                                </div>
                                <button type="submit" class="btn btn-dark" id="submitButton">
                                    <span id="spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display:none;"></span>
                                    Save
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        function clearError(inputId) {
            $(`#${inputId}`).on('change', function() {
                $(`#${inputId}Error`).text('');
            });
        }

        clearError('customerFile');

        $('#customerUploadForm').on('submit', function(event) {
            event.preventDefault();
            $('.text-danger').text(''); // Clear existing error messages
            let isValid = true;
            const customerFile = $('#customerFile').prop('files')[0];

            if (!customerFile) {
                $('#customerFileError').text('Excel file is required.');
                isValid = false;
            } else {
                const allowedExtensions = ['xlsx', 'xls', 'csv'];
                const fileExtension = customerFile.name.split('.').pop().toLowerCase();

                if (!allowedExtensions.includes(fileExtension)) {
                    $('#customerFileError').text('Invalid file type. Please upload an Excel file (.xlsx, .xls, .csv).');
                    isValid = false;
                }
            }

            if (!isValid) {
                toastr.error('Please select a valid Excel file.');
                return;
            }

            const formData = new FormData();
            formData.append('customerFile', customerFile);
            formData.append('_token', "<?php echo e(csrf_token()); ?>");

            // Show loader and disable submit button
            $('#submitButton').prop('disabled', true);
            $('#spinner').show(); // Show spinner

            $.ajax({
                url: '<?php echo e(route("store.customer")); ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    toastr.success('Excel file uploaded successfully!');
                    window.location.href = '/RMS/customer-list';
                },
                error: function(xhr) {
                    const errors = (xhr.responseJSON && xhr.responseJSON.errors) || {};
                    for (const [field, messages] of Object.entries(errors)) {
                        $(`#${field}Error`).text(messages[0]);
                    }
                    toastr.error(xhr.responseJSON ? xhr.responseJSON.message : 'File upload failed.');
                },
                complete: function() {
                    // Hide loader and enable submit button
                    $('#spinner').hide(); // Hide spinner
                    $('#submitButton').prop('disabled', false); // Enable button
                    toastr.success('Excel file uploaded successfully!');
                    window.location.href = '/RMS/customer-list';
                }
            });
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.main-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/siddhivinayakpump.com/public_html/resources/views/customer-master/create.blade.php ENDPATH**/ ?>