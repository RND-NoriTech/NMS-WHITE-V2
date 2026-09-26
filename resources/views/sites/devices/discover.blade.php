@extends('layouts.librenmsv1')

@section('title', 'Discover Device - ' . $site->name)

@section('content')
<div class="container-fluid">

    <div class="panel panel-default">

        <div class="panel-heading">
            <strong>
                <i class="fa fa-search"></i>
                Discover Device — {{ $site->name }}
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
                            <li style="white-space:pre-line;">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST"
                  action="{{ route('sites.devices.discover.store', $site) }}">

                @csrf

                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>IP Address / DNS Hostname</label>

                            <input type="text"
                                   name="hostname"
                                   class="form-control"
                                   value="{{ old('hostname') }}"
                                   placeholder="Example: 10.50.1.2 or switch01.site.local"
                                   required>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>SNMP Version</label>

                            <select class="form-control" disabled>
                                <option>SNMP v2c</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Community</label>

                            <input type="text"
                                   name="community"
                                   class="form-control"
                                   value="{{ old('community') }}"
                                   placeholder="Example: monitoring"
                                   required>
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
                            <label>Notes</label>

                            <input type="text"
                                   name="notes"
                                   class="form-control"
                                   value="{{ old('notes') }}"
                                   placeholder="Optional notes">
                        </div>
                    </div>

                </div>

                <button type="submit"
                        class="btn btn-primary">

                    <i class="fa fa-search"></i>
                    Discover & Add Device

                </button>

            </form>

        </div>
    </div>

</div>
@endsection