@extends('layout.main-layout')
@section('title', config('app.name') . ' || Add Customer')
@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Add Customer</h4>
                        <div class="basic-form">
                            <form id="editrateForm" action="{{ route('save.customer') }}" method="POST">
                                @csrf
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="trx_id">Trx ID</label>
                                        <input type="text" class="form-control" id="trx_id" name="trx_id" value="{{ old('trx_id') }}" placeholder="Enter Trx ID" required>
                                        @error('trx_id')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="type">Type</label>
                                        <select class="form-control" id="type" name="type" required>
                                            <option value="">Select Type</option>
                                            <option value="Regular" {{ old('type') == 'Regular' ? 'selected' : '' }}>Regular</option>
                                            <option value="Commercial" {{ old('type') == 'Commercial' ? 'selected' : '' }}>Commercial</option>
                                            <option value="Tractor" {{ old('type') == 'Tractor' ? 'selected' : '' }}>Tractor</option>
                                        </select>
                                        @error('type')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="date_time">Date Time</label>
                                        <input type="datetime-local" class="form-control" id="date_time" name="date_time" value="{{ old('date_time') }}" required>
                                        @error('date_time')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="pump">Pump</label>
                                        <input type="number" class="form-control" id="pump" name="pump" value="{{ old('pump') }}" placeholder="Enter Pump" required>
                                        @error('pump')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="rdb_nozzle">RDB Nozzle</label>
                                        <input type="number" class="form-control" id="rdb_nozzle" name="rdb_nozzle" value="{{ old('rdb_nozzle') }}" placeholder="Enter RDB Nozzle" required>
                                        @error('rdb_nozzle')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="product">Product</label>
                                        <input type="text" class="form-control" id="product" name="product" value="{{ old('product') }}" placeholder="Enter Product" required>
                                        @error('product')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="unit_price">Unit Price</label>
                                        <input type="number" step="0.01" class="form-control" id="unit_price" name="unit_price" value="{{ old('unit_price') }}" placeholder="Enter Unit Price" required>
                                        @error('unit_price')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="payment">Payment</label>
                                        <input type="text" class="form-control" id="payment" name="payment" value="{{ old('payment') }}" placeholder="Enter Payment Method" required>
                                        @error('payment')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="volume">Volume</label>
                                        <input type="number" step="0.01" class="form-control" id="volume" name="volume" value="{{ old('volume') }}" placeholder="Enter Volume" required>
                                        @error('volume')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="amount">Amount</label>
                                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="{{ old('amount') }}" placeholder="Enter Amount" required>
                                        @error('amount')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="print_id">Print ID</label>
                                        <input type="text" class="form-control" id="print_id" name="print_id" value="{{ old('print_id') }}" placeholder="Enter Print ID" required>
                                        @error('print_id')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="vehicle_no">Vehicle No</label>
                                        <input type="text" class="form-control" id="vehicle_no" name="vehicle_no" value="{{ old('vehicle_no') }}" placeholder="Enter Vehicle No" required>
                                        @error('vehicle_no')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="mobile_no">Mobile No</label>
                                        <input type="text" class="form-control" id="mobile_no" name="mobile_no" value="{{ old('mobile_no') }}" placeholder="Enter Mobile No" required>
                                        @error('mobile_no')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>                                   
                                </div>
                                <button type="submit" class="btn btn-dark">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@if(session('success'))
<script>
    toastr.success("{{ session('success') }}", "Success", {
        closeButton: true
        , progressBar: true
    });

</script>
@endif
@if(session('error'))
<script>
    toastr.error("{{ session('error') }}", "Error", {
        closeButton: true
        , progressBar: true
    });

</script>
@endif
@endsection