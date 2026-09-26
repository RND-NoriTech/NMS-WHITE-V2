@extends('layouts.librenmsv1')

@section('title', $site->name)

@section('content')
<div class="container-fluid">

    {{-- PAGE STYLES --}}
    <style>
        .nms-site-summary .panel-body {
            padding: 18px 20px;
        }

        .nms-site-summary .summary-item {
            margin-bottom: 8px;
        }

        .nms-site-summary .summary-label {
            font-weight: 700;
            color: #444;
            margin-bottom: 3px;
        }

        .nms-module-card {
            min-height: 190px;
            border: 1px solid #dfe5ea;
            border-radius: 6px;
            transition: box-shadow .15s ease, transform .15s ease;
        }

        .nms-module-card:hover {
            box-shadow: 0 3px 12px rgba(0,0,0,.08);
            transform: translateY(-1px);
        }

        .nms-module-card .panel-body {
            min-height: 190px;
            padding: 18px;
            display: flex;
            flex-direction: column;
        }

        .nms-module-head {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .nms-module-icon {
            width: 56px;
            height: 56px;
            border-radius: 10px;
            background: #eef6ff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
            color: #1683d8;
            flex-shrink: 0;
        }

        .nms-module-title {
            margin: 0 0 5px 0;
            font-size: 19px;
            font-weight: 600;
        }

        .nms-module-desc {
            color: #555;
        }

        .nms-module-divider {
            margin: 18px 0 14px;
        }

        .nms-module-actions {
            margin-top: auto;
        }

        .nms-module-btn {
            color: #fff !important;
            font-weight: 600;
            min-width: 155px;
            text-align: center;
        }

        .nms-module-btn:hover,
        .nms-module-btn:focus,
        .nms-module-btn:active {
            color: #fff !important;
        }

        @media (max-width: 767px) {
            .nms-module-card {
                min-height: auto;
            }

            .nms-module-card .panel-body {
                min-height: auto;
            }

            .nms-module-btn {
                width: 100%;
            }
        }
    </style>


    {{-- SITE HEADER --}}
    <div class="panel panel-default nms-site-summary">

        <div class="panel-heading">

            <strong>
                <i class="fa fa-building"></i>
                {{ $site->name }}
            </strong>

            <div class="pull-right">

                <a href="{{ route('sites.edit', $site) }}"
                   class="btn btn-warning btn-xs"
                   title="Edit Site">
                    <i class="fa fa-pencil"></i> Edit
                </a>

                <a href="{{ route('sites.index') }}"
                   class="btn btn-default btn-xs"
                   title="Back to Sites">
                    <i class="fa fa-arrow-left"></i> Back
                </a>

            </div>

            <div class="clearfix"></div>

        </div>


        <div class="panel-body">

            <div class="row">

                <div class="col-md-3 col-sm-6 summary-item">
                    <div class="summary-label">Site Code</div>
                    <div>{{ $site->code }}</div>
                </div>

                <div class="col-md-3 col-sm-6 summary-item">
                    <div class="summary-label">Status</div>

                    <div>
                        @if($site->status === 'active')
                            <span class="label label-success">Active</span>
                        @else
                            <span class="label label-default">Inactive</span>
                        @endif
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 summary-item">
                    <div class="summary-label">Team Leader</div>
                    <div>{{ $defaultTeam?->team_leader ?: '-' }}</div>
                </div>

                <div class="col-md-3 col-sm-6 summary-item">
                    <div class="summary-label">Contact Number</div>
                    <div>{{ $defaultTeam?->contact_number ?: '-' }}</div>
                </div>

            </div>


            <hr>


            <div class="row">

                <div class="col-md-6 summary-item">
                    <div class="summary-label">Address</div>
                    <div>{{ $site->address ?: '-' }}</div>
                </div>

                <div class="col-md-3 col-sm-6 summary-item">
                    <div class="summary-label">Latitude</div>
                    <div>{{ $site->latitude ?: '-' }}</div>
                </div>

                <div class="col-md-3 col-sm-6 summary-item">
                    <div class="summary-label">Longitude</div>
                    <div>{{ $site->longitude ?: '-' }}</div>
                </div>

            </div>


            @if($site->notes)

                <hr>

                <div class="summary-item">
                    <div class="summary-label">Notes</div>
                    <div>{{ $site->notes }}</div>
                </div>

            @endif

        </div>

    </div>


    {{-- SITE MODULES --}}
    <div class="row">

        {{-- TEAMS --}}
        <div class="col-md-4 col-sm-6">

            <div class="panel panel-default nms-module-card">

                <div class="panel-body">

                    <div class="nms-module-head">

                        <div class="nms-module-icon">
                            <i class="fa fa-users"></i>
                        </div>

                        <div>
                            <h4 class="nms-module-title">Teams</h4>
                            <div class="nms-module-desc">
                                Manage site teams
                            </div>
                        </div>

                    </div>

                    <hr class="nms-module-divider">

                    <div class="nms-module-actions">

                        @if($defaultTeam)

                            <a href="{{ route('sites.teams.show', [$site, $defaultTeam]) }}"
                               class="btn btn-primary btn-sm nms-module-btn">
                                <i class="fa fa-users"></i>
                                Manage Teams
                            </a>

                        @else

                            <a href="{{ route('sites.teams.create', $site) }}"
                               class="btn btn-primary btn-sm nms-module-btn">
                                <i class="fa fa-plus"></i>
                                Create Team
                            </a>

                        @endif

                    </div>

                </div>

            </div>

        </div>


        {{-- DEVICES --}}
        <div class="col-md-4 col-sm-6">

            <div class="panel panel-default nms-module-card">

                <div class="panel-body">

                    <div class="nms-module-head">

                        <div class="nms-module-icon">
                            <i class="fa fa-server"></i>
                        </div>

                        <div>
                            <h4 class="nms-module-title">Devices</h4>
                            <div class="nms-module-desc">
                                Assigned network devices
                            </div>
                        </div>

                    </div>

                    <hr class="nms-module-divider">

                    <div class="nms-module-actions">

                        <a href="{{ route('sites.devices.index', $site) }}"
                           class="btn btn-primary btn-sm nms-module-btn">
                            <i class="fa fa-server"></i>
                            Manage Devices
                        </a>

                    </div>

                </div>

            </div>

        </div>


        {{-- HARDWARE --}}
        <div class="col-md-4 col-sm-6">

            <div class="panel panel-default nms-module-card">

                <div class="panel-body">

                    <div class="nms-module-head">

                        <div class="nms-module-icon">
                            <i class="fa fa-cubes"></i>
                        </div>

                        <div>
                            <h4 class="nms-module-title">Hardware</h4>
                            <div class="nms-module-desc">
                                Site hardware inventory
                            </div>
                        </div>

                    </div>

                    <hr class="nms-module-divider">

                    <div class="nms-module-actions">

                        <a href="#"
                           class="btn btn-primary btn-sm nms-module-btn">
                            <i class="fa fa-cubes"></i>
                            Manage Hardware
                        </a>

                    </div>

                </div>

            </div>

        </div>


        {{-- LOCATION / NETWORK --}}
        <div class="col-md-4 col-sm-6">

            <div class="panel panel-default nms-module-card">

                <div class="panel-body">

                    <div class="nms-module-head">

                        <div class="nms-module-icon">
                            <i class="fa fa-sitemap"></i>
                        </div>

                        <div>
                            <h4 class="nms-module-title">Network</h4>
                            <div class="nms-module-desc">
                                Subnet, gateway, DNS and VPN
                            </div>
                        </div>

                    </div>

                    <hr class="nms-module-divider">

                    <div class="nms-module-actions">

                        <a href="{{ route('sites.network.edit', $site) }}"
                           class="btn btn-primary btn-sm nms-module-btn">
                            <i class="fa fa-sitemap"></i>
                            Network Settings
                        </a>

                    </div>

                </div>

            </div>

        </div>


        {{-- WIFI --}}
        <div class="col-md-4 col-sm-6">

            <div class="panel panel-default nms-module-card">

                <div class="panel-body">

                    <div class="nms-module-head">

                        <div class="nms-module-icon">
                            <i class="fa fa-wifi"></i>
                        </div>

                        <div>
                            <h4 class="nms-module-title">WiFi</h4>
                            <div class="nms-module-desc">
                                NADI WiFi connectivity
                            </div>
                        </div>

                    </div>

                    <hr class="nms-module-divider">

                    <div class="nms-module-actions">

                        <a href="#"
                           class="btn btn-primary btn-sm nms-module-btn">
                            <i class="fa fa-wifi"></i>
                            Manage WiFi
                        </a>

                    </div>

                </div>

            </div>

        </div>


        {{-- VEHICLES --}}
        <div class="col-md-4 col-sm-6">

            <div class="panel panel-default nms-module-card">

                <div class="panel-body">

                    <div class="nms-module-head">

                        <div class="nms-module-icon">
                            <i class="fa fa-bus"></i>
                        </div>

                        <div>
                            <h4 class="nms-module-title">Vehicles</h4>
                            <div class="nms-module-desc">
                                NADI vehicle tracking
                            </div>
                        </div>

                    </div>

                    <hr class="nms-module-divider">

                    <div class="nms-module-actions">

                        <a href="#"
                           class="btn btn-primary btn-sm nms-module-btn">
                            <i class="fa fa-bus"></i>
                            Manage Vehicles
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
@endsection