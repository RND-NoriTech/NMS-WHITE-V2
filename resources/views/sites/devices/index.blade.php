@extends('layouts.librenmsv1')

@section('title', 'Devices - ' . $site->name)

@section('content')
<div class="container-fluid">

    <div class="panel panel-default">

        <div class="panel-heading">
            <strong>
                <i class="fa fa-server"></i>
                Devices — {{ $site->name }}
            </strong>


            <div class="pull-right">

                                        <a href="{{ route('sites.devices.discover', $site) }}"
                class="btn btn-success btn-xs"
                style="color:#fff !important;">
                    <i class="fa fa-search"></i> DISCOVER DEVICE
                </a>


                <a href="{{ route('sites.devices.create', $site) }}"
                   class="btn btn-primary btn-xs"
                   style="color:#fff !important;">
                    <i class="fa fa-plus"></i> ASSIGN DEVICE
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
                        <th>Device Name</th>
                        <th>Management IP</th>
                        <th>OS</th>
                        <th>Model</th>
                        <th>Version</th>
                        <th>Status</th>
                        <th>Team</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($assignedDevices as $assigned)

                   <tr>
    <td>
        <strong>
            {{ $assigned->device?->sysName ?: $assigned->device?->hostname ?: '-' }}
        </strong>
    </td>

    <td>
        {{ $assigned->device?->hostname ?: '-' }}
    </td>

    <td>
        {{ $assigned->device?->os ?: '-' }}
    </td>

    <td>
        {{ $assigned->device?->hardware ?: '-' }}
    </td>

    <td>
        {{ $assigned->device?->version ?: '-' }}
    </td>

    <td>
        @if($assigned->device?->status)
            <span class="label label-success">Up</span>
        @else
            <span class="label label-danger">Down</span>
        @endif
    </td>

    <td>
        {{ $assigned->team?->name ?: '-' }}
    </td>

    <td style="white-space:nowrap;">

        @if($assigned->device)
            <a href="{{ url('/device/device=' . $assigned->device->device_id) }}"
               class="btn btn-info btn-xs"
               title="Open Device">
                <i class="fa fa-eye"></i>
            </a>
        @endif

        <form method="POST"
              action="{{ route('sites.devices.destroy', [$site, $assigned]) }}"
              style="display:inline-block; margin:0;">
            @csrf
            @method('DELETE')

            <button type="submit"
                    class="btn btn-danger btn-xs"
                    title="Remove Device"
                    onclick="return confirm('Remove this device from site?')">
                <i class="fa fa-trash"></i>
            </button>
        </form>

    </td>
</tr>

                @empty

                    <tr>
                        <td colspan="7" class="text-center">
                            No devices assigned to this site.
                        </td>
                    </tr>

                @endforelse

                </tbody>
            </table>

        </div>
    </div>

</div>
@endsection
