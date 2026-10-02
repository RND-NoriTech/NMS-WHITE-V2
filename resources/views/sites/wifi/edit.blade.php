@extends('layouts.librenmsv1')

@section('title', 'Edit WiFi - ' . $site->name)

@section('content')

<div class="container-fluid">

    <div class="panel panel-default">

        <div class="panel-heading clearfix">

            <strong>
                <i class="fa fa-pencil"></i>
                Edit WiFi — {{ $site->name }}
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

            @if($errors->any())

                <div class="alert alert-danger">

                    <ul style="margin-bottom:0;">

                        @foreach($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif


            <form
                method="POST"
                action="{{ route('sites.wifi.update', [$site, $wifi]) }}">

                @csrf
                @method('PUT')


                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Name *</label>

                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                value="{{ old('name', $wifi->name) }}"
                                required>

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Provider</label>

                            <select
                                name="provider"
                                class="form-control">

                                <option value="">
                                    -- No Provider --
                                </option>

                                <option
                                    value="mikrotik"
                                    {{ old('provider', $wifi->provider) === 'mikrotik' ? 'selected' : '' }}>
                                    MikroTik
                                </option>

                                <option
                                    value="ruckus"
                                    {{ old('provider', $wifi->provider) === 'ruckus' ? 'selected' : '' }}>
                                    Ruckus
                                </option>

                                <option
                                    value="omada"
                                    {{ old('provider', $wifi->provider) === 'omada' ? 'selected' : '' }}>
                                    Omada
                                </option>

                                <option
                                    value="other"
                                    {{ old('provider', $wifi->provider) === 'other' ? 'selected' : '' }}>
                                    Other
                                </option>

                            </select>

                            <small class="text-muted">
                                Used for optional metadata enrichment.
                                Monitoring always comes from LibreNMS / SNMP.
                            </small>

                        </div>

                    </div>

                </div>


                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group">

                            <label>SSID</label>

                            <input
                                type="text"
                                name="ssid"
                                class="form-control"
                                value="{{ old('ssid', $wifi->ssid) }}">

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Device Name</label>

                            <input
                                type="text"
                                name="device_name"
                                class="form-control"
                                value="{{ old('device_name', $wifi->device_name) }}">

                        </div>

                    </div>

                </div>


                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Model</label>

                            <input
                                type="text"
                                name="model"
                                class="form-control"
                                value="{{ old('model', $wifi->model) }}">

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Device ID</label>

                            <input
                                type="text"
                                name="device_id"
                                class="form-control"
                                value="{{ old('device_id', $wifi->device_id) }}">

                        </div>

                    </div>

                </div>


                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group">

                            <label>MAC Address</label>

                            <input
                                type="text"
                                name="mac_address"
                                class="form-control"
                                value="{{ old('mac_address', $wifi->mac_address) }}"
                                placeholder="AA:BB:CC:DD:EE:FF">

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="form-group">

                            <label>IP Address</label>

                            <input
                                type="text"
                                name="ip_address"
                                class="form-control"
                                value="{{ old('ip_address', $wifi->ip_address) }}"
                                placeholder="Example: 192.168.88.10">

                        </div>

                    </div>

                </div>


                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Gateway</label>

                            <input
                                type="text"
                                name="gateway"
                                class="form-control"
                                value="{{ old('gateway', $wifi->gateway) }}"
                                placeholder="Example: 192.168.88.1">

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Username</label>

                            <input
                                type="text"
                                name="username"
                                class="form-control"
                                value="{{ old('username', $wifi->username) }}">

                        </div>

                    </div>

                </div>


                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Password</label>

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                autocomplete="new-password"
                                placeholder="Leave blank to keep current password">

                            <small class="text-muted">
                                Leave blank if you do not want to change the current password.
                            </small>

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Team</label>

                            <select
                                name="team_id"
                                class="form-control">

                                <option value="">
                                    -- No Team --
                                </option>

                                @foreach($teams as $team)

                                    <option
                                        value="{{ $team->id }}"
                                        {{ old('team_id', $wifi->team_id) == $team->id ? 'selected' : '' }}>

                                        {{ $team->name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                    </div>

                </div>


                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Monitoring Method *</label>

                            <select
                                name="monitoring_method"
                                id="monitoring_method"
                                class="form-control">

                                <option
                                    value="snmp"
                                    {{ old('monitoring_method', $wifi->monitoring_method) === 'snmp' ? 'selected' : '' }}>
                                    SNMP / LibreNMS
                                </option>

                                <option
                                    value="api"
                                    {{ old('monitoring_method', $wifi->monitoring_method) === 'api' ? 'selected' : '' }}>
                                    API
                                </option>

                                <option
                                    value="manual"
                                    {{ old('monitoring_method', $wifi->monitoring_method) === 'manual' ? 'selected' : '' }}>
                                    Manual
                                </option>

                            </select>

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Status *</label>

                            <select
                                name="status"
                                class="form-control">

                                <option
                                    value="active"
                                    {{ old('status', $wifi->status) === 'active' ? 'selected' : '' }}>
                                    Active
                                </option>

                                <option
                                    value="inactive"
                                    {{ old('status', $wifi->status) === 'inactive' ? 'selected' : '' }}>
                                    Inactive
                                </option>

                                <option
                                    value="maintenance"
                                    {{ old('status', $wifi->status) === 'maintenance' ? 'selected' : '' }}>
                                    Maintenance
                                </option>

                                <option
                                    value="offline"
                                    {{ old('status', $wifi->status) === 'offline' ? 'selected' : '' }}>
                                    Offline
                                </option>

                            </select>

                        </div>

                    </div>

                </div>


                <div class="row">

                    {{-- SNMP / LIBRENMS MODE --}}
                    <div class="col-md-6"
                         id="snmp-fields">

                        <div class="form-group">

                            <label>Managed Device *</label>

                            <select
                                name="librenms_device_id"
                                id="librenms_device_id"
                                class="form-control">

                                <option value="">
                                    -- Select LibreNMS Device --
                                </option>

                                @foreach($managedDevices as $device)

                                    <option
                                        value="{{ $device->device_id }}"
                                        {{ old('librenms_device_id', $wifi->librenms_device_id) == $device->device_id ? 'selected' : '' }}>

                                        {{ $device->hostname }} - {{ $device->os }} - {{ $device->hardware }}

                                    </option>

                                @endforeach

                            </select>

                            <small class="text-muted">
                                Only LibreNMS devices already assigned to this site are shown.
                                Monitoring status is reused from LibreNMS.
                            </small>

                            <div style="margin-top:5px;">

                                <a href="{{ route('sites.devices.discover', $site) }}"
                                   class="btn btn-success btn-xs"
                                   style="color:#fff !important;">

                                    <i class="fa fa-search"></i>
                                    Add / Discover Device

                                </a>

                            </div>

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Wireless Interface *</label>

                            <select
                                name="librenms_port_id"
                                id="librenms_port_id"
                                class="form-control">

                                <option value="">
                                    -- Select Wireless Interface --
                                </option>

                                @foreach($devicePorts as $port)

                                    <option
                                        value="{{ $port->port_id }}"
                                        {{ old('librenms_port_id', $wifi->librenms_port_id) == $port->port_id ? 'selected' : '' }}>

                                        {{ $port->ifName }}@if($port->ifAlias) - {{ $port->ifAlias }}@endif

                                    </option>

                                @endforeach

                            </select>

                            <small class="text-muted">
                                Interfaces are loaded from LibreNMS when a Managed Device is selected.
                            </small>

                        </div>

                    </div>


                    {{-- API MODE --}}
                    <div class="col-md-6"
                         id="api-fields">

                        <div class="form-group">

                            <label>API Provider *</label>

                            <select
                                name="api_provider"
                                id="api_provider"
                                class="form-control">

                                <option value="">
                                    -- Select Provider --
                                </option>

                                <option
                                    value="omada"
                                    {{ old('api_provider', $wifi->api_provider) === 'omada' ? 'selected' : '' }}>
                                    Omada
                                </option>

                                <option
                                    value="ruckus"
                                    {{ old('api_provider', $wifi->api_provider) === 'ruckus' ? 'selected' : '' }}>
                                    Ruckus
                                </option>

                                <option
                                    value="other"
                                    {{ old('api_provider', $wifi->api_provider) === 'other' ? 'selected' : '' }}>
                                    Other
                                </option>

                            </select>

                        </div>


                        <div class="form-group">

                            <label>External Device ID</label>

                            <input
                                type="text"
                                name="external_device_id"
                                id="external_device_id"
                                class="form-control"
                                value="{{ old('external_device_id', $wifi->external_device_id) }}"
                                placeholder="Provider device identifier">

                        </div>

                    </div>


                    <div class="col-md-6"
                         id="manual-fields"
                         style="display:none;">

                        <div class="text-muted"
                             style="padding-top:25px;">

                            Manual mode uses the stored inventory fields only.

                        </div>

                    </div>

                </div>


                <div class="form-group">

                    <label>Notes</label>

                    <textarea
                        name="notes"
                        class="form-control"
                        rows="4">{{ old('notes', $wifi->notes) }}</textarea>

                </div>


                <button
                    type="submit"
                    class="btn btn-primary">

                    <i class="fa fa-save"></i>
                    Update WiFi

                </button>


                <a
                    href="{{ route('sites.wifi.index', $site) }}"
                    class="btn btn-default">

                    Cancel

                </a>

            </form>

        </div>

    </div>

</div>

<script>

/*
|--------------------------------------------------------------------------
| MONITORING MODE FIELD VISIBILITY
|--------------------------------------------------------------------------
*/

function updateWifiMonitoringFields()
{
    const mode = $('#monitoring_method').val();

    $('#snmp-fields').toggle(mode === 'snmp');
    $('#api-fields').toggle(mode === 'api');
    $('#manual-fields').toggle(mode === 'manual');
}

$('#monitoring_method').on('change', updateWifiMonitoringFields);


/*
|--------------------------------------------------------------------------
| LOAD WIRELESS INTERFACES FOR SELECTED MANAGED DEVICE
|--------------------------------------------------------------------------
*/

const wifiDevicePortsUrlBase =
    '{{ route('sites.wifi.device-ports', [$site, '__DEVICE__']) }}';

function loadWifiDevicePorts(deviceId, selectedPortId)
{
    const select = $('#librenms_port_id');

    select.empty();
    select.append(
        '<option value="">-- Select Wireless Interface --</option>'
    );

    if (!deviceId) {
        return;
    }


    $.ajax({

        url: wifiDevicePortsUrlBase.replace('__DEVICE__', deviceId),

        type: 'GET',

        dataType: 'json',


        success: function (response) {

            $.each(response.ports || [], function (index, port) {

                let label = port.ifName || port.ifDescr || ('Port ' + port.port_id);

                if (port.ifAlias) {
                    label = label + ' - ' + port.ifAlias;
                }

                select.append(
                    $('<option>', {
                        value: port.port_id,
                        text: label,
                        selected: selectedPortId == port.port_id
                    })
                );

            });

        },


        error: function () {

            alert('Unable to load interfaces for this device.');

        }

    });
}

$('#librenms_device_id').on('change', function () {
    loadWifiDevicePorts($(this).val(), null);
});


@if(old('librenms_device_id'))
    loadWifiDevicePorts('{{ old('librenms_device_id') }}', '{{ old('librenms_port_id') }}');
@elseif($wifi->librenms_device_id)
    loadWifiDevicePorts('{{ $wifi->librenms_device_id }}', '{{ $wifi->librenms_port_id }}');
@endif

updateWifiMonitoringFields();

</script>

@endsection
