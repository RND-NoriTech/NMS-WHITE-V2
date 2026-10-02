@extends('layouts.librenmsv1')

@section('title', 'Vehicles - ' . $site->name)

@section('content')

<div class="container-fluid">

    <div class="panel panel-default">

        <div class="panel-heading clearfix">

            <strong>
                <i class="fa fa-bus"></i>
                Vehicles — {{ $site->name }}
            </strong>

            <div class="pull-right">

                <a href="{{ route('sites.vehicles.create', $site) }}"
                   class="btn btn-primary btn-sm">

                    <i class="fa fa-plus"></i>
                    Add Vehicle

                </a>

                <a href="{{ route('sites.show', $site) }}"
                   class="btn btn-default btn-sm">

                    <i class="fa fa-arrow-left"></i>
                    Back

                </a>

            </div>

        </div>


        <div class="panel-body">

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif


            <div class="table-responsive">

                <table class="table table-bordered table-hover table-striped">

                    <thead>
                    <tr>
                        <th>VEHICLE</th>
                        <th>REGISTRATION</th>
                        <th>TYPE</th>
                        <th>MAKE / MODEL</th>
                        <th>DRIVER</th>
                        <th>TRACKER ID</th>
                        <th>LAST SEEN</th>
                        <th>TEAM</th>
                        <th>STATUS</th>
                        <th width="150">ACTION</th>
                    </tr>
                    </thead>


                    <tbody>

                    @forelse($vehicles as $item)

                        <tr>

                            <td>
                                <strong>
                                    {{ $item->name }}
                                </strong>
                            </td>


                            <td>
                                {{ $item->registration_number }}
                            </td>


                            <td>
                                {{ $item->vehicle_type ?: '-' }}
                            </td>


                            <td>

                                @if($item->make || $item->model)

                                    {{ trim(($item->make ?: '') . ' ' . ($item->model ?: '')) }}

                                @else

                                    -

                                @endif

                            </td>


                            <td>
                                {{ $item->driver_name ?: '-' }}
                            </td>


                            <td>
                                {{ $item->tracker_device_id ?: '-' }}
                            </td>


                            <td>

                                @if($item->last_seen_at)

                                    {{ $item->last_seen_at->format('Y-m-d H:i') }}

                                @else

                                    -

                                @endif

                            </td>


                            <td>
                                {{ $item->team?->name ?: '-' }}
                            </td>


                            <td>

                                @if($item->status === 'active')

                                    <span class="label label-success">
                                        Active
                                    </span>

                                @elseif($item->status === 'maintenance')

                                    <span class="label label-warning">
                                        Maintenance
                                    </span>

                                @elseif($item->status === 'offline')

                                    <span class="label label-danger">
                                        Offline
                                    </span>

                                @else

                                    <span class="label label-default">
                                        Inactive
                                    </span>

                                @endif

                            </td>


                            <td>

                                <a
                                    href="{{ route('sites.vehicles.show', [$site, $item]) }}"
                                    class="btn btn-default btn-xs"
                                    title="View">

                                    <i class="fa fa-eye"></i>

                                </a>


                                <a
                                    href="{{ route('sites.vehicles.edit', [$site, $item]) }}"
                                    class="btn btn-warning btn-xs"
                                    title="Edit">

                                    <i class="fa fa-pencil"></i>

                                </a>


                                <form
                                    method="POST"
                                    action="{{ route('sites.vehicles.destroy', [$site, $item]) }}"
                                    style="display:inline-block"
                                    onsubmit="return confirm('Delete this vehicle?');">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-danger btn-xs"
                                        title="Delete">

                                        <i class="fa fa-trash"></i>

                                    </button>

                                </form>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="10"
                                class="text-center text-muted">

                                No vehicles added yet.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection
