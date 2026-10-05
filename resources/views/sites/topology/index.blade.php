@extends('layouts.librenmsv1')

@section('title', $site->name . ' - Device Map')

@section('content')

<div class="container-fluid">

    <style>
        .nms-topology-map {
            height: 620px;
            border: 1px solid #dfe5ea;
            border-radius: 6px;
            background: #fbfcfd;
        }

        .nms-topology-legend .label {
            font-size: 12px;
            margin-right: 4px;
        }

        .nms-topology-details table {
            margin-bottom: 0;
        }

        .nms-topology-details .detail-label {
            font-weight: 700;
            color: #444;
            width: 40%;
        }

        .nms-topology-details .detail-actions {
            margin-top: 12px;
        }

        #nms-topology-no-data {
            display: none;
        }
    </style>


    <div class="panel panel-default">

        <div class="panel-heading clearfix">

            <strong>
                <i class="fa fa-share-alt"></i>
                {{ $site->name }} - Device Map
            </strong>

            <span class="text-muted">
                LLDP / CDP network topology
            </span>

            <div class="pull-right">

                <button type="button"
                        id="nms-topology-refresh"
                        class="btn btn-default btn-sm">

                    <i class="fa fa-refresh"></i>
                    Refresh

                </button>

                <button type="button"
                        id="nms-topology-fit"
                        class="btn btn-default btn-sm">

                    <i class="fa fa-expand"></i>
                    Fit to Screen

                </button>

                <a href="{{ route('sites.show', $site) }}"
                   class="btn btn-default btn-sm">

                    <i class="fa fa-arrow-left"></i>
                    Back

                </a>

            </div>

        </div>


        <div class="panel-body">


            {{-- LEGEND --}}
            <div class="nms-topology-legend"
                 style="margin-bottom:10px;">

                <span class="label label-success">Up</span>
                <span class="label label-danger">Down</span>
                <span class="label label-primary">Site Device</span>
                <span class="label label-default">Discovered Neighbor</span>

                <span class="text-muted" style="margin-left:10px;">
                    Data source: NMS-WHITE LLDP / CDP discovery (no direct device polling)
                </span>

            </div>


            {{-- STATUS AREA FOR REFRESH / DISCOVERY MESSAGES --}}
            <div id="nms-topology-status"
                 style="margin-bottom:10px;"></div>


            {{-- NO LLDP/CDP DATA STATE --}}
            <div class="alert alert-info"
                 id="nms-topology-no-data">

                <strong>
                    No LLDP/CDP topology discovered for this Site.
                </strong>

                <br>
                Ensure LLDP or CDP is enabled on the network devices and run
                NMS-WHITE discovery.

            </div>


            <div class="row">

                {{-- MAP --}}
                <div class="col-md-8">

                    <div id="nms-topology-map"
                         class="nms-topology-map"></div>

                </div>


                {{-- DETAILS --}}
                <div class="col-md-4">

                    <div class="panel panel-default nms-topology-details"
                         id="nms-topology-details"
                         style="display:none;">

                        <div class="panel-heading">

                            <strong id="nms-topology-details-title">
                                Details
                            </strong>

                        </div>

                        <div class="panel-body">

                            <div id="nms-topology-details-content"></div>

                            <div class="detail-actions" id="nms-topology-details-actions"></div>

                        </div>

                    </div>

                    <div class="panel panel-default"
                         id="nms-topology-hint">

                        <div class="panel-body text-muted">

                            Click a node or a link to view details.

                            <ul style="margin-top:6px; margin-bottom:0;">
                                <li>Drag nodes / pan / zoom freely.</li>
                                <li>Blue = managed Site device.</li>
                                <li>Grey = LLDP/CDP Discovered Neighbor.</li>
                            </ul>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection

{{-- Library assets load in <head> exactly like the native device
     Neighbours map. The init script runs after content via the scripts stack. --}}
@push('scripts')

<script>

/*
|--------------------------------------------------------------------------
| NMS-WHITE DEVICE MAP
|--------------------------------------------------------------------------
|
| Interactive LLDP/CDP topology built from the sites.topology.data
| JSON endpoint. Node/edge colors reuse the existing Bootstrap palette
| and the NMS-WHITE blue token. LibreNMS stays the source of truth.
|
*/

const topologyColors = {
    up: '#5cb85c',
    down: '#d9534f',
    site: '#1683d8',
    neighbor: '#777777',
    edge: '#999999',
    edgeHover: '#1683d8',
};

let topologyNetwork = null;
let topologyNodesById = {};
let topologyEdgesById = {};


