@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h1 class="m-0 text-dark">Add New Asset</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Asset Details</h3>
                </div>
                <form action="{{ route('assets.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label>Asset Name</label>
                            <input type="text" name="name" class="form-control" required placeholder="e.g. Yamaha Keyboard">
                        </div>
                        <div class="form-group">
                            <label>Serial Number</label>
                            <input type="text" name="serial_number" class="form-control" placeholder="Optional">
                        </div>
                        <div class="form-group">
                            <label>Department</label>
                            <select name="department_id" class="form-control">
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Value</label>
                                    <input type="number" step="0.01" name="value" class="form-control" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Purchase Date</label>
                                    <input type="date" name="purchase_date" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Condition</label>
                            <select name="condition" class="form-control">
                                <option value="new">New</option>
                                <option value="good" selected>Good</option>
                                <option value="fair">Fair</option>
                                <option value="poor">Poor</option>
                                <option value="broken">Broken</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save Asset</button>
                        <a href="{{ route('assets.index') }}" class="btn btn-default float-right">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
