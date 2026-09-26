@extends('layouts.librenmsv1')

@section('title', 'Hardware - ' . $site->name)

@section('content')

<div class="container-fluid">

    <div class="panel panel-default">

        <div class="panel-heading clearfix">

            <strong>
                <i class="fa fa-cubes"></i>
                Hardware — {{ $site->name }}
            </strong>

            <div class="pull-right">

                            @admin
                    <a href="{{ route('sites.hardware.audit', $site) }}"
                    class="btn btn-info btn-sm">

                        <i class="fa fa-history"></i>
                        Audit Log

                    </a>
                @endadmin

                
                <a href="{{ route('sites.hardware.create', $site) }}"
                   class="btn btn-primary btn-sm">

                    <i class="fa fa-plus"></i>
                    Add Hardware

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
                        <th>MODEL</th>
                        <th>DEVICE ID</th>
                        <th>GATEWAY</th>
                        <th>MAC ADDRESS</th>
                        <th>USERNAME</th>
                        <th>PASSWORD</th>
                        <th>TEAM</th>
                        <th>STATUS</th>
                        <th width="120">ACTION</th>
                    </tr>
                    </thead>


                    <tbody>

                    @forelse($hardware as $item)

                        <tr>

                            <td>
                                <strong>
                                    {{ $item->model }}
                                </strong>
                            </td>


                            <td>
                                {{ $item->device_id }}
                            </td>


                            <td>
                                {{ $item->gateway ?: '-' }}
                            </td>


                            <td>
                                {{ $item->mac_address ?: '-' }}
                            </td>


                            <td>
                                {{ $item->username ?: '-' }}
                            </td>


                            <td>

                                @if($item->password)

                                    <span id="password-{{ $item->id }}">
                                        ••••••••
                                    </span>

                                    @admin

                                        <button
                                            type="button"
                                            class="btn btn-default btn-xs"
                                            style="margin-left:5px;"
                                            onclick="revealHardwarePassword(
                                                {{ $item->id }},
                                                '{{ route('sites.hardware.password', [$site, $item]) }}'
                                            )">

                                            <i class="fa fa-eye"></i>

                                        </button>

                                    @endadmin

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

                                @else

                                    <span class="label label-default">
                                        Inactive
                                    </span>

                                @endif

                            </td>


                            <td>

                                <a
                                    href="{{ route('sites.hardware.edit', [$site, $item]) }}"
                                    class="btn btn-warning btn-xs">

                                    <i class="fa fa-pencil"></i>

                                </a>


                                <form
                                    method="POST"
                                    action="{{ route('sites.hardware.destroy', [$site, $item]) }}"
                                    style="display:inline-block"
                                    onsubmit="return confirm('Delete this hardware?');">

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
                                colspan="9"
                                class="text-center text-muted">

                                No hardware added yet.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>



{{-- REVEAL PASSWORD MODAL --}}

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

                    This will reveal the stored hardware password.

                </div>

                <p>
                    Only continue if you are authorized
                    to view this credential.
                </p>

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

let hardwarePasswordId = null;
let hardwarePasswordUrl = null;


/*
|--------------------------------------------------------------------------
| OPEN REVEAL MODAL
|--------------------------------------------------------------------------
*/

function revealHardwarePassword(id, url)
{
    hardwarePasswordId = id;
    hardwarePasswordUrl = url;

    $('#revealPasswordModal').modal('show');
}


/*
|--------------------------------------------------------------------------
| CONFIRM REVEAL
|--------------------------------------------------------------------------
*/

$('#confirmRevealPassword').on('click', function () {

    if (!hardwarePasswordId || !hardwarePasswordUrl) {
        return;
    }

    const button = $(this);

    button.prop('disabled', true);

    button.html(
        '<i class="fa fa-spinner fa-spin"></i> Loading'
    );


    $.ajax({

        url: hardwarePasswordUrl,

        type: 'GET',

        dataType: 'json',


        success: function (response) {

            $('#revealPasswordModal').modal('hide');

            const target =
                $('#password-' + hardwarePasswordId);


            target.html(
                '<span class="hardware-password-value"></span>' +
                ' ' +
                '<button type="button" ' +
                'class="btn btn-default btn-xs" ' +
                'title="Copy Password" ' +
                'onclick="copyHardwarePassword(' +
                hardwarePasswordId +
                ')">' +
                '<i class="fa fa-copy"></i>' +
                '</button>'
            );


            target
                .find('.hardware-password-value')
                .text(response.password);


            /*
            |--------------------------------------------------------------------------
            | AUTO HIDE PASSWORD AFTER 15 SECONDS
            |--------------------------------------------------------------------------
            */

            setTimeout(function () {

                target.text('••••••••');

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

function copyHardwarePassword(id)
{
    const value =
        $('#password-' + id)
            .find('.hardware-password-value')
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