/*
|--------------------------------------------------------------------------
| OS DISPLAY
|--------------------------------------------------------------------------
*/

function topologyOsDisplay(item)
{
    if (!item.os) {
        return null;
    }

    let name = item.os;

    if (name === 'routeros') {
        name = 'RouterOS';
    } else if (name === 'opnsense') {
        name = 'OPNsense';
    } else {
        name = name.charAt(0).toUpperCase() + name.slice(1);
    }

    if (item.os_version) {
        name = name + ' ' + item.os_version;
    }

    return name;
}


/*
|--------------------------------------------------------------------------
| DATA LOADING
|--------------------------------------------------------------------------
*/

function topologyShowError(message)
{
    const container = document.getElementById('nms-topology-map');

    if (!container) {
        window.alert(message);
        return;
    }

    container.innerHTML = '<div class="alert alert-danger" style="margin:12px;">'
        + topologyEscape(message) + '</div>';
}


function topologyLoad()
{
    $('#nms-topology-details').hide();
    $('#nms-topology-hint').show();

    $.getJSON('{{ route('sites.topology.data', $site) }}')
        .done(function (data) {

            topologyNodesById = {};
            topologyEdgesById = {};

            (data.nodes || []).forEach(function (node) {
                topologyNodesById[node.id] = node;
            });

            (data.edges || []).forEach(function (edge) {
                topologyEdgesById[edge.id] = edge;
            });

            const hasLinks = ((data.summary || {}).links || 0) > 0;

            $('#nms-topology-no-data').toggle(!hasLinks);

            if (!hasLinks) {
                $('#nms-topology-map').empty();
                topologyNetwork = null;
                return;
            }

            topologyRender(data);
        })
        .fail(function (xhr, textStatus, errorThrown) {

            const message = 'Unable to load topology data'
                + (xhr && xhr.status ? ' (HTTP ' + xhr.status + ')' : '')
                + (errorThrown ? ': ' + errorThrown : '.');

            topologyShowError(message);
        });
}


/*
|--------------------------------------------------------------------------
| RENDER VIS-NETWORK TOPOLOGY
|--------------------------------------------------------------------------
*/

function topologyRender(data)
{
    const container = document.getElementById('nms-topology-map');

    if (!container) {
        return;
    }

    /*
     * Defensive: the visualization library must be present, otherwise the
     * canvas would stay silently blank.
     */

    if (
        typeof vis === 'undefined'
        || typeof vis.DataSet === 'undefined'
        || typeof vis.Network === 'undefined'
    ) {
        topologyShowError('Unable to load topology visualization library.');
        return;
    }

    /*
     * The container must have a usable, visible height before the network
     * is constructed or the canvas renders with zero height.
     */

    if (!container.offsetHeight) {
        container.style.height = '620px';
    }

    const nodes = new vis.DataSet(
        (data.nodes || []).map(function (node) {
            return topologyNodeStyle(node);
        })
    );

    const edges = new vis.DataSet(
        (data.edges || []).map(function (edge) {
            return {
                id: edge.id,
                from: edge.from,
                to: edge.to,
                label: edge.label,
                font: {
                    size: 11,
                    multi: 'md',
                    color: '#444444'
                },
                color: {
                    color: topologyColors.edge,
                    hover: topologyColors.edgeHover,
                    highlight: topologyColors.edgeHover
                },
                arrows: 'none'
            };
        })
    );

    if (topologyNetwork) {
        topologyNetwork.destroy();
    }

    topologyNetwork = new vis.Network(
        container,
        { nodes: nodes, edges: edges },
        {
            autoResize: true,
            physics: {
                solver: 'forceAtlas2Based',
                stabilization: true
            },
            interaction: {
                dragNodes: true,
                dragView: true,
                zoomSpeed: 0.7,
                navigationButtons: false
            },
            nodes: {
                shape: 'box',
                font: {
                    size: 13,
                    multi: 'md'
                }
            },
            edges: {
                selectionWidth: 2
            }
        }
    );

    topologyNetwork.once('afterDrawing', function () {
        topologyNetwork.fit({ animation: false });
    });


    /*
    |--------------------------------------------------------------------------
    | CLICK: NODE / LINK DETAILS
    |--------------------------------------------------------------------------
    */

    topologyNetwork.on('click', function (params) {

        if (params.edges.length) {
            topologyShowLink(topologyEdgesById[params.edges[0]]);
            return;
        }

        if (params.nodes.length) {
            topologyShowNode(topologyNodesById[params.nodes[0]]);
            return;
        }

        $('#nms-topology-details').hide();
        $('#nms-topology-hint').show();

    });
}


