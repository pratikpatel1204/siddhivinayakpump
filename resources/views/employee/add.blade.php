@extends('layout.main-layout')
@section('title', config('app.name') . ' || Create Employee')
@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Create Employee</h4>
                        <div class="basic-form">
                            <form id="signupForm">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" id="name" placeholder="Enter Name">
                                        <small class="text-danger" id="nameError"></small>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" placeholder="Enter Email">
                                        <small class="text-danger" id="emailError"></small>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="password">Password</label>
                                        <input type="password" class="form-control" id="password" placeholder="Password">
                                        <small class="text-danger" id="passwordError"></small>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="confirmPassword">Confirm Password</label>
                                        <input type="password" class="form-control" id="confirmPassword" name="password_confirmation" placeholder="Confirm Password">
                                        <small class="text-danger" id="confirmPasswordError"></small>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="mobile">Mobile</label>
                                        <input type="text" class="form-control" id="mobile" placeholder="Enter Mobile">
                                        <small class="text-danger" id="mobileError"></small>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="profileImage">Profile Image</label>
                                        <input type="file" class="form-control-file" id="profileImage">
                                        <small class="text-danger" id="profileImageError"></small>
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
<script>
    $(document).ready(function () {
        // Function to clear errors when input changes
        function clearError(inputId) {
            $(`#${inputId}`).on('input', function () {
                $(`#${inputId}Error`).text('');
            });
        }

        // Attach input event listeners to clear errors
        clearError('name');
        clearError('email');
        clearError('password');
        clearError('confirmPassword');
        clearError('mobile');
        clearError('profileImage');

        // Form submission handler
        $('#signupForm').on('submit', function (event) {
            event.preventDefault(); // Prevent default form submission

            // Reset all error messages
            $('.text-danger').text('');

            // Validate form fields
            let isValid = true;
            const name = $('#name').val().trim();
            const email = $('#email').val().trim();
            const password = $('#password').val();
            const confirmPassword = $('#confirmPassword').val();
            const mobile = $('#mobile').val().trim();
            const profileImage = $('#profileImage').prop('files')[0];

            // Name validation
            if (!name) {
                $('#nameError').text('Name is required.');
                isValid = false;
            }

            // Email validation
            if (!email) {
                $('#emailError').text('Email is required.');
                isValid = false;
            } else if (!/\S+@\S+\.\S+/.test(email)) {
                $('#emailError').text('Invalid email format.');
                isValid = false;
            }

            // Password validation
            if (!password) {
                $('#passwordError').text('Password is required.');
                isValid = false;
            } else if (password.length < 6) {
                $('#passwordError').text('Password must be at least 8 characters.');
                isValid = false;
            }

            // Confirm Password validation
            if (!confirmPassword) {
                $('#confirmPasswordError').text('Confirm Password is required.');
                isValid = false;
            } else if (password !== confirmPassword) {
                $('#confirmPasswordError').text('Passwords do not match.');
                isValid = false;
            }

            // Mobile validation (exactly 10 digits, no alphabets)
            if (!mobile) {
                $('#mobileError').text('Mobile number is required.');
                isValid = false;
            } else if (!/^\d{10}$/.test(mobile)) {
                $('#mobileError').text('Mobile number must be exactly 10 digits.');
                isValid = false;
            }

            // Profile Image validation
            if (!profileImage) {
                $('#profileImageError').text('Profile Image is required.');
                isValid = false;
            }

            // If validation fails, stop here
            if (!isValid) {
                toastr.error('Please fix the errors in the form.');
                return;
            }

            // Create FormData object for AJAX submission
            const formData = new FormData();
            formData.append('name', name);
            formData.append('email', email);
            formData.append('password', password);
            formData.append('password_confirmation', confirmPassword);
            formData.append('mobile', mobile);
            formData.append('profileImage', profileImage);
            formData.append('_token', "{{ csrf_token() }}");

            // AJAX call
            $.ajax({
                url: '{{ route("store.employee") }}', // Replace with your server endpoint
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    toastr.success('Form submitted successfully!');
                    window.location.href = '/RMS/employee-list';
                },
                error: function (xhr, status, error) {
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
