@extends('layout.main-layout')
@section('title', config('app.name') . ' || Update Service')
@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Update Service</h4>
                        <div class="basic-form">
                            <form id="ServiceupdateForm" enctype="multipart/form-data">
                                @csrf                              
                                <input type="hidden" class="form-control" id="id" name="id" value="{{$service->id}}">                                                         
                                <div class="form-group">
                                    <label for="service_name">Service Name</label>
                                    <input type="text" class="form-control" id="service_name" name="service_name" value="{{$service->service_name}}">
                                    <small class="text-danger" id="ServiceNameError"></small>
                                </div>
                                <div class="form-group">
                                    <label for="price">Price</label>
                                    <input type="number" class="form-control" id="price" name="price" value="{{$service->price}}">
                                    <small class="text-danger" id="priceError"></small>
                                </div>
                                <button type="submit" class="btn btn-dark">Update</button>
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
        // Function to clear errors when input changes
        function clearError(inputId) {
            $(`#${inputId}`).on('input', function() {
                $(`#${inputId}Error`).text('');
            });
        }

        // Attach input event listeners to clear errors
        clearError('price');
        clearError('service_name');

        // Form submission handler
        $('#ServiceupdateForm').on('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            // Reset all error messages
            $('.text-danger').text('');

            // Validate form fields
            let isValid = true;
            const id = $('#id').val();
            const price = $('#price').val();
            const serviceName = $('#service_name').val();

            // Price validation
            if (!price) {
                $('#priceError').text('Price is required.');
                isValid = false;
            }

            // Service Name validation
            if (!serviceName) {
                $('#ServiceNameError').text('Service Name is required.');
                isValid = false;
            }

            // If validation fails, stop here
            if (!isValid) {
                toastr.error('Please fix the errors in the form.');
                return;
            }

            // Create FormData for AJAX submission
            const formData = new FormData();
            formData.append('id', id);
            formData.append('service_name', serviceName);
            formData.append('price', price);
            formData.append('_token', "{{ csrf_token() }}");           

            // AJAX request to update the service
            $.ajax({
                url: '{{ route("update.service.master") }}', // Ensure you use the correct route for updating
                type: 'POST', // Use POST with PUT method override
                data: formData,
                processData: false, // Prevent jQuery from processing the data
                contentType: false, // Prevent jQuery from setting content-type header
                success: function(response) {
                    toastr.success('Service updated successfully!');
                    window.location.href = '/RMS/service-master'; // Redirect to services list
                },
                error: function(xhr, status, error) {
                    const errors = (xhr.responseJSON && xhr.responseJSON.errors) || {};
                    for (const [field, message] of Object.entries(errors)) {
                        $(`#${field}Error`).text(message);
                    }
                    toastr.error(xhr.responseJSON ? xhr.responseJSON.message : 'Form submission failed.');
                }
            });
        });
    });
</script>
@endsection