/*
|--------------------------------------------------------------------------
| NODE STYLE BY STATE / KIND
|--------------------------------------------------------------------------
|
| Blue = Site Device (NMS-WHITE token),
| Green = Up, Red = Down, Grey = Discovered Neighbor
|
*/

function topologyNodeStyle(node)
{
    let color = topologyColors.neighbor;
    let fontColor = '#ffffff';

    if (node.kind === 'site') {
        color = topologyColors.site;
    } else if (node.kind === 'managed') {
        if (node.state === 'Down') {
            color = topologyColors.down;
        } else {
            color = topologyColors.up;
        }
    }


    let lines = [];

    if (node.kind === 'neighbor') {
        lines.push(node.label);
        lines.push('Discovered Neighbor');
    } else {
        lines.push(node.label);
        const os = topologyOsDisplay(node);
        if (os) {
            lines.push(os);
        }

        if (node.kind === 'site') {
            lines.push('Site Device - ' + node.state);
        } else if (node.kind === 'managed') {
            lines.push('Managed - ' + node.state);
        }
    }


    return {
        id: node.id,
        label: lines.join('\n'),
        title: node.state,
        color: {
            background: color,
            border: color,
            highlight: {
                background: color,
                border: '#333333'
            }
        },
        font: {
            color: fontColor,
            multi: 'md'
        }
    };
}


/*
|--------------------------------------------------------------------------
| NODE DETAILS PANEL
|--------------------------------------------------------------------------
*/

function topologyEscape(value)
{
    return value == null
        ? ''
        : String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
}

function topologyDetailTable(rows)
{
    let html = '<table class="table table-bordered table-condensed">\n';

    rows.forEach(function (row) {
        if (typeof row[1] === 'undefined' || row[1] === null || row[1] === '') {
            return;
        }

        /*
         * LLDP/CDP metadata comes from external network devices and is
         * never trusted: values are HTML-escaped unless trusted markup.
         */

        const value = row[2] === true ? row[1] : topologyEscape(row[1]);

        html += '<tr>'
            + '<td class="detail-label">' + row[0] + '</td>'
            + '<td>' + value + '</td>'
            + '</tr>\n';
    });

    html += '</table>';

    return html;
}


function topologyShowNode(node)
{
    if (!node) {
        return;
    }

    let actions = '';
    let rows = [];

    if (node.kind === 'neighbor') {

        rows.push(['Type', '<strong>Discovered Neighbor</strong>']);
        rows.push(['Remote System Name', node.system_name]);

        if (node.platform) {
            rows.push(['Platform', node.platform]);
        }

        if (node.version) {
            rows.push(['Version', node.version]);
        }


        if ((node.links || []).length) {
            let list = '<ul style="padding-left:18px; margin-bottom:0;">';

            node.links.forEach(function (connection) {
                list += '<li>'
                    + connection.local_device
                    + ': <strong>' + connection.local_port + '</strong>'
                    + ' &harr; <strong>' + connection.remote_port + '</strong>'
                    + ' (' + connection.protocol + ')'
                    + '</li>';
            });

            list += '</ul>';

            rows.push(['Connections', list, true]);
        }

    } else {


        rows.push(['Device Name', node.hostname]);
        rows.push(['Management IP', node.mgmt_ip]);
        rows.push(['OS', topologyOsDisplay(node)]);
        rows.push(['Model', node.hardware]);
        rows.push(['Status', node.state]);

        if (node.site_name) {
            rows.push(['Site', node.site_name]);
        } else {
            rows.push([
                'Site',
                '<span class="label label-default">Managed - not assigned to this Site</span>',
                true
            ]);
        }

        rows.push(['Device ID', node.device_id]);

        actions = '<a href="' + node.device_url + '" class="btn btn-primary btn-xs">'
            + '<i class="fa fa-external-link"></i> Open Device</a> '
            + '<a href="' + node.ports_url + '" class="btn btn-default btn-xs">'
            + '<i class="fa fa-ethernet"></i> Open Ports</a>';
    }

    $('#nms-topology-details-content').html(topologyDetailTable(rows));
    $('#nms-topology-details-actions').html(actions);
    $('#nms-topology-details-title').text(node.kind === 'neighbor' ? 'Discovered Neighbor' : 'Device');
    $('#nms-topology-hint').hide();
    $('#nms-topology-details').show();
}


/*
|--------------------------------------------------------------------------
| LINK DETAILS PANEL
|--------------------------------------------------------------------------
*/

