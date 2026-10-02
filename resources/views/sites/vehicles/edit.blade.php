@extends('layouts.librenmsv1')

@section('title', 'Edit Vehicle - ' . $site->name)

@section('content')

<div class="container-fluid">

    <div class="panel panel-default">

        <div class="panel-heading clearfix">

            <strong>
                <i class="fa fa-pencil"></i>
                Edit Vehicle — {{ $site->name }}
            </strong>

            <div class="pull-right">

                <a href="{{ route('sites.vehicles.index', $site) }}"
                   class="btn btn-default btn-sm">

                    <i class="fa fa-arrow-left"></i>
                    Back

                </a>

            </div>

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


            <form
                method="POST"
                action="{{ route('sites.vehicles.update', [$site, $vehicle]) }}">

                @csrf
                @method('PUT')


                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Vehicle Name *</label>

                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                value="{{ old('name', $vehicle->name) }}"
                                required>

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Registration Number *</label>

                            <input
                                type="text"
                                name="registration_number"
                                class="form-control"
                                value="{{ old('registration_number', $vehicle->registration_number) }}"
                                required>

                        </div>

                    </div>

                </div>


                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Vehicle Type</label>

                            <input
                                type="text"
                                name="vehicle_type"
                                class="form-control"
                                value="{{ old('vehicle_type', $vehicle->vehicle_type) }}">

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Make</label>

                            <input
                                type="text"
                                name="make"
                                class="form-control"
                                value="{{ old('make', $vehicle->make) }}">

                        </div>

                    </div>

                </div>


                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Model</label>

                            <input
                                type="text"
                                name="model"
                                class="form-control"
                                value="{{ old('model', $vehicle->model) }}">

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Year</label>

                            <input
                                type="number"
                                name="year"
                                class="form-control"
                                value="{{ old('year', $vehicle->year) }}"
                                min="1900"
                                max="2100">

                        </div>

                    </div>

                </div>


                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Color</label>

                            <input
                                type="text"
                                name="color"
                                class="form-control"
                                value="{{ old('color', $vehicle->color) }}">

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Driver Name</label>

                            <input
                                type="text"
                                name="driver_name"
                                class="form-control"
                                value="{{ old('driver_name', $vehicle->driver_name) }}">

                        </div>

                    </div>

                </div>


                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Driver Phone</label>

                            <input
                                type="text"
                                name="driver_phone"
                                class="form-control"
                                value="{{ old('driver_phone', $vehicle->driver_phone) }}">

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Tracker Device ID</label>

                            <input
                                type="text"
                                name="tracker_device_id"
                                class="form-control"
                                value="{{ old('tracker_device_id', $vehicle->tracker_device_id) }}">

                        </div>

                    </div>

                </div>


                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Tracker IMEI</label>

                            <input
                                type="text"
                                name="tracker_imei"
                                class="form-control"
                                value="{{ old('tracker_imei', $vehicle->tracker_imei) }}">

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Tracker MAC Address</label>

                            <input
                                type="text"
                                name="tracker_mac_address"
                                class="form-control"
                                value="{{ old('tracker_mac_address', $vehicle->tracker_mac_address) }}"
                                placeholder="AA:BB:CC:DD:EE:FF">

                        </div>

                    </div>

                </div>


                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Tracker IP Address</label>

                            <input
                                type="text"
                                name="tracker_ip_address"
                                class="form-control"
                                value="{{ old('tracker_ip_address', $vehicle->tracker_ip_address) }}"
                                placeholder="Example: 10.10.10.5">

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Team</label>

                            <select
                                name="team_id"
                                class="form-control">

                                <option value="">
                                    -- No Team --
                                </option>

                                @foreach($teams as $team)

                                    <option
                                        value="{{ $team->id }}"
                                        {{ old('team_id', $vehicle->team_id) == $team->id ? 'selected' : '' }}>

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

                            <select
                                name="status"
                                class="form-control">

                                <option
                                    value="active"
                                    {{ old('status', $vehicle->status) === 'active' ? 'selected' : '' }}>
                                    Active
                                </option>

                                <option
                                    value="inactive"
                                    {{ old('status', $vehicle->status) === 'inactive' ? 'selected' : '' }}>
                                    Inactive
                                </option>

                                <option
                                    value="maintenance"
                                    {{ old('status', $vehicle->status) === 'maintenance' ? 'selected' : '' }}>
                                    Maintenance
                                </option>

                                <option
                                    value="offline"
                                    {{ old('status', $vehicle->status) === 'offline' ? 'selected' : '' }}>
                                    Offline
                                </option>

                            </select>

                        </div>

                    </div>

                </div>


                <div class="form-group">

                    <label>Notes</label>

                    <textarea
                        name="notes"
                        class="form-control"
                        rows="4">{{ old('notes', $vehicle->notes) }}</textarea>

                </div>


                <button
                    type="submit"
                    class="btn btn-primary">

                    <i class="fa fa-save"></i>
                    Update Vehicle

                </button>


                <a
                    href="{{ route('sites.vehicles.index', $site) }}"
                    class="btn btn-default">

                    Cancel

                </a>

            </form>

        </div>

    </div>

</div>

@endsection
