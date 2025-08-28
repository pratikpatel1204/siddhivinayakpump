@extends('layout.main-layout')
@section('title', config('app.name') . ' || Service Master')
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
                        <h4 class="card-title mb-0">Service Master</h4>
                        <a href="{{route('create.service.master')}}" class="btn btn-primary" role="button"><i class="fas fa-plus"></i> Service</a>
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
                                        <th>Service Name</th>
                                        <th>Price</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($service as $ser)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $ser->service_name }}</td>                                       
                                        <td>{{ $ser->price }}</td>
                                        <td>
                                            <a href="{{ route('edit.service.master', ['id' => Crypt::encryptString($ser->id)]) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="javascript:void(0);" class="btn btn-danger btn-sm delete-btn" data-id="{{ $ser->id }}" role="button">
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
            var rateId = $(this).data('id');
            if (confirm('Are you sure you want to delete this service?')) {
                $.ajax({
                    url: '{{route("destroy.service.master")}}'
                    , type: 'post'
                    , data: {
                        _token: '{{ csrf_token() }}'
                        , id: rateId
                    , }
                    , success: function(response) {
                        toastr.success(response.message);
                        window.location.reload();
                    }
                    , error: function(xhr, status, error) {
                        toastr.error('Error deleting rate');
                    }
                });
            }
        });
    });

</script>
@if(session('success'))
<script>
    toastr.success("{{ session('success') }}", "Success");

</script>
@endif
@endsection
