@extends('layouts.librenmsv1')

@section('title', 'Add Vehicle - ' . $site->name)

@section('content')

<div class="container-fluid">

    <div class="panel panel-default">

        <div class="panel-heading">
            <strong>
                <i class="fa fa-plus"></i>
                Add Vehicle — {{ $site->name }}
            </strong>
        </div>

        <div class="panel-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin-bottom:0;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST"
                  action="{{ route('sites.vehicles.store', $site) }}">

                @csrf

                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Vehicle Name *</label>

                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   value="{{ old('name') }}"
                                   placeholder="Example: Patrol Truck 01"
                                   required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Registration Number *</label>

                            <input type="text"
                                   name="registration_number"
                                   class="form-control"
                                   value="{{ old('registration_number') }}"
                                   placeholder="Example: ABC-1234"
                                   required>
                        </div>
                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Vehicle Type</label>

                            <input type="text"
                                   name="vehicle_type"
                                   class="form-control"
                                   value="{{ old('vehicle_type') }}"
                                   placeholder="Example: Pickup">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Make</label>

                            <input type="text"
                                   name="make"
                                   class="form-control"
                                   value="{{ old('make') }}"
                                   placeholder="Example: Toyota">
                        </div>
                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Model</label>

                            <input type="text"
                                   name="model"
                                   class="form-control"
                                   value="{{ old('model') }}"
                                   placeholder="Example: Hilux">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Year</label>

                            <input type="number"
                                   name="year"
                                   class="form-control"
                                   value="{{ old('year') }}"
                                   min="1900"
                                   max="2100"
                                   placeholder="Example: 2024">
                        </div>
                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Color</label>

                            <input type="text"
                                   name="color"
                                   class="form-control"
                                   value="{{ old('color') }}"
                                   placeholder="Example: White">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Driver Name</label>

                            <input type="text"
                                   name="driver_name"
                                   class="form-control"
                                   value="{{ old('driver_name') }}"
                                   placeholder="Example: Ahmad Ali">
                        </div>
                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Driver Phone</label>

                            <input type="text"
                                   name="driver_phone"
                                   class="form-control"
                                   value="{{ old('driver_phone') }}"
                                   placeholder="Example: +60 12-345 6789">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tracker Device ID</label>

                            <input type="text"
                                   name="tracker_device_id"
                                   class="form-control"
                                   value="{{ old('tracker_device_id') }}"
                                   placeholder="Example: TRK-001">
                        </div>
                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tracker IMEI</label>

                            <input type="text"
                                   name="tracker_imei"
                                   class="form-control"
                                   value="{{ old('tracker_imei') }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tracker MAC Address</label>

                            <input type="text"
                                   name="tracker_mac_address"
                                   class="form-control"
                                   value="{{ old('tracker_mac_address') }}"
                                   placeholder="AA:BB:CC:DD:EE:FF">
                        </div>
                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tracker IP Address</label>

                            <input type="text"
                                   name="tracker_ip_address"
                                   class="form-control"
                                   value="{{ old('tracker_ip_address') }}"
                                   placeholder="Example: 10.10.10.5">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Team</label>

                            <select name="team_id"
                                    class="form-control">

                                <option value="">
                                    -- No Team --
                                </option>

                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}"
                                        {{ old('team_id') == $team->id ? 'selected' : '' }}>
                                        {{ $team->name }}
                                    </option>
                                @endforeach

                            </select>
                        </div>
                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status *</label>

                            <select name="status"
                                    class="form-control">

                                <option value="active">
                                    Active
                                </option>

                                <option value="inactive">
                                    Inactive
                                </option>

                                <option value="maintenance">
                                    Maintenance
                                </option>

                                <option value="offline">
                                    Offline
                                </option>

                            </select>
                        </div>
                    </div>

                </div>

                <div class="form-group">
                    <label>Notes</label>

                    <textarea name="notes"
                              class="form-control"
                              rows="4">{{ old('notes') }}</textarea>
                </div>

                <button type="submit"
                        class="btn btn-primary">

                    <i class="fa fa-save"></i>
                    Save Vehicle

                </button>

                <a href="{{ route('sites.vehicles.index', $site) }}"
                   class="btn btn-default">

                    Cancel

                </a>

            </form>

        </div>
    </div>

</div>

@endsection
