@extends('layout.main-layout')
@section('title', config('app.name') . ' || Reward Master')
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
                        <h4 class="card-title mb-0">Reward Master</h4>
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
                                        <th>Regular Reward Price</th>
                                        <th>Regular Reward Points</th>
                                        <th>Commercial Reward Price</th>
                                        <th>Commercial Reward Points</th>
                                        <th>Tractor Reward Price</th>
                                        <th>Tractor Reward Points</th>
                                        <th>Regular Price</th>
                                        <th>Regular Gift Point</th>
                                        <th>Commercial Price</th>
                                        <th>Commercial Gift Point</th>
                                        <th>Tractor Price</th>
                                        <th>Tractor Gift Point</th>
                                        <th>Expiry Days</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reward as $rew)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>₹ {{ $rew->regular_reward_price }}</td>
                                        <td>{{ $rew->regular_reward_points }}</td>
                                        <td>₹ {{ $rew->commercial_reward_price }}</td>
                                        <td>{{ $rew->commercial_reward_points }}</td>
                                        <td>₹ {{ $rew->tractor_reward_price }}</td>
                                        <td>{{ $rew->tractor_reward_points }}</td>
                                        <td>₹ {{ $rew->regular_price }}</td>
                                        <td>{{ $rew->regular_gift_point }}</td>
                                        <td>₹ {{ $rew->commercial_price }}</td>
                                        <td>{{ $rew->commercial_gift_point }}</td>
                                        <td>₹ {{ $rew->tractor_price }}</td>
                                        <td>{{ $rew->tractor_gift_point }}</td>
                                        <td>{{ $rew->expiry_days }} days</td>
                                        <td>
                                            <a href="{{ route('edit.reward.master', ['id' => Crypt::encryptString($rew->id)]) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i>
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
@if(session('success'))
<script>
    toastr.success("{{ session('success') }}", "Success");
</script>
@endif
@endsection