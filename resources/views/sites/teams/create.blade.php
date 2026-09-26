@extends('layouts.librenmsv1')

@section('title', 'Add Team - ' . $site->name)

@section('content')
<div class="container-fluid">

    <div class="panel panel-default">

        <div class="panel-heading">
            <strong>
                <i class="fa fa-users"></i>
                Add Team — {{ $site->name }}
            </strong>

            <div class="pull-right">
                <a href="{{ route('sites.teams.index', $site) }}"
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
                  action="{{ route('sites.teams.store', $site) }}">

                @csrf

                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Team Name</label>

                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   value="{{ old('name') }}"
                                   placeholder="Example: Team WiFi A"
                                   required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Team Code</label>

                            <input type="text"
                                   name="code"
                                   class="form-control"
                                   value="{{ old('code') }}"
                                   placeholder="Example: WIFI-A">
                        </div>
                    </div>

                </div>


                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Team Leader</label>

                            <input type="text"
                                   name="team_leader"
                                   class="form-control"
                                   value="{{ old('team_leader') }}"
                                   placeholder="Example: Danial">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Contact Number</label>

                            <input type="text"
                                   name="contact_number"
                                   class="form-control"
                                   value="{{ old('contact_number') }}"
                                   placeholder="Example: 0123456789">
                        </div>
                    </div>

                </div>


                <div class="form-group">
                    <label>Status</label>

                    <select name="status"
                            class="form-control">

                        <option value="active"
                            {{ old('status', 'active') === 'active' ? 'selected' : '' }}>
                            Active
                        </option>

                        <option value="inactive"
                            {{ old('status') === 'inactive' ? 'selected' : '' }}>
                            Inactive
                        </option>

                    </select>
                </div>


                <div class="form-group">
                    <label>Notes</label>

                    <textarea name="notes"
                              class="form-control"
                              rows="4"
                              placeholder="Optional notes">{{ old('notes') }}</textarea>
                </div>


                <button type="submit"
                        class="btn btn-primary">
                    <i class="fa fa-save"></i>
                    Save Team
                </button>

                <a href="{{ route('sites.teams.index', $site) }}"
                   class="btn btn-default">
                    Cancel
                </a>

            </form>

        </div>
    </div>

</div>
@endsection
