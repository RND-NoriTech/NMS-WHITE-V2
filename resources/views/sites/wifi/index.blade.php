@extends('layouts.librenmsv1')

@section('title', 'WiFi - ' . $site->name)

@section('content')

<div class="container-fluid">

    @php
        $providerLabels = [
            'mikrotik' => 'MikroTik',
            'ruckus' => 'Ruckus',
            'omada' => 'Omada',
            'other' => 'Other',
        ];

        $syncStatusLabels = [
            'synced' => 'Synced',
            'not_configured' => 'API not configured',
            'unsupported' => 'No provider',
            'error' => 'Sync error',
        ];
    @endphp

    <div class="panel panel-default">

        <div class="panel-heading clearfix">

            <strong>
                <i class="fa fa-wifi"></i>
                WiFi — {{ $site->name }}
            </strong>

            <div class="pull-right">

                <a href="{{ route('sites.wifi.import', $site) }}"
                   class="btn btn-success btn-sm">

                    <i class="fa fa-download"></i>
                    Import WiFi Interfaces

                </a>

                <a href="{{ route('sites.wifi.create', $site) }}"
                   class="btn btn-primary btn-sm">

                    <i class="fa fa-plus"></i>
                    Add WiFi

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

            @if(session('warning'))
                <div class="alert alert-warning">
                    {{ session('warning') }}
                </div>
            @endif


            <div class="table-responsive">

                <table class="table table-bordered table-hover table-striped">

                    <thead>
                    <tr>
                        <th>NAME</th>
                        <th>PROVIDER</th>
                        <th>SSID</th>
                        <th>MODE</th>
                        <th>DEVICE</th>
                        <th>INTERFACE</th>
                        <th>IP</th>
                        <th>TRAFFIC</th>
                        <th>STATUS</th>
                        <th>LAST SYNC</th>
                        <th>TEAM</th>
                        <th width="180">ACTION</th>
                    </tr>
                    </thead>


                    <tbody>

                    @forelse($wifi as $item)

                        @php
                            $isSnmp = $item->monitoring_method === 'snmp';
                            $isApi = $item->monitoring_method === 'api';
                        @endphp

                        <tr>

                            <td>
                                <strong>
                                    {{ $item->name }}
                                </strong>
                            </td>


                            <td>

                                @if($item->provider && isset($providerLabels[$item->provider]))

                                    <span class="label label-default">
                                        {{ $providerLabels[$item->provider] }}
                                    </span>

                                @elseif($item->provider)

                                    {{ $item->provider }}

                                @else

                                    -

                                @endif

                            </td>


                            <td>
                                {{ $item->ssid ?: '-' }}
                            </td>


                            <td>
                                {{ $item->wireless_mode ?: '-' }}
                            </td>


                            <td>

                                @if($isSnmp)

                                    {{ $item->device?->hostname ?: '-' }}

                                @elseif($isApi)

                                    {{ $item->api_provider ? ucfirst($item->api_provider) : '-' }}

                                @else

                                    {{ $item->device_name ?: ($item->model ?: '-') }}

                                @endif

                            </td>


                            <td>

                                @if($isSnmp)

                                    {{ $item->port?->ifName ?: '-' }}

                                @else

                                    -

                                @endif

                            </td>


                            <td>

                                @if($isSnmp)

                                    {{ $item->device?->hostname ?: '-' }}

                                @else

                                    {{ $item->ip_address ?: '-' }}

                                @endif

                            </td>


                            <td>

                                @if($isSnmp && $item->port)

                                    @if($item->port->ifInOctets_rate !== null)
                                        <i class="fa fa-arrow-down text-muted"></i>
                                        {{ \LibreNMS\Util\Number::formatBi($item->port->ifInOctets_rate, 2, 0, 'ps') }}
                                    @endif

                                    @if($item->port->ifOutOctets_rate !== null)
                                        <i class="fa fa-arrow-up text-muted"></i>
                                        {{ \LibreNMS\Util\Number::formatBi($item->port->ifOutOctets_rate, 2, 0, 'ps') }}
                                    @endif

                                    @if($item->port->ifInOctets_rate === null && $item->port->ifOutOctets_rate === null)
                                        -
                                    @endif

                                @else

                                    -

                                @endif

                            </td>


                            <td>

                                @if($isSnmp)

                                    @if(! $item->device)

                                        <span class="label label-default">
                                            Unknown
                                        </span>

                                    @elseif($item->device->disabled)

                                        <span class="label label-danger">
                                            Disabled
                                        </span>

                                    @elseif(! $item->device->status)

                                        <span class="label label-danger">
                                            Down
                                        </span>

                                    @elseif(! $item->port)

                                        <span class="label label-default">
                                            Unknown
                                        </span>

                                    @elseif($item->port->ifAdminStatus?->value === 'down')

                                        <span class="label label-danger">
                                            Disabled
                                        </span>

                                    @elseif($item->port->ifOperStatus?->value === 'up')

                                        <span class="label label-success">
                                            Up
                                        </span>

                                    @else

                                        <span class="label label-danger">
                                            Down
                                        </span>

                                    @endif

                                @else

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

                                @endif

                            </td>


                            <td>

                                @if($item->metadata_synced_at)

                                    {{ $item->metadata_synced_at->format('Y-m-d H:i') }}

                                    @if($item->metadata_sync_status && $item->metadata_sync_status !== 'synced')

                                        <br>
                                        <small
                                            class="text-muted"
                                            title="{{ $item->metadata_sync_message }}">
                                            {{ $syncStatusLabels[$item->metadata_sync_status] ?? $item->metadata_sync_status }}
                                        </small>

                                    @endif

                                @else

                                    -

                                @endif

                            </td>


                            <td>
                                {{ $item->team?->name ?: '-' }}
                            </td>


                            <td>

                                <form
                                    method="POST"
                                    action="{{ route('sites.wifi.sync', [$site, $item]) }}"
                                    style="display:inline-block">

                                    @csrf

                                    <button
                                        type="submit"
                                        class="btn btn-info btn-xs"
                                        title="Sync WiFi Metadata">

                                        <i class="fa fa-refresh"></i>

                                    </button>

                                </form>


                                @if($item->password)

                                    @admin

                                        <button
                                            type="button"
                                            class="btn btn-default btn-xs"
                                            title="Reveal Password"
                                            onclick="revealWifiPassword(
                                                {{ $item->id }},
                                                '{{ route('sites.wifi.password', [$site, $item]) }}'
                                            )">

                                            <i class="fa fa-eye"></i>

                                        </button>

                                    @endadmin

                                @endif


                                <a
                                    href="{{ route('sites.wifi.edit', [$site, $item]) }}"
                                    class="btn btn-warning btn-xs">

                                    <i class="fa fa-pencil"></i>

                                </a>


                                <form
                                    method="POST"
                                    action="{{ route('sites.wifi.destroy', [$site, $item]) }}"
                                    style="display:inline-block"
                                    onsubmit="return confirm('Delete this WiFi connection?');">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-danger btn-xs">

                                        <i class="fa fa-trash"></i>

                                    </button>

                                </form>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="12"
                                class="text-center text-muted">

                                No WiFi connections added yet.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>



