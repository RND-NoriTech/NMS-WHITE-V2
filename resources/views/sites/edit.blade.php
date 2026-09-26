@extends('layouts.librenmsv1')

@section('title', 'Edit Site')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">

            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong>Edit Site</strong>
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

                    <form method="POST" action="{{ route('sites.update', $site) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Site Name</label>
                                    <input type="text"
                                           name="name"
                                           class="form-control"
                                           value="{{ old('name', $site->name) }}"
                                           required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Site Code</label>
                                    <input type="text"
                                           name="code"
                                           class="form-control"
                                           value="{{ old('code', $site->code) }}"
                                           required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Address</label>
                            <textarea name="address"
                                      class="form-control"
                                      rows="3">{{ old('address', $site->address) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Latitude</label>
                                    <input type="number"
                                           step="0.0000001"
                                           name="latitude"
                                           class="form-control"
                                           value="{{ old('latitude', $site->latitude) }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Longitude</label>
                                    <input type="number"
                                           step="0.0000001"
                                           name="longitude"
                                           class="form-control"
                                           value="{{ old('longitude', $site->longitude) }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Contact Person</label>
                                    <input type="text"
                                           name="contact_person"
                                           class="form-control"
                                           value="{{ old('contact_person', $site->contact_person) }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Contact Number</label>
                                    <input type="text"
                                           name="contact_number"
                                           class="form-control"
                                           value="{{ old('contact_number', $site->contact_number) }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="active"
                                    {{ old('status', $site->status) === 'active' ? 'selected' : '' }}>
                                    Active
                                </option>

                                <option value="inactive"
                                    {{ old('status', $site->status) === 'inactive' ? 'selected' : '' }}>
                                    Inactive
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes"
                                      class="form-control"
                                      rows="4">{{ old('notes', $site->notes) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Save Changes
                        </button>

                        <a href="{{ route('sites.index') }}"
                           class="btn btn-default">
                            Cancel
                        </a>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
