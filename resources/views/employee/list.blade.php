@extends('layout.main-layout')
@section('title', config('app.name') . ' || Employee List')
@section('content')
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
                        <a href="{{route('add.employee')}}" class="btn btn-primary" role="button"><i class="fas fa-plus"></i> Employee</a>
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
                                    @foreach ($users as $user)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{$user->name}}</td>
                                        <td>{{$user->email}}</td>
                                        <td>{{$user->mobile}}</td>
                                        <td>{{$user->showpassword}}</td>
                                        <td>
                                            <img src="{{ asset($user->profile_image) }}" alt="Profile Image" width="50">
                                        </td>
                                        <td>
                                            <a href="{{ route('edit.employee', ['id' => Crypt::encryptString($user->id)]) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="javascript:void(0);" class="btn btn-danger btn-sm delete-btn" data-id="{{ $user->id }}" role="button">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
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
                    url: '{{route("destroy.employee")}}',
                    type: 'post'
                    , data: {
                        _token: '{{ csrf_token() }}', 
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
@endsection
