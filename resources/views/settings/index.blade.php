@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">⚙️ System Settings</h1>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">General Configuration</h3>
                </div>
                <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Church Name</label>
                                    <input type="text" name="church_name" class="form-control" value="{{ $settings['church_name'] ?? 'My Church' }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Church Slogan / Motto</label>
                                    <input type="text" name="church_slogan" class="form-control" value="{{ $settings['church_slogan'] ?? '' }}">
                                </div>
                                <div class="form-group">
                                    <label>Address</label>
                                    <textarea name="church_address" class="form-control" rows="3">{{ $settings['church_address'] ?? '' }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input type="text" name="church_phone" class="form-control" value="{{ $settings['church_phone'] ?? '' }}">
                                </div>
                                <div class="form-group">
                                    <label>Email Address</label>
                                    <input type="email" name="church_email" class="form-control" value="{{ $settings['church_email'] ?? '' }}">
                                </div>
                                <div class="form-group">
                                    <label>Currency Symbol</label>
                                    <input type="text" name="currency_symbol" class="form-control" value="{{ $settings['currency_symbol'] ?? 'TZS' }}">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Current Logo</label><br>
                                    @if(isset($settings['church_logo']))
                                        <img src="{{ asset('storage/' . $settings['church_logo']) }}" alt="Church Logo" style="max-height: 100px; border: 1px solid #ddd; padding: 5px; border-radius: 5px;">
                                    @else
                                        <p class="text-muted">No logo uploaded</p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Upload New Logo</label>
                                    <div class="custom-file">
                                        <input type="file" name="church_logo" class="custom-file-input" id="customFile">
                                        <label class="custom-file-label" for="customFile">Choose file</label>
                                    </div>
                                    <small class="form-text text-muted">Recommended size: 200x200px (PNG/JPG)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
