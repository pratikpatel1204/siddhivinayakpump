@extends('layout.main-layout')
@section('title', config('app.name') . ' || Redeem Reward History')
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
                        <h4 class="card-title mb-0">Redeem Reward History</h4>
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
                            <table id="red_his" class="table table-striped table-bordered zero-configuration">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Employee Name</th>
                                        <th>Name</th>
                                        <th>Address</th>
                                        <th>Village/City</th>
                                        <th>District</th>
                                        <th>State</th>
                                        <th>Type</th>
                                        <th>Mobile No</th>
                                        <th>Vehicle No</th>
                                        <th>Service</th>
                                        <th>Used Reward Points</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($redeemHistory as $history)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $history->emp?->name ?? 'N/A' }}</td>
                                        <td>{{ $history->name }}</td>
                                        <td>{{ $history->address }}</td>
                                        <td>{{ $history->village_city }}</td>
                                        <td>{{ $history->district }}</td>
                                        <td>{{ $history->state }}</td>
                                        <td>{{ $history->type }}</td>
                                        <td>{{ $history->mobile_no }}</td>
                                        <td>{{ $history->vehicle_no }}</td>
                                        <td>{{ ucwords(str_replace('_', ' ', $history->service)) }}</td>
                                        <td>{{ $history->used_reward_points }}</td>
                                        <td>{{ $history->created_at->format('d-m-Y H:i:s') }}</td>
                                        <td>{{ $history->updated_at->format('d-m-Y H:i:s') }}</td>
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
        $('#red_his').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'excelHtml5'
                    , text: 'Export to Excel'
                    , className: 'btn btn-success'
                }
                , {
                    extend: 'pdfHtml5'
                    , text: 'Export to PDF'
                    , className: 'btn btn-danger'
                }
            ]
        });
    });
</script>
@if(session('success'))
<script>
    toastr.success("{{ session('success') }}", "Success");

</script>
@endif
@endsection
