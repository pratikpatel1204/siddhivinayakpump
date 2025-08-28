@extends('layout.main-layout')
@section('title', config('app.name') . ' || expired-reward-point-list')
@section('content')
<style>
    .dataTables_length select {
        border: 1px solid black;
    }
    .dataTables_filter input {
        border: 1px solid black;
        height: 30px !important;
    }
    .form-control-sm { min-height: 30px; }
    .form-control { height: 30px; }
</style>

<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card mb-0">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <h4 class="card-title mb-0">Expired Reward Point List</h4>                      
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
                            <table class="table table-striped" id="report_table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Customer Type</th>                                        
                                        <th>Vehicle No</th>
                                        <th>Mobile No</th>
                                        <th>Expired Points</th>
                                        <th>Reward Created</th>
                                        <th>Reward Expired</th>
                                        <th>Expired On</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($expiredRewards as $row)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $row->customer->type ?? '-' }}</td>                                      
                                        <td>{{ $row->customer->vehicle_no ?? '-' }}</td>
                                        <td>{{ $row->customer->mobile_no ?? '-' }}</td>
                                        <td>{{ $row->expired_points }}</td>
                                        <td>{{ $row->reward_created_date }}</td>
                                        <td>{{ $row->reward_expired_date }}</td>
                                        <td>{{ $row->expired_on }}</td>
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

@if(session('success'))
<script>
    toastr.success("{{ session('success') }}", "Success");
</script>
@endif

<script>
    $(document).ready(function() {
        var tableConfig = {
            searching: true,
            ordering: true,
            paging: true,
            info: true,
            dom: 'Bfrtip',
            buttons: [
                { extend: 'excelHtml5', text: 'Export to Excel', className: 'btn btn-success' },
                { extend: 'pdfHtml5', text: 'Export to PDF', className: 'btn btn-danger' }
            ]
        };
        $('#report_table').DataTable(tableConfig);
    });
</script>
@endsection
