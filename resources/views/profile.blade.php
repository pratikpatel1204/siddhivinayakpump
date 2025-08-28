@extends('layout.main-layout')
@section('title', config('app.name') . ' || Profile')
@section('content')
<div class="content-body">
    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-lg-6 col-sm-6">
                <div class="card">
                    <img class="img-fluid" src="{{$user->profile_image}}" alt="" style="width: 100%;height: 300px;">
                    <div class="card-body">
                        <h5 class="card-title">Name : {{$user->name}}</h5>
                        <p class="card-text">Email : {{$user->email}}</p>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <form id="passwordupdateform" method="POST">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label for="current_password">Current Password</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Enter current password" required>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="new_password">New Password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Enter new password" required>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="confirm_password">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
                                </div>
                            </div>
                            <button type="button" id="submitBtn" class="btn btn-primary mt-4">Update Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#submitBtn').on('click', function(e) {
            e.preventDefault(); // Prevent form from submitting normally

            // Get form data
            var formData = {
                current_password: $('#current_password').val(),
                password: $('#new_password').val(),
                password_confirmation: $('#confirm_password').val(),
                _token: '{{ csrf_token() }}' // Add CSRF token
            };

            // Validation: Make sure passwords match
            if (formData.password !== formData.password_confirmation) {
                toastr.error("The new passwords do not match.");
                return;
            }

            // Send AJAX request
            $.ajax({
                url: "{{ route('password.update') }}", // Make sure the route is correct
                method: "POST",
                data: formData,
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success("Password updated successfully.");
                        window.location.reload();
                    } else {
                        toastr.error(response.message || 'Something went wrong, please try again.');
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error("Error: " + xhr.responseJSON.message || "Something went wrong.");
                }
            });
        });
    });
</script>
@endsection
