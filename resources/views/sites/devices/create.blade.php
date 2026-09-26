@extends('layouts.librenmsv1')

@section('title', 'Assign Device - ' . $site->name)

@section('content')
<div class="container-fluid">

    <div class="panel panel-default">

        <div class="panel-heading">
            <strong>
                <i class="fa fa-server"></i>
                Assign Existing Device — {{ $site->name }}
            </strong>

            <div class="pull-right">
                <a href="{{ route('sites.devices.index', $site) }}"
                   class="btn btn-default btn-xs">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
            </div>

            <div class="clearfix"></div>
        </div>

        <div class="panel-body">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin-bottom:0;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST"
                  action="{{ route('sites.devices.store', $site) }}">

                @csrf

                <div class="form-group">
                    <label>LibreNMS Device</label>

                    <select name="device_id"
                            class="form-control"
                            required>

                        <option value="">
                            -- Select Device --
                        </option>

                        @foreach($devices as $device)
                            <option value="{{ $device->device_id }}"
                                {{ old('device_id') == $device->device_id ? 'selected' : '' }}>
                                {{ $device->hostname }}
                                @if($device->sysName)
                                    — {{ $device->sysName }}
                                @endif
                            </option>
                        @endforeach

                    </select>
                </div>

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

                <div class="form-group">
                    <label>Notes</label>

                    <textarea name="notes"
                              class="form-control"
                              rows="3">{{ old('notes') }}</textarea>
                </div>

                <button type="submit"
                        class="btn btn-primary">
                    <i class="fa fa-link"></i>
                    Assign Device
                </button>

            </form>

        </div>
    </div>

</div>
@endsection
