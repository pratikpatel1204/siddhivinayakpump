@extends('layout.main-layout')
@section('title', config('app.name') . ' || Edit Employee')
@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Employee</h4>
                        <div class="basic-form">
                            <form id="editempForm">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" id="name" value="{{$employee->name}}" placeholder="Enter Name">
                                        <small class="text-danger" id="nameError"></small>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" value="{{$employee->email}}" placeholder="Enter Email">
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
                                        <input type="text" class="form-control" id="mobile" value="{{$employee->mobile}}" placeholder="Enter Mobile">
                                        <small class="text-danger" id="mobileError"></small>
                                    </div>
                                    <div class="form-group col-md-6">
                                        @if (!empty($employee->profile_image) && file_exists(public_path($employee->profile_image)))
                                        <img src="{{ asset($employee->profile_image) }}" class="mb-2" alt="Profile Image" width="150">
                                        @endif
                                        <br><label for="profileImage">Profile Image</label><br>
                                        <input type="file" class="form-control-file" id="profileImage">
                                        <small class="text-danger" id="profileImageError"></small>
                                    </div>
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
        function clearError(inputId) {
            $(`#${inputId}`).on('input change', function() {
                $(`#${inputId}Error`).text('');
            });
        }

        clearError('name');
        clearError('email');
        clearError('password');
        clearError('confirmPassword');
        clearError('profileImage');
        clearError('mobile');

        const allowedImageTypes = ['image/jpeg', 'image/png', 'image/jpg'];

        $('#editempForm').on('submit', function(event) {
            event.preventDefault();
            $('.text-danger').text('');

            let isValid = true;
            const name = $('#name').val().trim();
            const email = $('#email').val().trim();
            const password = $('#password').val();
            const confirmPassword = $('#confirmPassword').val();
            const profileImage = $('#profileImage').prop('files')[0];
            const mobile = $('#mobile').val().trim();

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

            // Mobile validation
            if (!mobile) {
                $('#mobileError').text('Mobile number is required.');
                isValid = false;
            } else if (!/^[0-9]\d{9}$/.test(mobile)) {
                $('#mobileError').text('Mobile number must be 10 digits.');
                isValid = false;
            }

            // Password validation (optional)
            if (password.length > 0 && password.length < 6) {
                $('#passwordError').text('Password must be at least 6 characters.');
                isValid = false;
            }

            // Confirm Password validation
            if (password && password !== confirmPassword) {
                $('#confirmPasswordError').text('Passwords do not match.');
                isValid = false;
            }

            // Profile Image validation
            if (profileImage) {
                if (!allowedImageTypes.includes(profileImage.type)) {
                    $('#profileImageError').text('Only JPG and PNG files are allowed.');
                    isValid = false;
                } else if (profileImage.size > 2 * 1024 * 1024) { // 2MB max size
                    $('#profileImageError').text('Image must be less than 2MB.');
                    isValid = false;
                }
            }

            if (!isValid) {
                toastr.error('Please fix the errors in the form.');
                return;
            }

            // Create FormData object
            const formData = new FormData();
            formData.append('id', '{{ $employee->id }}');
            formData.append('name', name);
            formData.append('email', email);
            formData.append('mobile', mobile);
            if (password) {
                formData.append('password', password);
                formData.append('password_confirmation', confirmPassword);
            }
            if (profileImage) {
                formData.append('profileImage', profileImage);
            }
            formData.append('_token', "{{ csrf_token() }}");

            $.ajax({
                url: '{{ route("update.employee") }}'
                , type: 'POST'
                , data: formData
                , processData: false
                , contentType: false
                , success: function(response) {
                    toastr.success('Form submitted successfully!');
                    window.location.href = '/RMS/employee-list';
                }
                , error: function(xhr) {
                    const errors = (xhr.responseJSON && xhr.responseJSON.errors) || {};
                    for (const [field, message] of Object.entries(errors)) {
                        $(`#${field}Error`).text(message);
                    }
                    toastr.error(xhr.responseJSON ? xhr.responseJSON.message : 'Form submission failed.');
                    console.error("AJAX Error:", xhr);
                }
            });
        });
    });
</script>
@endsection
