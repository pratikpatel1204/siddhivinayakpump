@extends('layout.main-layout')
@section('title', config('app.name') . ' || Update Reward')
@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Update Reward</h4>
                        <div class="basic-form">
                            <form id="UpdateRewardForm" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" class="form-control" id="id" name="id" value="{{$reward->id}}">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="regular_reward_price">Regular Reward Price</label>
                                        <input type="number" class="form-control" id="regular_reward_price" name="regular_reward_price" value="{{$reward->regular_reward_price}}">
                                        <small class="text-danger" id="regularRewardPriceError"></small>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="regular_reward_points">Regular Reward Points</label>
                                        <input type="number" class="form-control" id="regular_reward_points" name="regular_reward_points" value="{{$reward->regular_reward_points}}">
                                        <small class="text-danger" id="regularRewardPointsError"></small>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="commercial_reward_price">Commercial Reward Price</label>
                                        <input type="number" class="form-control" id="commercial_reward_price" name="commercial_reward_price" value="{{$reward->commercial_reward_price}}">
                                        <small class="text-danger" id="commercialRewardPriceError"></small>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="commercial_reward_points">Commercial Reward Points</label>
                                        <input type="number" class="form-control" id="commercial_reward_points" name="commercial_reward_points" value="{{$reward->commercial_reward_points}}">
                                        <small class="text-danger" id="commercialRewardPointsError"></small>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="tractor_reward_price">Tractor Reward Price</label>
                                        <input type="number" class="form-control" id="tractor_reward_price" name="tractor_reward_price" value="{{$reward->tractor_reward_price}}">
                                        <small class="text-danger" id="tractorRewardPriceError"></small>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="tractor_reward_points">Tractor Reward Points</label>
                                        <input type="number" class="form-control" id="tractor_reward_points" name="tractor_reward_points" value="{{$reward->tractor_reward_points}}">
                                        <small class="text-danger" id="tractorRewardPointsError"></small>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="regular_price">Regular Price</label>
                                        <input type="number" class="form-control" id="regular_price" name="regular_price" value="{{$reward->regular_price}}">
                                        <small class="text-danger" id="regularPriceError"></small>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="regular_gift_point">Regular Gift Point</label>
                                        <input type="number" class="form-control" id="regular_gift_point" name="regular_gift_point" value="{{$reward->regular_gift_point}}">
                                        <small class="text-danger" id="regularGiftPointError"></small>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="commercial_price">Commercial Price</label>
                                        <input type="number" class="form-control" id="commercial_price" name="commercial_price" value="{{$reward->commercial_price}}">
                                        <small class="text-danger" id="commercialPriceError"></small>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="commercial_gift_point">Commercial Gift Point</label>
                                        <input type="number" class="form-control" id="commercial_gift_point" name="commercial_gift_point" value="{{$reward->commercial_gift_point}}">
                                        <small class="text-danger" id="commercialGiftPointError"></small>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="tractor_price">Tractor Price</label>
                                        <input type="number" class="form-control" id="tractor_price" name="tractor_price" value="{{$reward->tractor_price}}">
                                        <small class="text-danger" id="tractorPriceError"></small>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="tractor_gift_point">Tractor Gift Point</label>
                                        <input type="number" class="form-control" id="tractor_gift_point" name="tractor_gift_point" value="{{$reward->tractor_gift_point}}">
                                        <small class="text-danger" id="tractorGiftPointError"></small>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="expiry_days">Expiry Days</label>
                                        <input type="number" class="form-control" id="expiry_days" name="expiry_days" value="{{$reward->expiry_days}}">
                                        <small class="text-danger" id="expiryDaysError"></small>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-dark">Update</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        function clearError(inputId) {
            $(`#${inputId}`).on('input', function() {
                $(`#${inputId}Error`).text('');
            });
        }

        // Clear errors on input
        const fields = [
            'regular_reward_price', 'regular_reward_points'
            , 'commercial_reward_price', 'commercial_reward_points'
            , 'tractor_reward_price', 'tractor_reward_points'
            , 'regular_price', 'regular_gift_point'
            , 'commercial_price', 'commercial_gift_point'
            , 'tractor_price', 'tractor_gift_point', 'expiry_days'
        ];

        fields.forEach(field => clearError(field));

        $('#UpdateRewardForm').on('submit', function(event) {
            event.preventDefault(); // Prevent form submission

            // Reset all error messages
            $('.text-danger').text('');

            let isValid = true;

            // Function to validate positive numbers
            function validateNumber(fieldId, errorId, fieldName) {
                let value = $(`#${fieldId}`).val().trim();
                if (value === '') {
                    $(`#${errorId}`).text(`${fieldName} is required.`);
                    isValid = false;
                } else if (isNaN(value) || parseFloat(value) < 0) {
                    $(`#${errorId}`).text(`${fieldName} must be a positive number.`);
                    isValid = false;
                }
            }

            // Validate all fields
            validateNumber('regular_reward_price', 'regularRewardPriceError', 'Regular Reward Price');
            validateNumber('regular_reward_points', 'regularRewardPointsError', 'Regular Reward Points');
            validateNumber('commercial_reward_price', 'commercialRewardPriceError', 'Commercial Reward Price');
            validateNumber('commercial_reward_points', 'commercialRewardPointsError', 'Commercial Reward Points');
            validateNumber('tractor_reward_price', 'tractorRewardPriceError', 'Tractor Reward Price');
            validateNumber('tractor_reward_points', 'tractorRewardPointsError', 'Tractor Reward Points');
            validateNumber('regular_price', 'regularPriceError', 'Regular Price');
            validateNumber('regular_gift_point', 'regularGiftPointError', 'Regular Gift Point');
            validateNumber('commercial_price', 'commercialPriceError', 'Commercial Price');
            validateNumber('commercial_gift_point', 'commercialGiftPointError', 'Commercial Gift Point');
            validateNumber('tractor_price', 'tractorPriceError', 'Tractor Price');
            validateNumber('tractor_gift_point', 'tractorGiftPointError', 'Tractor Gift Point');
            validateNumber('expiry_days', 'expiryDaysError', 'Expiry Days');
            
            if (!isValid) {
                toastr.error('Please fix the errors before submitting.');
                return;
            }

            // Create FormData object
            let formData = new FormData(this);

            $.ajax({
                url: '{{ route("update.reward.master") }}'
                , type: 'POST'
                , data: formData
                , processData: false
                , contentType: false
                , success: function(response) {
                    toastr.success('Reward updated successfully!');
                    window.location.href = '/RMS/reward-master';
                }
                , error: function(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        Object.entries(xhr.responseJSON.errors).forEach(([field, message]) => {
                            $(`#${field}Error`).text(message);
                        });
                    }
                    toastr.error(xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Form submission failed.');
                }
            });
        });
    });
</script>
@endsection