function topologyShowLink(edge)
{
    if (!edge) {
        return;
    }

    const details = edge.details || {};

    let rows = [
        ['Protocol', details.protocol],
        ['Local Device', details.local_device],
        ['Local Interface', details.local_interface],
    ];

    if (details.local_port_descr) {
        rows.push(['Local Port Description', details.local_port_descr]);
    }

    rows.push([
        'Remote Device / System',
        details.remote_resolved
            ? details.remote_device + ' (Managed)'
            : details.remote_device + ' (Discovered Neighbor)'
    ]);

    rows.push(['Remote Interface', details.remote_interface]);

    if (details.remote_port_descr) {
        rows.push(['Remote Port Description', details.remote_port_descr]);
    }

    if (details.local_port_status) {
        rows.push(['Port Status', details.local_port_status]);
    }

    if (details.remote_port_status) {
        rows.push(['Remote Port Status', details.remote_port_status]);
    }

    if (details.local_port_speed) {
        rows.push(['Port Speed', details.local_port_speed]);
    }

    if (details.local_port_rx) {
        rows.push(['RX', details.local_port_rx]);
    }

    if (details.local_port_tx) {
        rows.push(['TX', details.local_port_tx]);
    }



    $('#nms-topology-details-content').html(topologyDetailTable(rows));
    $('#nms-topology-details-actions').html('');
    $('#nms-topology-details-title').text('Link');
    $('#nms-topology-hint').hide();
    $('#nms-topology-details').show();
}


/*
|--------------------------------------------------------------------------
| STATUS AREA (VISIBLE REFRESH / DISCOVERY MESSAGES)
|--------------------------------------------------------------------------
*/

function topologyShowStatus(messages, level)
{
    const html = messages
        .map(function (message) {
            return topologyEscape(message);
        })
        .join('<br>');

    $('#nms-topology-status')
        .html('<div class="alert alert-' + (level || 'success') + '" style="margin-bottom:10px;">'
            + html + '</div>');
}


/*
|--------------------------------------------------------------------------
| INITIALIZE (AFTER DOM READY AND ASSETS LOADED)
|--------------------------------------------------------------------------
*/

$(function () {

    /*
     * DOM ready: the container and controls exist now. The library assets
     * load in <head>, so they are available before this runs.
     */

    const container = document.getElementById('nms-topology-map');

    if (!container) {
        return;
    }

    if (
        typeof vis === 'undefined'
        || typeof vis.DataSet === 'undefined'
        || typeof vis.Network === 'undefined'
    ) {
        topologyShowError('Unable to load topology visualization library.');
        return;
    }

    /*
     * Refresh: server-side discovery via sites.topology.refresh (the CSRF
     * header is attached to all requests by the layout's global jQuery
     * setup). After the POST completes, topology data is reloaded and the
     * map is redrawn; the button is always restored.
     */

    $('#nms-topology-refresh').on('click', function () {

        const button = $(this);
        const fitButton = $('#nms-topology-fit');
        const originalHtml = button.html();

        if (button.prop('disabled')) {
            return;
        }

        button.prop('disabled', true);
        button.html('<i class="fa fa-spinner fa-spin"></i> Discovering...');
        fitButton.prop('disabled', true);

        $.post('{{ route('sites.topology.refresh', $site) }}')
            .done(function (response) {

                const devices = response.devices || [];
                const statusMessages = [];

                if (!devices.length) {
                    statusMessages.push('No managed devices are assigned to this Site.');
                }

                devices.forEach(function (device) {
                    if (!device.success) {
                        statusMessages.push(
                            'Discovery failed for device ' + device.device_id
                            + (device.message ? ': ' + device.message : '')
                        );
                    }
                });

                if (devices.length && !statusMessages.length) {
                    statusMessages.push('Topology discovery completed');
                }

                if (statusMessages.length) {
                    topologyShowStatus(statusMessages, statusMessages.length === 1 ? 'success' : 'warning');
                }
            })
            .fail(function (xhr) {
                topologyShowStatus(
                    ['Refresh request failed' + (xhr.status ? ' (HTTP ' + xhr.status + ')' : '') + '.'],
                    'danger'
                );
            })
            .always(function () {
                button.prop('disabled', false).html(originalHtml);
                fitButton.prop('disabled', false);

                /*
                 * Reload topology data and redraw the map after discovery.
                 */

                topologyLoad();
            });
    });

    $('#nms-topology-fit').on('click', function () {
        if (topologyNetwork) {
            topologyNetwork.fit({ animation: false });
        }
    });

    topologyLoad();

});

</script>

@endpush

@section('javascript')
<script src="{{ url('js/vis-network.min.js') }}"></script>
<script src="{{ url('js/vis-data.min.js') }}"></script>
@endsection
