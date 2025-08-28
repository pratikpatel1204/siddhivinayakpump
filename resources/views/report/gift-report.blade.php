@extends('layout.main-layout')
@section('title', config('app.name') . ' || Gift Report')
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
                        <h4 class="card-title mb-0">Gift Report</h4>                      
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
                                        <th>Type</th>
                                        <th>Name</th>
                                        <th>Village/City</th>
                                        <th>District</th>
                                        <th>Vehicle No</th>
                                        <th>Mobile No</th>
                                        <th>Earn Reward Points</th>
                                        <th>Used Reward Points</th>
                                        <th>Pending Reward Points</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customers as $cust)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $cust->type }}</td>
                                        <td>{{ $cust->name }}</td>
                                        <td>{{ $cust->village_city }}</td>
                                        <td>{{ $cust->district }}</td>
                                        <td>{{ $cust->vehicle_no }}</td>
                                        <td>{{ $cust->mobile_no }}</td>
                                        <td>{{ $cust->earned_points }}</td>
                                        <td>{{ $cust->used_points }}</td>
                                        <td>{{ $cust->pending_points }}</td>
                                        <td>{{ $cust->date_time }}</td>
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
                {
                    extend: 'excelHtml5',
                    text: 'Export to Excel',
                    className: 'btn btn-success'
                },
                {
                    extend: 'pdfHtml5',
                    text: 'Export to PDF',
                    className: 'btn btn-danger'
                }
            ]
        };
        $('#report_table').DataTable(tableConfig);
    });
</script>
@endsection
