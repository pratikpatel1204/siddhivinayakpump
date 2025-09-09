@extends('layout.main-layout')
@section('title', config('app.name') . ' || Reward Redeem')
@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Reward Redeem</h4>
                        <div class="basic-form">
                            <form id="redeemForm">
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="type">Select Type</label>
                                        <select name="type" id="type" class="form-control">
                                            <option value="Regular" selected>Regular</option>
                                            <option value="Tractor">Tractor</option>
                                            <option value="Commercial">Commercial</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="mobile_no">Mobile No</label>
                                        <input type="number" class="form-control" id="mobile_no" name="mobile_no" placeholder="Enter Mobile No">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="vehicle_no">Vehicle No</label>
                                        <input type="number" class="form-control" id="vehicle_no" name="vehicle_no" placeholder="Enter Vehicle No">
                                        <div id="vehicle_suggestions" class="list-group" style="position: absolute; width: 100%;height: 100px;overflow: auto;"></div>
                                    </div>
                                    <div class="form-group col-md-1">
                                        <button type="button" id="searchBtn" class="btn btn-primary mt-4">Search</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card" id="rewardcard">
                    <div class="card-body">
                        <div class="basic-form">
                            <form id="updateredeemForm">
                                <input type="hidden" class="form-control" id="id" name="id">
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea class="form-control" id="address" name="address" rows="2" placeholder="Enter your address"></textarea>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="village_city" class="form-label">Village / City</label>
                                        <input type="text" class="form-control" id="village_city" name="village_city" placeholder="Enter your village or city" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="district" class="form-label">District</label>
                                        <input type="text" class="form-control" id="district" name="district" placeholder="Enter your district">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="state" class="form-label">State</label>
                                        <input type="text" class="form-control" id="state" name="state" placeholder="Enter your state">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="create">Select Role</label>
                                        <select name="service" id="service" class="form-control">
                                            @foreach($services as $key => $service)
                                                <option value="{{ $service->service_name }}" data-point="{{ $service->price }}" 
                                                    {{ $key === 0 ? 'selected' : '' }}>
                                                    {{ $service->service_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="pending_reward_points">Available Reward Points</label>
                                        <input type="number" class="form-control" id="pending_reward_points" name="available_reward_points" readonly>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="used_reward_points">Enter Use Reward Points</label>
                                        <input type="number" class="form-control" id="used_reward_points" name="used_reward_points" placeholder="Enter Use Reward Points">
                                        <small id="used_reward_points_error" class="text-danger"></small>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-dark" id="submitBtn">
                                    <span id="spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display:none;"></span>
                                    Submit
                                </button>
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
        $("#rewardcard").hide();
        $("#searchBtn").on("click", function() {
            let vehicleNo = $("#vehicle_no").val();
            let mobileNo = $("#mobile_no").val();
            let types = $("#type").val();
            if (vehicleNo === "" && mobileNo === "") {
                toastr.error('Please enter either Vehicle No or Mobile No.');
                return;
            }
            if (types === "") {
                toastr.error('Please select a type.');
                return;
            }
            $.ajax({
                url: "{{ route('redeem.check') }}"
                , method: "POST"
                , data: {
                    vehicle_no: vehicleNo
                    , mobile_no: mobileNo
                    , type: types
                    , _token: "{{ csrf_token() }}"
                }
                , success: function(response) {
                    if (response.status === "found") {
                        toastr.success("User found: " + response.Reward.vehicle_no + "/" + response.Reward.mobile_no);
                        $("#rewardcard").fadeIn();
                        $("#id").val(response.Reward.id);
                        $("#mobile_no").val(response.Reward.mobile_no);
                        $("#vehicle_no").val(response.Reward.vehicle_no);
                        $("#pending_reward_points").val(response.Reward.pending_reward_points);
                        $("#name").val(response?.redeemhistory?.name ?? '');
                        $("#address").val(response.redeemhistory.address);
                        $("#village_city").val(response.redeemhistory.village_city);
                        $("#district").val(response.redeemhistory.district);
                        $("#state").val(response.redeemhistory.state);
                    } else if (response.status === "not found") {
                        toastr.warning(response.message);
                        $("#rewardcard").hide();
                    }
                }
                , error: function(xhr) {
                    console.log(xhr.responseText);
                    if (xhr.status === 400) {
                        toastr.error("Validation Error: " + JSON.parse(xhr.responseText).message);
                    } else if (xhr.status === 404) {
                        toastr.warning("No data found: " + JSON.parse(xhr.responseText).message);
                    } else {
                        toastr.error("Something went wrong! Please try again.");
                    }
                    $("#rewardcard").hide();
                }
            });
        });
    });

