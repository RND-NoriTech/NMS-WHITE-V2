@extends('layouts.librenmsv1')

@section('title', 'Add WiFi - ' . $site->name)

@section('content')

<div class="container-fluid">

    <div class="panel panel-default">

        <div class="panel-heading">
            <strong>
                <i class="fa fa-plus"></i>
                Add WiFi — {{ $site->name }}
            </strong>
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

            <form method="POST"
                  action="{{ route('sites.wifi.store', $site) }}">

                @csrf

                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Name *</label>

                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   value="{{ old('name') }}"
                                   placeholder="Example: NADI-LINK-01"
                                   required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Provider</label>

                            <select name="provider"
                                    class="form-control">

                                <option value="">
                                    -- No Provider --
                                </option>

                                <option value="mikrotik"
                                    {{ old('provider') === 'mikrotik' ? 'selected' : '' }}>
                                    MikroTik
                                </option>

                                <option value="ruckus"
                                    {{ old('provider') === 'ruckus' ? 'selected' : '' }}>
                                    Ruckus
                                </option>

                                <option value="omada"
                                    {{ old('provider') === 'omada' ? 'selected' : '' }}>
                                    Omada
                                </option>

                                <option value="other"
                                    {{ old('provider') === 'other' ? 'selected' : '' }}>
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

                            <input type="text"
                                   name="ssid"
                                   class="form-control"
                                   value="{{ old('ssid') }}"
                                   placeholder="Example: NADI-WiFi">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Device Name</label>

                            <input type="text"
                                   name="device_name"
                                   class="form-control"
                                   value="{{ old('device_name') }}"
                                   placeholder="Example: AP-TOWER-01">
                        </div>
                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Model</label>

                            <input type="text"
                                   name="model"
                                   class="form-control"
                                   value="{{ old('model') }}"
                                   placeholder="Example: UniFi U6-Pro">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Device ID</label>

                            <input type="text"
                                   name="device_id"
                                   class="form-control"
                                   value="{{ old('device_id') }}"
                                   placeholder="Example: AP-NOW-001">
                        </div>
                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>MAC Address</label>

                            <input type="text"
                                   name="mac_address"
                                   class="form-control"
                                   value="{{ old('mac_address') }}"
                                   placeholder="AA:BB:CC:DD:EE:FF">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>IP Address</label>

                            <input type="text"
                                   name="ip_address"
                                   class="form-control"
                                   value="{{ old('ip_address') }}"
                                   placeholder="Example: 192.168.88.10">
                        </div>
                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Gateway</label>

                            <input type="text"
                                   name="gateway"
                                   class="form-control"
                                   value="{{ old('gateway') }}"
                                   placeholder="Example: 192.168.88.1">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Username</label>

                            <input type="text"
                                   name="username"
                                   class="form-control"
                                   value="{{ old('username') }}">
                        </div>
                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Password</label>

                            <input type="password"
                                   name="password"
                                   class="form-control"
                                   autocomplete="new-password">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Team</label>

                            <select name="team_id"
                                    class="form-control">

                                <option value="">
                                    -- No Team --
                                </option>

                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}"
                                        {{ old('team_id') == $team->id ? 'selected' : '' }}>
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

                            <select name="monitoring_method"
                                    id="monitoring_method"
                                    class="form-control">

                                <option value="snmp">
                                    SNMP / LibreNMS
                                </option>

                                <option value="api">
                                    API
                                </option>

                                <option value="manual">
                                    Manual
                                </option>

                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status *</label>

                            <select name="status"
                                    class="form-control">

                                <option value="active">
                                    Active
                                </option>

                                <option value="inactive">
                                    Inactive
                                </option>

                                <option value="maintenance">
                                    Maintenance
                                </option>

                                <option value="offline">
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

                            <select name="librenms_device_id"
                                    id="librenms_device_id"
                                    class="form-control">

                                <option value="">
                                    -- Select LibreNMS Device --
                                </option>

                                @foreach($managedDevices as $device)
                                    <option value="{{ $device->device_id }}"
                                        {{ old('librenms_device_id') == $device->device_id ? 'selected' : '' }}>
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

                            <select name="librenms_port_id"
                                    id="librenms_port_id"
                                    class="form-control">

                                <option value="">
                                    -- Select Wireless Interface --
                                </option>

                                @foreach($devicePorts as $port)
                                    <option value="{{ $port->port_id }}"
                                        {{ old('librenms_port_id') == $port->port_id ? 'selected' : '' }}>
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

                            <select name="api_provider"
                                    id="api_provider"
                                    class="form-control">

                                <option value="">
                                    -- Select Provider --
                                </option>

                                <option value="omada">
                                    Omada
                                </option>

                                <option value="ruckus">
                                    Ruckus
                                </option>

                                <option value="other">
                                    Other
                                </option>

                            </select>

                        </div>

                        <div class="form-group">

                            <label>External Device ID</label>

                            <input type="text"
                                   name="external_device_id"
                                   id="external_device_id"
                                   class="form-control"
                                   value="{{ old('external_device_id') }}"
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

                    <textarea name="notes"
                              class="form-control"
                              rows="4">{{ old('notes') }}</textarea>
                </div>

                <button type="submit"
                        class="btn btn-primary">

                    <i class="fa fa-save"></i>
                    Save WiFi

                </button>

                <a href="{{ route('sites.wifi.index', $site) }}"
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
@endif

updateWifiMonitoringFields();

</script>

@endsection
