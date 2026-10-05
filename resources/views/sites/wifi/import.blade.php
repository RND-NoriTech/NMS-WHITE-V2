@extends('layouts.librenmsv1')

@section('title', 'Import WiFi Interfaces - ' . $site->name)

@section('content')

<div class="container-fluid">

    <div class="panel panel-default">

        <div class="panel-heading clearfix">

            <strong>
                <i class="fa fa-download"></i>
                Import WiFi Interfaces — {{ $site->name }}
            </strong>

            <div class="pull-right">

                <a href="{{ route('sites.wifi.index', $site) }}"
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

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin-bottom:0;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            @if($candidates->isEmpty())

                <div class="alert alert-info"
                     style="margin-bottom:0;">

                    No unlinked wireless-looking interfaces found on the managed devices
                    assigned to this site. Use
                    <a href="{{ route('sites.devices.discover', $site) }}">Add / Discover Device</a>
                    if a device is missing.

                </div>

            @else

                <p>
                    Select the managed wireless interfaces to import as WiFi records.
                    Each record will use SNMP / NMS-WHITE monitoring and derive its
                    status from NMS-WHITE. SSID can be filled in later.
                </p>


                <form method="POST"
                      action="{{ route('sites.wifi.import.store', $site) }}">

                    @csrf

                    @foreach($candidates as $candidate)

                        <div class="panel panel-default">

                            <div class="panel-heading">
                                <strong>
                                    <i class="fa fa-server"></i>
                                    {{ $candidate['device']->hostname }}
                                </strong>
                                <span class="text-muted">
                                    ({{ $candidate['device']->os }} / {{ $candidate['device']->hardware }})
                                </span>
                            </div>

                            <div class="panel-body">

                                <div class="table-responsive">

                                    <table class="table table-bordered table-hover table-striped"
                                           style="margin-bottom:0;">

                                        <thead>
                                        <tr>
                                            <th width="40"></th>
                                            <th>INTERFACE</th>
                                            <th>DESCRIPTION</th>
                                            <th>ALIAS</th>
                                            <th>OPER STATUS</th>
                                            <th>MAC</th>
                                            <th>WILL BE CREATED AS</th>
                                        </tr>
                                        </thead>

                                        <tbody>

                                        @foreach($candidate['ports'] as $port)

                                            <tr>

                                                <td>

                                                    <input type="checkbox"
                                                           name="ports[]"
                                                           value="{{ $candidate['device']->device_id }}:{{ $port->port_id }}">

                                                </td>


                                                <td>
                                                    <strong>
                                                        {{ $port->ifName ?: $port->ifDescr ?: $port->port_id }}
                                                    </strong>
                                                </td>


                                                <td>
                                                    {{ $port->ifDescr ?: '-' }}
                                                </td>


                                                <td>
                                                    {{ $port->ifAlias ?: '-' }}
                                                </td>


                                                <td>

                                                    @if($port->ifAdminStatus?->value === 'down')
                                                        <span class="label label-danger">Admin Down</span>
                                                    @elseif($port->ifOperStatus?->value === 'up')
                                                        <span class="label label-success">Up</span>
                                                    @elseif($port->ifOperStatus?->value === 'down')
                                                        <span class="label label-danger">Down</span>
                                                    @else
                                                        <span class="label label-default">{{ $port->ifOperStatus?->value ?: 'Unknown' }}</span>
                                                    @endif

                                                </td>


                                                <td>
                                                    {{ $port->ifPhysAddress ?: '-' }}
                                                </td>


                                                <td>
                                                    {{ $candidate['device']->hostname }} {{ $port->ifName }}
                                                </td>

                                            </tr>

                                        @endforeach

                                        </tbody>

                                    </table>

                                </div>

                            </div>

                        </div>

                    @endforeach


                    <button type="submit"
                            class="btn btn-primary">

                        <i class="fa fa-download"></i>
                        Import Selected Interfaces

                    </button>

                    <a href="{{ route('sites.wifi.index', $site) }}"
                       class="btn btn-default">

                        Cancel

                    </a>

                </form>

            @endif

        </div>

    </div>

</div>

@endsection
