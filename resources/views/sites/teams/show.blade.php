@extends('layouts.librenmsv1')

@section('title', $team->name . ' - ' . $site->name)

@section('content')
<div class="container-fluid">

    {{-- TEAM INFO --}}
    <div class="panel panel-default">

        <div class="panel-heading">
            <strong>
                <i class="fa fa-users"></i>
                {{ $team->name }}
            </strong>

            <div class="pull-right">

                <a href="{{ route('sites.show', $site) }}"
                   class="btn btn-default btn-xs">
                    <i class="fa fa-arrow-left"></i> Back
                </a>

            </div>

            <div class="clearfix"></div>
        </div>

        <div class="panel-body">

            <div class="row">

                <div class="col-md-3">
                    <strong>Team Code</strong><br>
                    {{ $team->code ?: '-' }}
                </div>

                <div class="col-md-3">
                    <strong>Team Leader</strong><br>
                    {{ $team->team_leader ?: '-' }}
                </div>

                <div class="col-md-3">
                    <strong>Contact</strong><br>
                    {{ $team->contact_number ?: '-' }}
                </div>

                <div class="col-md-3">
                    <strong>Status</strong><br>

                    @if($team->status === 'active')
                        <span class="label label-success">
                            Active
                        </span>
                    @else
                        <span class="label label-default">
                            Inactive
                        </span>
                    @endif
                </div>

            </div>

            @if($team->notes)
                <hr>

                <div>
                    <strong>Notes</strong><br>
                    {{ $team->notes }}
                </div>
            @endif

        </div>
    </div>


    {{-- TEAM MEMBERS --}}
    <div class="panel panel-default">

        <div class="panel-heading">

            <strong>
                <i class="fa fa-user"></i>
                Team Members
            </strong>

            <div class="pull-right">

                <a href="{{ route('sites.teams.members.create', [$site, $team]) }}"
                   class="btn btn-primary btn-xs"
                   style="color:#fff !important;">

                    <i class="fa fa-plus"></i>
                    ADD MEMBER

                </a>

            </div>

            <div class="clearfix"></div>

        </div>


        <div class="panel-body">

            {{-- SUCCESS MESSAGE --}}
            @if(session('success'))

                <div class="alert alert-success">
                    {{ session('success') }}
                </div>

            @endif


            <table class="table table-striped table-hover">

                <thead>

                    <tr>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th style="width:100px;">Action</th>
                    </tr>

                </thead>


                <tbody>

                @forelse($team->members as $member)

                    <tr>

                        {{-- NAME --}}
                        <td>
                            <strong>
                                {{ $member->name }}
                            </strong>
                        </td>


                        {{-- POSITION --}}
                        <td>

                            @if($member->role === 'team_leader')

                                <span class="label label-primary">
                                    <i class="fa fa-star"></i>
                                    Team Leader
                                </span>

                            @elseif($member->role === 'team_member')

                                <span class="label label-default">
                                    Team Member
                                </span>

                            @else

                                -

                            @endif

                        </td>


                        {{-- PHONE --}}
                        <td>
                            {{ $member->phone ?: '-' }}
                        </td>


                        {{-- EMAIL --}}
                        <td>
                            {{ $member->email ?: '-' }}
                        </td>


                        {{-- STATUS --}}
                        <td>

                            @if($member->status === 'active')

                                <span class="label label-success">
                                    Active
                                </span>

                            @else

                                <span class="label label-default">
                                    Inactive
                                </span>

                            @endif

                        </td>


                        {{-- ACTION --}}
                        <td style="white-space:nowrap;">

                            <form method="POST"
                                  action="{{ route('sites.teams.members.destroy', [$site, $team, $member]) }}"
                                  style="display:inline-block; margin:0;">

                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="btn btn-danger btn-xs"
                                        title="Delete Member"
                                        onclick="return confirm('Delete this team member?')">

                                    <i class="fa fa-trash"></i>

                                </button>

                            </form>

                        </td>

                    </tr>


                @empty

                    <tr>

                        <td colspan="6"
                            class="text-center">

                            No team members added yet.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>
@endsection
