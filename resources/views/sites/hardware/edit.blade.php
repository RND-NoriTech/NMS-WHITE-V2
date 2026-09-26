@extends('layouts.librenmsv1')

@section('title', 'Edit Hardware - ' . $site->name)

@section('content')

<div class="container-fluid">

    <div class="panel panel-default">

        <div class="panel-heading clearfix">

            <strong>
                <i class="fa fa-pencil"></i>
                Edit Hardware — {{ $site->name }}
            </strong>

            <div class="pull-right">

                <a href="{{ route('sites.hardware.index', $site) }}"
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
                action="{{ route('sites.hardware.update', [$site, $hardware]) }}">

                @csrf
                @method('PUT')


                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Model *</label>

                            <input
                                type="text"
                                name="model"
                                class="form-control"
                                value="{{ old('model', $hardware->model) }}"
                                required>

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Device ID *</label>

                            <input
                                type="text"
                                name="device_id"
                                class="form-control"
                                value="{{ old('device_id', $hardware->device_id) }}"
                                required>

                        </div>

                    </div>

                </div>


                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Gateway</label>

                            <input
                                type="text"
                                name="gateway"
                                class="form-control"
                                value="{{ old('gateway', $hardware->gateway) }}"
                                placeholder="Example: 192.168.88.1">

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="form-group">

                            <label>MAC Address</label>

                            <input
                                type="text"
                                name="mac_address"
                                class="form-control"
                                value="{{ old('mac_address', $hardware->mac_address) }}"
                                placeholder="AA:BB:CC:DD:EE:FF">

                        </div>

                    </div>

                </div>


                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Username</label>

                            <input
                                type="text"
                                name="username"
                                class="form-control"
                                value="{{ old('username', $hardware->username) }}">

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Password</label>

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                autocomplete="new-password"
                                placeholder="Leave blank to keep current password">

                            <small class="text-muted">
                                Leave blank if you do not want to change the current password.
                            </small>

                        </div>

                    </div>

                </div>


                <div class="row">

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
                                        {{ old('team_id', $hardware->team_id) == $team->id ? 'selected' : '' }}>

                                        {{ $team->name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Status</label>

                            <select
                                name="status"
                                class="form-control">

                                <option
                                    value="active"
                                    {{ old('status', $hardware->status) === 'active' ? 'selected' : '' }}>
                                    Active
                                </option>

                                <option
                                    value="inactive"
                                    {{ old('status', $hardware->status) === 'inactive' ? 'selected' : '' }}>
                                    Inactive
                                </option>

                                <option
                                    value="maintenance"
                                    {{ old('status', $hardware->status) === 'maintenance' ? 'selected' : '' }}>
                                    Maintenance
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
                        rows="4">{{ old('notes', $hardware->notes) }}</textarea>

                </div>


                <button
                    type="submit"
                    class="btn btn-primary">

                    <i class="fa fa-save"></i>
                    Update Hardware

                </button>


                <a
                    href="{{ route('sites.hardware.index', $site) }}"
                    class="btn btn-default">

                    Cancel

                </a>

            </form>

        </div>

    </div>

</div>

@endsection