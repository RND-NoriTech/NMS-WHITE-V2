@extends('layouts.librenmsv1')

@section('title', 'Add Hardware - ' . $site->name)

@section('content')

<div class="container-fluid">

    <div class="panel panel-default">

        <div class="panel-heading">
            <strong>
                <i class="fa fa-plus"></i>
                Add Hardware — {{ $site->name }}
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
                  action="{{ route('sites.hardware.store', $site) }}">

                @csrf

                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Model *</label>

                            <input type="text"
                                   name="model"
                                   class="form-control"
                                   value="{{ old('model') }}"
                                   placeholder="Example: ES205G"
                                   required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Device ID *</label>

                            <input type="text"
                                   name="device_id"
                                   class="form-control"
                                   value="{{ old('device_id') }}"
                                   placeholder="Example: SW-NOW-001"
                                   required>
                        </div>
                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Gateway</label>

                            <input type="text"
                                   name="gateway"
                                   class="form-control"
                                   value="{{ old('gateway') }}"
                                   placeholder="Example: 192.168.88.1">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>MAC Address</label>

                            <input type="text"
                                   name="mac_address"
                                   class="form-control"
                                   value="{{ old('mac_address') }}"
                                   placeholder="AA:BB:CC:DD:EE:FF">
                        </div>
                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Username</label>

                            <input type="text"
                                   name="username"
                                   class="form-control"
                                   value="{{ old('username') }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Password</label>

                            <input type="password"
                                   name="password"
                                   class="form-control"
                                   autocomplete="new-password">
                        </div>
                    </div>

                </div>

                <div class="row">

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

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status</label>

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
                    Save Hardware

                </button>

                <a href="{{ route('sites.hardware.index', $site) }}"
                   class="btn btn-default">

                    Cancel

                </a>

            </form>

        </div>
    </div>

</div>

@endsection