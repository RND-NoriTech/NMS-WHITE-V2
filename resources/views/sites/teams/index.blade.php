@extends('layouts.librenmsv1')

@section('title', 'Teams - ' . $site->name)

@section('content')
<div class="container-fluid">

    <div class="panel panel-default">

        <div class="panel-heading">
            <strong>
                <i class="fa fa-users"></i>
                Teams — {{ $site->name }}
            </strong>

            <div class="pull-right">
                <a href="{{ route('sites.teams.create', $site) }}"
                   class="btn btn-primary btn-xs"
                   style="color:#fff !important;">
                    <i class="fa fa-plus"></i> ADD
                </a>

                <a href="{{ route('sites.show', $site) }}"
                   class="btn btn-default btn-xs">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
            </div>

            <div class="clearfix"></div>
        </div>

        <div class="panel-body">

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <table class="table table-striped table-hover">

                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Team Name</th>
                        <th>Team Leader</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th style="width:120px;">Action</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($teams as $team)

                    <tr>

                        <td>
                            {{ $team->code ?: '-' }}
                        </td>

                        <td>
                            <a href="{{ route('sites.teams.show', [$site, $team]) }}">
                                <strong>{{ $team->name }}</strong>
                            </a>
                        </td>

                        <td>
                            {{ $team->team_leader ?: '-' }}
                        </td>

                        <td>
                            {{ $team->contact_number ?: '-' }}
                        </td>

                        <td>
                            @if($team->status === 'active')
                                <span class="label label-success">
                                    Active
                                </span>
                            @else
                                <span class="label label-default">
                                    Inactive
                                </span>
                            @endif
                        </td>

                        <td style="white-space:nowrap;">

                            {{-- VIEW --}}
                            <a href="{{ route('sites.teams.show', [$site, $team]) }}"
                               class="btn btn-info btn-xs"
                               title="View Team">
                                <i class="fa fa-eye"></i>
                            </a>

                            {{-- EDIT --}}
                            <a href="{{ route('sites.teams.edit', [$site, $team]) }}"
                               class="btn btn-warning btn-xs"
                               title="Edit Team">
                                <i class="fa fa-pencil"></i>
                            </a>

                            {{-- DELETE --}}
                            <form method="POST"
                                  action="{{ route('sites.teams.destroy', [$site, $team]) }}"
                                  style="display:inline-block; margin:0;">

                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="btn btn-danger btn-xs"
                                        title="Delete Team"
                                        onclick="return confirm('Delete this team?')">
                                    <i class="fa fa-trash"></i>
                                </button>

                            </form>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6" class="text-center">
                            No teams added yet.
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>
@endsection
