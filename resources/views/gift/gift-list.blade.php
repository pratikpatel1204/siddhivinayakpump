@extends('layout.main-layout')
@section('title', config('app.name') . ' || Gift List')
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
                        <h4 class="card-title mb-0">Gift List</h4>
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
                        <button type="button" class="btn btn-primary" id="clearAll">Clear All</button>
                        <button type="button" class="btn btn-success" id="whatsappBtn">WhatsApp</button>                       
                        <div class="table-responsive">
                            <table id="gift_table" class="table table-striped table-bordered zero-configuration">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>
                                            <input type="checkbox" id="selectAll"> All
                                        </th>
                                        <th>Mobile No</th>
                                        <th>Vehicle No</th>
                                        <th>Type</th>
                                        <th>Total Amount</th>
                                        <th>Earn Gift Points</th>
                                        <th>Used Gift Points</th>
                                        <th>Pending Gift Points</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customers as $custom)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>
                                            <input type="checkbox" class="rowCheckbox">
                                        </td>
                                        <td>{{$custom->mobile_no}}</td>
                                        <td>{{$custom->vehicle_no}}</td>
                                        <td>{{$custom->type}}</td>
                                        <td>{{$custom->total_amount}}</td>
                                        <td>{{$custom->earned_points}}</td>
                                        <td>{{$custom->used_points}}</td>
                                        <td>{{$custom->pending_points}}</td>
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
        $('#gift_table').DataTable({
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
<script>
    $(document).ready(function() {
        // Select all checkboxes when the header checkbox is clicked
        $('#selectAll').on('change', function() {
            $('.rowCheckbox').prop('checked', $(this).prop('checked'));
        });

        // If any individual checkbox is unchecked, uncheck the "Select All" checkbox
        $('.rowCheckbox').on('change', function() {
            if (!$(this).prop('checked')) {
                $('#selectAll').prop('checked', false);
            }

            // If all checkboxes are checked, check the "Select All" checkbox
            if ($('.rowCheckbox:checked').length === $('.rowCheckbox').length) {
                $('#selectAll').prop('checked', true);
            }
        });

        // Clear all checkboxes when the "Clear All" button is clicked
        $('#clearAll').on('click', function() {
            $('.rowCheckbox, #selectAll').prop('checked', false);
        });
    });
</script>
@endsection