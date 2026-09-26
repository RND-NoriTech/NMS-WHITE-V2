@extends('layouts.librenmsv1')

@section('title', 'Hardware Audit - ' . $site->name)

@section('content')

<div class="container-fluid">

    <div class="panel panel-default">

        <div class="panel-heading clearfix">

            <strong>
                <i class="fa fa-history"></i>
                Hardware Audit Log — {{ $site->name }}
            </strong>

            <div class="pull-right">

                <a href="{{ route('sites.hardware.index', $site) }}"
                   class="btn btn-default btn-sm">

                    <i class="fa fa-arrow-left"></i>
                    Back

                </a>

            </div>

        </div>


        <div class="panel-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover table-striped">

                    <thead>

                    <tr>
                        <th>ID</th>
                        <th>HARDWARE</th>
                        <th>DEVICE ID</th>
                        <th>USER</th>
                        <th>ACTION</th>
                        <th>IP ADDRESS</th>
                        <th>DATE / TIME</th>
                    </tr>

                    </thead>


                    <tbody>

                    @forelse($audits as $audit)

                        <tr>

                            <td>
                                {{ $audit->id }}
                            </td>

                            <td>
                                {{ $audit->hardware?->model ?: '-' }}
                            </td>

                            <td>
                                {{ $audit->hardware?->device_id ?: '-' }}
                            </td>

                            <td>
                                {{ $audit->username ?: '-' }}
                            </td>

                            <td>

                                @if($audit->action === 'reveal_password')

                                    <span class="label label-warning">
                                        Password Revealed
                                    </span>

                                @else

                                    <span class="label label-default">
                                        {{ $audit->action }}
                                    </span>

                                @endif

                            </td>

                            <td>
                                {{ $audit->ip_address ?: '-' }}
                            </td>

                            <td>
                                {{ $audit->created_at }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7"
                                class="text-center text-muted">

                                No hardware audit records found.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>


            <div class="text-center">
                {{ $audits->links() }}
            </div>

        </div>

    </div>

</div>

@endsection