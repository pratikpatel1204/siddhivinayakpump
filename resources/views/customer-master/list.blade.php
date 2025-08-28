@extends('layout.main-layout')
@section('title', config('app.name') . ' || Customer List')
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
                        <h4 class="card-title mb-0">Customer List</h4>
                        <a href="{{route('create.customer')}}" class="btn btn-primary" role="button"><i class="fas fa-plus"></i> Customer</a>
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
                      	<div class="row mb-3">
                            <div class="col-md-3">
                                <input type="text" id="start_date" class="form-control" placeholder="Start Date" value="{{ request('start_date')}}">
                            </div>
                            <div class="col-md-3">
                                <input type="text" id="end_date" class="form-control" placeholder="End Date" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-2">
                                <button id="filterBtn" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="cust_table" class="table table-striped table-bordered zero-configuration">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Action</th>
                                        <th>Status</th>
                                        <th>Type</th>
                                        <th>Transaction ID</th>
                                        <th>Date & Time</th>
                                        <th>Pump</th>
                                        <th>Nozzle</th>
                                        <th>Product</th>
                                        <th>Unit Price</th>
                                        <th>Payment</th>
                                        <th>Volume</th>
                                        <th>Amount</th>
                                        <th>Print ID</th>
                                        <th>Vehicle No</th>
                                        <th>Mobile No</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customers as $cust)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <a href="javascript:void(0);" class="btn btn-info btn-sm delete-btn" data-id="{{ $cust->id }}" role="button">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                            @if ($cust->status == '1')
                                            <a href="javascript:void(0);" class="btn btn-danger btn-sm status-btn" data-id="{{ $cust->id }}" data-status="0" role="button">
                                                <i class="fas fa-times"></i>
                                            </a>
                                            @else
                                            <a href="javascript:void(0);" class="btn btn-success btn-sm status-btn" data-id="{{ $cust->id }}" data-status="1" role="button">
                                                <i class="fas fa-check"></i>
                                            </a>
                                            @endif
                                            <a href="{{route('edit.customer', ['id' => encrypt($cust->id)])}}" class="btn btn-success btn-sm" role="button">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        </td>
                                        <td>
                                            @if ($cust->status == '1')
                                            <span class="badge bg-success text-dark">Active</span>
                                            @else
                                            <span class="badge bg-danger text-dark">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $cust->type }}</td>
                                        <td>{{ $cust->trx_id }}</td>
                                        <td>{{ $cust->date_time }}</td>
                                        <td>{{ $cust->pump }}</td>
                                        <td>{{ $cust->rdb_nozzle }}</td>
                                        <td>{{ $cust->product }}</td>
                                        <td>{{ $cust->unit_price }}</td>
                                        <td>{{ $cust->payment }}</td>
                                        <td>{{ $cust->volume }}</td>
                                        <td>{{ $cust->amount }}</td>
                                        <td>{{ $cust->print_id }}</td>
                                        <td>{{ $cust->vehicle_no }}</td>
                                        <td>{{ $cust->mobile_no }}</td>
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
        $('#cust_table').DataTable({
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 Library -->
<script>
    $(document).ready(function() {
        $('.delete-btn').on('click', function() {
            var rateId = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to undo this action!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("destroy.customer") }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: rateId
                        },
                        success: function(response) {
                            Swal.fire("Deleted!", response.message, "success");
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        },
                        error: function(xhr, status, error) {
                            Swal.fire("Error!", "Failed to delete the rate.", "error");
                        }
                    });
                }
            });
        });
    });
</script>
<script>
    $(document).ready(function() {
        $(".status-btn").on("click", function() {
            let button = $(this);
            let customerId = button.data("id");
            let newStatus = button.data("status");
            let confirmMessage = (newStatus == 1) ?
                "Are you sure you want to activate this customer?" :
                "Are you sure you want to deactivate this customer?";
            Swal.fire({
                title: "Confirm Action"
                , text: confirmMessage
                , icon: "warning"
                , showCancelButton: true
                , confirmButtonColor: "#3085d6"
                , cancelButtonColor: "#d33"
                , confirmButtonText: "Yes, Confirm!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('customer.updateStatus') }}",
                        type: "POST"
                        , data: {
                            _token: "{{ csrf_token() }}"
                            , id: customerId
                            , status: newStatus
                        }
                        , success: function(response) {
                            if (response.success) {
                                window.location.reload();
                            } else {
                                Swal.fire("Error!", "Failed to update status.", "error");
                            }
                        }
                        , error: function() {
                            Swal.fire("Error!", "Something went wrong.", "error");
                        }
                    });
                }
            });
        });
    });
</script>
@if(session('success'))
<script>
    toastr.success("{{ session('success') }}", "Success");

</script>
@endif
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    $(document).ready(function () {
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);

        // Initialize flatpickr with default only if input is empty
        flatpickr("#start_date", {
            dateFormat: "d-m-Y",
            defaultDate: $('#start_date').val() || firstDay
        });

        flatpickr("#end_date", {
            dateFormat: "d-m-Y",
            defaultDate: $('#end_date').val() || lastDay
        });

        // On filter button click
        $('#filterBtn').on('click', function () {
            let startDate = $('#start_date').val();
            let endDate = $('#end_date').val();

            const url = new URL(window.location.href);
            url.searchParams.set('start_date', startDate);
            url.searchParams.set('end_date', endDate);

            window.location.href = url.toString();
        });

        // Auto call on first load
        const urlParams = new URLSearchParams(window.location.search);
        if (!urlParams.has('start_date') || !urlParams.has('end_date')) {
            $('#filterBtn').click(); // trigger filter on load with default range
        }
    });
</script>
@endsection
