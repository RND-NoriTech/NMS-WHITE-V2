@extends('layouts.librenmsv1')

@section('title', 'Sites')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">

            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong>Sites</strong>

		<a href="{{ route('sites.create') }}"
		   class="btn btn-primary btn-sm pull-right"
		   style="font-weight:600; color:#fff !important;">
		    <i class="fa fa-plus"></i> ADD
		</a>
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
                                <th>Site Name</th>
                                <th>Address</th>
                                <th>Contact</th>
                                <th>Status</th>
                                <th width="160">Action</th>
                            </tr>
                        </thead>

                        <tbody>
@forelse($sites as $site)
    <tr>
        <td>{{ $site->code }}</td>

        <td>{{ $site->name }}</td>

        <td>{{ $site->address }}</td>

        <td>
            <strong>{{ $site->teams->first()?->team_leader ?: '-' }}</strong>

            @if($site->teams->first()?->contact_number)
                <br>
                <small>{{ $site->teams->first()?->contact_number }}</small>
            @endif
        </td>

        <td>
            @if($site->status === 'active')
                <span class="label label-success">Active</span>
            @else
                <span class="label label-default">Inactive</span>
            @endif
        </td>

        <td>
            <a href="{{ route('sites.show', $site) }}"
               class="btn btn-info btn-xs"
               title="View Details">
                <i class="fa fa-eye"></i> View
            </a>

            <a href="{{ route('sites.edit', $site) }}"
               class="btn btn-warning btn-xs">
                <i class="fa fa-pencil"></i> Edit
            </a>

            <form action="{{ route('sites.destroy', $site) }}"
                  method="POST"
                  style="display:inline;">
                @csrf
                @method('DELETE')

                <button type="submit"
                        class="btn btn-danger btn-xs"
                        onclick="return confirm('Delete this site?')">
                    <i class="fa fa-trash"></i> Delete
                </button>
            </form>
        </td>
    </tr>

@empty
    <tr>
        <td colspan="6" class="text-center">
            No sites added yet.
        </td>
    </tr>
@endforelse
</tbody>
                    </table>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