{{-- REVEAL PASSWORD MODAL (NON-LIBRENMS CREDENTIALS ONLY) --}}

<div
    class="modal fade"
    id="revealPasswordModal"
    tabindex="-1"
    role="dialog">

    <div
        class="modal-dialog modal-sm"
        role="document">

        <div class="modal-content">

            <div class="modal-header">

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal">

                    &times;

                </button>

                <h4 class="modal-title">

                    <i class="fa fa-lock"></i>
                    Reveal Password

                </h4>

            </div>


            <div class="modal-body">

                <div
                    class="alert alert-warning"
                    style="margin-bottom:10px;">

                    This will reveal the stored WiFi password.

                </div>

                <p>
                    Only continue if you are authorized
                    to view this credential.
                </p>


                <div
                    id="revealPasswordResult"
                    style="display:none;">

                    <p>
                        <strong
                            class="wifi-password-value"
                            style="word-break:break-all;"></strong>
                    </p>

                    <button
                        type="button"
                        class="btn btn-default btn-xs"
                        title="Copy Password"
                        onclick="copyWifiPassword()">

                        <i class="fa fa-copy"></i>
                        Copy

                    </button>

                </div>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-default"
                    data-dismiss="modal">

                    Cancel

                </button>


                <button
                    type="button"
                    class="btn btn-danger"
                    id="confirmRevealPassword">

                    <i class="fa fa-eye"></i>
                    Reveal

                </button>

            </div>

        </div>

    </div>

</div>



<script>

let wifiPasswordId = null;
let wifiPasswordUrl = null;


/*
|--------------------------------------------------------------------------
| OPEN REVEAL MODAL
|--------------------------------------------------------------------------
*/

function revealWifiPassword(id, url)
{
    wifiPasswordId = id;
    wifiPasswordUrl = url;

    $('#revealPasswordModal').modal('show');
}


/*
|--------------------------------------------------------------------------
| CONFIRM REVEAL
|--------------------------------------------------------------------------
*/

$('#confirmRevealPassword').on('click', function () {

    if (!wifiPasswordId || !wifiPasswordUrl) {
        return;
    }

    const button = $(this);

    button.prop('disabled', true);

    button.html(
        '<i class="fa fa-spinner fa-spin"></i> Loading'
    );


    $.ajax({

        url: wifiPasswordUrl,

        type: 'GET',

        dataType: 'json',


        success: function (response) {

            $('#revealPasswordResult')
                .show();

            $('#revealPasswordResult')
                .find('.wifi-password-value')
                .text(response.password);


            /*
            |--------------------------------------------------------------------------
            | AUTO HIDE PASSWORD AFTER 15 SECONDS
            |--------------------------------------------------------------------------
            */

            setTimeout(function () {

                $('#revealPasswordResult').hide();

                $('#revealPasswordResult')
                    .find('.wifi-password-value')
                    .text('');

                $('#revealPasswordModal').modal('hide');

            }, 15000);

        },


        error: function (xhr) {

            let message =
                'Unable to reveal password.';


            if (
                xhr.responseJSON &&
                xhr.responseJSON.message
            ) {

                message =
                    xhr.responseJSON.message;

            }


            alert(message);

        },


        complete: function () {

            button.prop('disabled', false);

            button.html(
                '<i class="fa fa-eye"></i> Reveal'
            );

        }

    });

});


/*
|--------------------------------------------------------------------------
| COPY PASSWORD
|--------------------------------------------------------------------------
*/

function copyWifiPassword()
{
    const value =
        $('#revealPasswordResult')
            .find('.wifi-password-value')
            .text();


    if (!value) {
        return;
    }


    navigator.clipboard
        .writeText(value)

        .then(function () {

            alert(
                'Password copied to clipboard.'
            );

        })

        .catch(function () {

            alert(
                'Unable to copy password.'
            );

        });
}

</script>

@endsection
