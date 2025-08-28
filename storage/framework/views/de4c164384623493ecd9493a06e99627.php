<!DOCTYPE html>
<html class="h-100" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?php echo e(config('app.name') . ' || Login'); ?></title>
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo e(asset('admin/images/favicon.png')); ?>">
    <link href="<?php echo e(asset('admin/css/style.css')); ?>" rel="stylesheet">
</head>
<body class="h-100">
    <div id="preloader">
        <div class="loader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="3" stroke-miterlimit="10" />
            </svg>
        </div>
    </div>
    <div class="login-form-bg h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100">
                <div class="col-xl-6">
                    <div class="form-input-content">
                        <div class="card login-form mb-0">
                            <div class="card-body pt-5">
                                <div class="text-center">
                                    <img src="<?php echo e(asset('admin/images/logo-compact.png')); ?>" alt="" style="width: 200px;">
                                </div>
                                <form class="mt-5 mb-5 login-input" id="loginform">
                                    <div class="form-group">
                                        <input type="email" class="form-control" name="email" id="email" placeholder="Email">
                                    </div>
                                    <div class="form-group">
                                        <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                                    </div>
                                    <button class="btn login-form__btn submit w-100" id="signInButton">
                                        <span class="button-text">Sign In</span>
                                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    </button>                                    
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="<?php echo e(asset('admin/plugins/common/common.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/js/custom.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/js/settings.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/js/gleek.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/js/styleSwitcher.js')); ?>"></script>
</body>
<script>
    $(document).ready(function() {
        // Bind the form submission
        $('#loginform').submit(function(e) {
            e.preventDefault(); // Prevent default form submission

            // Get the email and password values
            var email = $('#email').val();
            var password = $('#password').val();
            var isValid = true;

            // Clear previous error messages or highlights
            $('.form-group').removeClass('has-error');
            $('.form-group .error-message').remove();

            // Validate email
            if (email === '') {
                isValid = false;
                $('#email').closest('.form-group').addClass('has-error');
                $('#email').after('<span class="error-message text-danger">Email is required.</span>');
            } else if (!validateEmail(email)) {
                isValid = false;
                $('#email').closest('.form-group').addClass('has-error');
                $('#email').after('<span class="error-message text-danger">Please enter a valid email address.</span>');
            }

            // Validate password
            if (password === '') {
                isValid = false;
                $('#password').closest('.form-group').addClass('has-error');
                $('#password').after('<span class="error-message text-danger">Password is required.</span>');
            } else if (password.length < 6) {
                isValid = false;
                $('#password').closest('.form-group').addClass('has-error');
                $('#password').after('<span class="error-message text-danger">Password must be at least 6 characters long.</span>');
            }

            // If validation failed, do not send the AJAX request
            if (!isValid) {
                toastr.error('Please fix the errors before submitting.');
                return; // Prevent AJAX call if validation fails
            }

            // Show loading spinner in the submit button
            $('#submitBtn').prop('disabled', true);
            $('#submitBtn').html('<i class="fa fa-spinner fa-spin"></i> Logging in...'); // Add spinner
            var $button = $('#signInButton');
            $button.prop('disabled', true);
            $button.find('.button-text').text('Signing In...');
            $button.find('.spinner-border').removeClass('d-none');
            // Send the AJAX request
            $.ajax({
                url: '<?php echo e(route("login")); ?>', 
                type: 'POST', 
                data: {
                    email: email, 
                    password: password,
                     _token: '<?php echo e(csrf_token()); ?>',
                }, 
                success: function(response) {
                    window.location.href = response.redirect_url || '/dashboard';
                }, 
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    if (errors) {
                        $.each(errors, function(key, value) {
                            toastr.error(value[0]);
                        });
                    } else {
                        toastr.error('An error occurred. Please try again later.');
                    }
                },                  
                complete: function() {
                    $button.prop('disabled', false);
                    $button.find('.button-text').text('Sign In'); // Reset text
                    $button.find('.spinner-border').addClass('d-none'); // Hide loader
                }
            });
        });
        function validateEmail(email) {
            var re = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
            return re.test(email);
        }
    });
</script>
</html>
<?php /**PATH D:\xampp\htdocs\siddhivinayakpump\resources\views/auth/login.blade.php ENDPATH**/ ?>