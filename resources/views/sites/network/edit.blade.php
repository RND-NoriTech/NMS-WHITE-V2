@extends('layouts.librenmsv1')

@section('title', 'Network - ' . $site->name)

@section('content')
<div class="container-fluid">

    <div class="panel panel-default">

        <div class="panel-heading">
            <strong>
                <i class="fa fa-sitemap"></i>
                Site Network — {{ $site->name }}
            </strong>

            <div class="pull-right">
                <a href="{{ route('sites.show', $site) }}"
                   class="btn btn-default btn-xs">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
            </div>

            <div class="clearfix"></div>
        </div>

        <div class="panel-body">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin-bottom:0;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST"
                  action="{{ route('sites.network.update', $site) }}">

                @csrf
                @method('PUT')

                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Management Subnet</label>

                            <input type="text"
                                   name="management_subnet"
                                   class="form-control"
                                   value="{{ old('management_subnet', $network?->management_subnet) }}"
                                   placeholder="Example: 10.50.1.0/24">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Gateway</label>

                            <input type="text"
                                   name="gateway"
                                   class="form-control"
                                   value="{{ old('gateway', $network?->gateway) }}"
                                   placeholder="Example: 10.50.1.1">
                        </div>
                    </div>

                </div>


                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>DNS Domain</label>

                            <input type="text"
                                   name="dns_domain"
                                   class="form-control"
                                   value="{{ old('dns_domain', $network?->dns_domain) }}"
                                   placeholder="Example: kl.nadi.internal">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Monitoring Method</label>

                            <select name="monitoring_method"
                                    class="form-control">

                                <option value="direct"
                                    {{ old('monitoring_method', $network?->monitoring_method ?? 'direct') === 'direct' ? 'selected' : '' }}>
                                    Direct
                                </option>

                                <option value="vpn"
                                    {{ old('monitoring_method', $network?->monitoring_method) === 'vpn' ? 'selected' : '' }}>
                                    VPN
                                </option>

                                <option value="dns"
                                    {{ old('monitoring_method', $network?->monitoring_method) === 'dns' ? 'selected' : '' }}>
                                    DNS
                                </option>

                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>VPN Status</label>

                            <select name="vpn_status"
                                    class="form-control">

                                <option value="unknown"
                                    {{ old('vpn_status', $network?->vpn_status ?? 'unknown') === 'unknown' ? 'selected' : '' }}>
                                    Unknown
                                </option>

                                <option value="connected"
                                    {{ old('vpn_status', $network?->vpn_status) === 'connected' ? 'selected' : '' }}>
                                    Connected
                                </option>

                                <option value="disconnected"
                                    {{ old('vpn_status', $network?->vpn_status) === 'disconnected' ? 'selected' : '' }}>
                                    Disconnected
                                </option>

                            </select>
                        </div>
                    </div>

                </div>


                <div class="form-group">
                    <label>Notes</label>

                    <textarea name="notes"
                              class="form-control"
                              rows="4"
                              placeholder="Optional notes">{{ old('notes', $network?->notes) }}</textarea>
                </div>


                <button type="submit"
                        class="btn btn-primary">
                    <i class="fa fa-save"></i>
                    Save Network Settings
                </button>

            </form>

        </div>
    </div>

</div>
@endsection
