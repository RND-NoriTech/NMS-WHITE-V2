@extends('layouts.librenmsv1')

@section('title', 'Add Member - ' . $team->name)

@section('content')
<div class="container-fluid">

    <div class="panel panel-default">

        <div class="panel-heading">
            <strong>
                <i class="fa fa-user-plus"></i>
                Add Team Member — {{ $team->name }}
            </strong>

            <div class="pull-right">
                <a href="{{ route('sites.teams.show', [$site, $team]) }}"
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
                  action="{{ route('sites.teams.members.store', [$site, $team]) }}">

                @csrf

                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Name</label>

                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   value="{{ old('name') }}"
                                   placeholder="Example: Danial"
                                   required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Position</label>

                            <select name="role"
                                    class="form-control"
                                    required>

                                <option value="">
                                    -- Select Position --
                                </option>

                                <option value="team_leader"
                                    {{ old('role') === 'team_leader' ? 'selected' : '' }}>
                                    Team Leader
                                </option>

                                <option value="team_member"
                                    {{ old('role') === 'team_member' ? 'selected' : '' }}>
                                    Team Member
                                </option>

                            </select>
                        </div>
                    </div>

                </div>


                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Phone</label>

                            <input type="text"
                                   name="phone"
                                   class="form-control"
                                   value="{{ old('phone') }}"
                                   placeholder="Example: 0123456789">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email</label>

                            <input type="email"
                                   name="email"
                                   class="form-control"
                                   value="{{ old('email') }}"
                                   placeholder="Example: danial@example.com">
                        </div>
                    </div>

                </div>


                <div class="form-group">
                    <label>Status</label>

                    <select name="status"
                            class="form-control"
                            required>

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
                    Save Member
                </button>

                <a href="{{ route('sites.teams.show', [$site, $team]) }}"
                   class="btn btn-default">
                    Cancel
                </a>

            </form>

        </div>
    </div>

</div>
@endsection
