@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h1 class="m-0 text-dark">Edit Asset</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Asset Details</h3>
                </div>
                <form action="{{ route('assets.update', $asset) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label>Asset Name</label>
                            <input type="text" name="name" class="form-control" required value="{{ $asset->name }}">
                        </div>
                        <div class="form-group">
                            <label>Serial Number</label>
                            <input type="text" name="serial_number" class="form-control" value="{{ $asset->serial_number }}">
                        </div>
                        <div class="form-group">
                            <label>Department</label>
                            <select name="department_id" class="form-control">
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ $asset->department_id == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Value</label>
                                    <input type="number" step="0.01" name="value" class="form-control" value="{{ $asset->value }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Purchase Date</label>
                                    <input type="date" name="purchase_date" class="form-control" value="{{ $asset->purchase_date ? $asset->purchase_date->format('Y-m-d') : '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Condition</label>
                            <select name="condition" class="form-control">
                                <option value="new" {{ $asset->condition == 'new' ? 'selected' : '' }}>New</option>
                                <option value="good" {{ $asset->condition == 'good' ? 'selected' : '' }}>Good</option>
                                <option value="fair" {{ $asset->condition == 'fair' ? 'selected' : '' }}>Fair</option>
                                <option value="poor" {{ $asset->condition == 'poor' ? 'selected' : '' }}>Poor</option>
                                <option value="broken" {{ $asset->condition == 'broken' ? 'selected' : '' }}>Broken</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ $asset->description }}</textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update Asset</button>
                        <a href="{{ route('assets.index') }}" class="btn btn-default float-right">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