</script>
<script>
    $(document).ready(function() {
        function validateForm() {
            let isValid = true;

            // Name validation (required)
            let name = $("#name").val().trim();
            if (name === "") {
                $("#name").addClass("is-invalid");
                isValid = false;
            } else {
                $("#name").removeClass("is-invalid");
            }

            // Address validation (required)
            let address = $("#address").val().trim();
            if (address === "") {
                $("#address").addClass("is-invalid");
                isValid = false;
            } else {
                $("#address").removeClass("is-invalid");
            }

            // Village/City validation (required)
            let villageCity = $("#village_city").val().trim();
            if (villageCity === "") {
                $("#village_city").addClass("is-invalid");
                isValid = false;
            } else {
                $("#village_city").removeClass("is-invalid");
            }

            // District validation (required)
            let district = $("#district").val().trim();
            if (district === "") {
                $("#district").addClass("is-invalid");
                isValid = false;
            } else {
                $("#district").removeClass("is-invalid");
            }

            // State validation (required)
            let state = $("#state").val().trim();
            if (state === "") {
                $("#state").addClass("is-invalid");
                isValid = false;
            } else {
                $("#state").removeClass("is-invalid");
            }

            // Service selection validation
            let service = $("#service").val();
            if (service === "") {
                $("#service").addClass("is-invalid");
                isValid = false;
            } else {
                $("#service").removeClass("is-invalid");
            }

            // Used Reward Points Validation
            let availableRewardPoints = parseInt($("#pending_reward_points").val()) || 0;
            let usedRewardPoints = parseInt($("#used_reward_points").val()) || 0;
            $("#used_reward_points_error").text("");

            if (usedRewardPoints <= 0) {
                $("#used_reward_points_error").text("Please enter used reward points.");
                $("#used_reward_points").addClass("is-invalid");
                isValid = false;
            } else if (usedRewardPoints > availableRewardPoints) {
                $("#used_reward_points_error").text("Used reward points cannot exceed available reward points.");
                $("#used_reward_points").addClass("is-invalid");
                isValid = false;
            } else {
                $("#used_reward_points").removeClass("is-invalid");
            }

            return isValid;
        }

        // Event binding for validation on input
        $("#used_reward_points, #name, #address, #village_city, #district, #state, #service").on("input change", function() {
            validateForm();
        });

        // Submit button click event
        $("#submitBtn").on("click", function() {
            if (!validateForm()) {
                toastr.error("Please correct the errors before submitting.");
                return;
            }
            let $btn = $(this);
            let $spinner = $("#spinner");
            $btn.prop("disabled", true);
            $spinner.show();

            // Collect all form data
            let formData = {
                id: $("#id").val()
                , name: $("#name").val().trim()
                , address: $("#address").val().trim()
                , village_city: $("#village_city").val().trim()
                , district: $("#district").val().trim()
                , state: $("#state").val().trim()
                , service: $("#service").val()
                , type: $("#type").val()
                , mobile_no: $("#mobile_no").val()
                , vehicle_no: $("#vehicle_no").val()
                , available_reward_points: parseInt($("#pending_reward_points").val()) || 0
                , used_reward_points: parseInt($("#used_reward_points").val()) || 0
                , _token: "{{ csrf_token() }}"
            };

            $.ajax({
                url: "{{ route('redeem.update') }}"
                , method: "POST"
                , data: formData
                , success: function(response) {
                    if (response.status === "success") {
                        toastr.success("Points updated successfully.");
                        window.location.reload();
                    } else {
                        toastr.warning("Something went wrong. Please try again.");
                        window.location.reload();
                    }
                }
                , error: function(xhr) {
                    toastr.error("Something went wrong! Please try again.");
                    console.log(xhr.responseText);
                    $btn.prop("disabled", false);
                    $spinner.hide();
                    window.location.reload();
                }
                , complete: function() {   
                    window.location.reload();
                    $btn.prop("disabled", false);
                    $spinner.hide();
                }
            });
        });
    });

</script>
<script>
    $(document).ready(function() {
        function validateRewardPoints() {
            let availableRewardPoints = parseInt($("#pending_reward_points").val()) || 0;
            let usedRewardPoints = parseInt($("#used_reward_points").val()) || 0;
            $("#used_reward_points_error").text("");
            if (usedRewardPoints <= 0) {
                $("#used_reward_points_error").text("Please enter used reward points.");
                return;
            }
            if (usedRewardPoints > availableRewardPoints) {
                $("#used_reward_points_error").text("Used reward points cannot exceed available reward points.");
                return;
            }
        }
        $("#used_reward_points").on("input change", function() {
            validateRewardPoints();
        });
    });

</script>
<script>
    $(document).ready(function() {
        function updateServiceInfo() {
            let selectedValue = $("#service").val();
            let selectedText = $("#service option:selected").text();
            let selectedPoint = $("#service option:selected").data("point");
            $('#used_reward_points').val(selectedPoint);
        }
        updateServiceInfo();
        $("#service").on("change", function() {
            updateServiceInfo();
        });
    });

</script>
<script>
$(document).ready(function() {
    $('#mobile_no').on('keyup', function() {
        let query = $(this).val();
        if(query.length >= 3) {  // start searching after 3 digits
            $.ajax({
                url: "{{ route('get.vehicles.by.mobile') }}",
                type: "GET",
                data: { mobile_no: query },
                success: function(data) {
                    let suggestionBox = $('#vehicle_suggestions');
                    suggestionBox.empty();

                    if(data.length > 0){
                        suggestionBox.show();
                        $.each(data, function(index, vehicle){
                            suggestionBox.append('<a href="#" class="list-group-item list-group-item-action vehicle-item">'+ vehicle.vehicle_no +'</a>');
                        });
                    } else {
                        suggestionBox.hide();
                    }
                }
            });
        } else {
            $('#vehicle_suggestions').hide();
        }
    });

    // When clicking suggestion → fill input
    $(document).on('click', '.vehicle-item', function(e) {
        e.preventDefault();
        $('#vehicle_no').val($(this).text());
    });
});
</script>
@endsection