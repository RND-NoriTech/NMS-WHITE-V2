@extends('layouts.librenmsv1')

@section('title', $vehicle->name . ' - ' . $site->name)

@section('content')
<div class="container-fluid">

    {{-- VEHICLE HEADER --}}
    <div class="panel panel-default nms-vehicle-summary">

        <div class="panel-heading">

            <strong>
                <i class="fa fa-bus"></i>
                {{ $vehicle->name }}
            </strong>

            <div class="pull-right">

                <a href="{{ route('sites.vehicles.edit', [$site, $vehicle]) }}"
                   class="btn btn-warning btn-xs"
                   title="Edit Vehicle">
                    <i class="fa fa-pencil"></i> Edit
                </a>

                <a href="{{ route('sites.vehicles.index', $site) }}"
                   class="btn btn-default btn-xs"
                   title="Back to Vehicles">
                    <i class="fa fa-arrow-left"></i> Back
                </a>

            </div>

            <div class="clearfix"></div>

        </div>

    </div>


    <div class="row">

        {{-- VEHICLE INFORMATION --}}
        <div class="col-md-6">

            <div class="panel panel-default">

                <div class="panel-heading">
                    <strong>
                        <i class="fa fa-car"></i>
                        Vehicle Information
                    </strong>
                </div>

                <div class="panel-body">

                    <div class="row">

                        <div class="col-md-6 col-sm-6 summary-item">
                            <div class="summary-label">Name</div>
                            <div>{{ $vehicle->name }}</div>
                        </div>

                        <div class="col-md-6 col-sm-6 summary-item">
                            <div class="summary-label">Registration</div>
                            <div>{{ $vehicle->registration_number }}</div>
                        </div>

                        <div class="col-md-6 col-sm-6 summary-item">
                            <div class="summary-label">Type</div>
                            <div>{{ $vehicle->vehicle_type ?: '-' }}</div>
                        </div>

                        <div class="col-md-6 col-sm-6 summary-item">
                            <div class="summary-label">Make</div>
                            <div>{{ $vehicle->make ?: '-' }}</div>
                        </div>

                        <div class="col-md-6 col-sm-6 summary-item">
                            <div class="summary-label">Model</div>
                            <div>{{ $vehicle->model ?: '-' }}</div>
                        </div>

                        <div class="col-md-6 col-sm-6 summary-item">
                            <div class="summary-label">Year</div>
                            <div>{{ $vehicle->year ?: '-' }}</div>
                        </div>

                        <div class="col-md-6 col-sm-6 summary-item">
                            <div class="summary-label">Color</div>
                            <div>{{ $vehicle->color ?: '-' }}</div>
                        </div>

                        <div class="col-md-6 col-sm-6 summary-item">
                            <div class="summary-label">Status</div>

                            <div>
                                @if($vehicle->status === 'active')
                                    <span class="label label-success">Active</span>
                                @elseif($vehicle->status === 'maintenance')
                                    <span class="label label-warning">Maintenance</span>
                                @elseif($vehicle->status === 'offline')
                                    <span class="label label-danger">Offline</span>
                                @else
                                    <span class="label label-default">Inactive</span>
                                @endif
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- DRIVER --}}
        <div class="col-md-6">

            <div class="panel panel-default">

                <div class="panel-heading">
                    <strong>
                        <i class="fa fa-user"></i>
                        Driver
                    </strong>
                </div>

                <div class="panel-body">

                    <div class="row">

                        <div class="col-md-6 col-sm-6 summary-item">
                            <div class="summary-label">Driver Name</div>
                            <div>{{ $vehicle->driver_name ?: '-' }}</div>
                        </div>

                        <div class="col-md-6 col-sm-6 summary-item">
                            <div class="summary-label">Driver Phone</div>
                            <div>{{ $vehicle->driver_phone ?: '-' }}</div>
                        </div>

                        <div class="col-md-6 col-sm-6 summary-item">
                            <div class="summary-label">Team</div>
                            <div>{{ $vehicle->team?->name ?: '-' }}</div>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- TRACKING DEVICE --}}
        <div class="col-md-6">

            <div class="panel panel-default">

                <div class="panel-heading">
                    <strong>
                        <i class="fa fa-map-marker"></i>
                        Tracking Device
                    </strong>
                </div>

                <div class="panel-body">

                    <div class="row">

                        <div class="col-md-6 col-sm-6 summary-item">
                            <div class="summary-label">Tracker Device ID</div>
                            <div>{{ $vehicle->tracker_device_id ?: '-' }}</div>
                        </div>

                        <div class="col-md-6 col-sm-6 summary-item">
                            <div class="summary-label">IMEI</div>
                            <div>{{ $vehicle->tracker_imei ?: '-' }}</div>
                        </div>

                        <div class="col-md-6 col-sm-6 summary-item">
                            <div class="summary-label">MAC Address</div>
                            <div>{{ $vehicle->tracker_mac_address ?: '-' }}</div>
                        </div>

                        <div class="col-md-6 col-sm-6 summary-item">
                            <div class="summary-label">IP Address</div>
                            <div>{{ $vehicle->tracker_ip_address ?: '-' }}</div>
                        </div>

                        <div class="col-md-6 col-sm-6 summary-item">
                            <div class="summary-label">Last Seen</div>
                            <div>
                                @if($vehicle->last_seen_at)
                                    {{ $vehicle->last_seen_at->format('Y-m-d H:i:s') }}
                                @else
                                    -
                                @endif
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- LOCATION --}}
        <div class="col-md-6">

            <div class="panel panel-default">

                <div class="panel-heading">
                    <strong>
                        <i class="fa fa-globe"></i>
                        Location
                    </strong>
                </div>

                <div class="panel-body">

                    @if($vehicle->latitude !== null && $vehicle->longitude !== null)

                        <div class="row">

                            <div class="col-md-6 col-sm-6 summary-item">
                                <div class="summary-label">Latitude</div>
                                <div>{{ $vehicle->latitude }}</div>
                            </div>

                            <div class="col-md-6 col-sm-6 summary-item">
                                <div class="summary-label">Longitude</div>
                                <div>{{ $vehicle->longitude }}</div>
                            </div>

                        </div>

                    @else

                        <div class="text-muted">
                            No location recorded yet.
                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>


    {{-- NOTES --}}
    @if($vehicle->notes)

        <div class="panel panel-default">

            <div class="panel-heading">
                <strong>
                    <i class="fa fa-file-text-o"></i>
                    Notes
                </strong>
            </div>

            <div class="panel-body">
                {{ $vehicle->notes }}
            </div>

        </div>

    @endif

</div>
@endsection
