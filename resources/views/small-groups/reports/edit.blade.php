@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h1 class="m-0 text-dark">✏️ Edit Weekly Report - {{ $weekRange }}</h1>
            <p class="text-muted">Modify your submitted weekly report</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h3 class="card-title">Week of {{ $weekRange }}</h3>
                    <p class="mb-0"><small>{{ $smallGroup->name }}</small></p>
                </div>
                <form action="{{ route('small-groups.reports.update', $weekStartDate->format('Y-m-d')) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                        @endif

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> You are editing your previously submitted report. Changes will be saved immediately.
                        </div>

                        @foreach($questions as $question)
                        @php
                            $existingResponse = $existingResponses->get($question->id);
                            $currentValue = old('responses.'.$question->id, $existingResponse?->response_value);
                        @endphp
                        <div class="form-group border-bottom pb-3 mb-4">
                            <label class="font-weight-bold">
                                {{ $loop->iteration }}. {{ $question->question_sw }}
                            </label>
                            <p class="text-muted mb-2"><small>{{ $question->question_en }}</small></p>

                            @if($question->response_type === 'number')
                                <input type="number" 
                                       name="responses[{{ $question->id }}]" 
                                       class="form-control" 
                                       placeholder="Enter number"
                                       min="0"
                                       value="{{ $currentValue ?? 0 }}">
                            
                            @elseif($question->response_type === 'yes_no')
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" 
                                           id="q{{ $question->id }}_yes" 
                                           name="responses[{{ $question->id }}]" 
                                           class="custom-control-input" 
                                           value="1"
                                           {{ $currentValue === '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="q{{ $question->id }}_yes">
                                        <span class="text-success">✓ Yes / Ndio</span>
                                    </label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" 
                                           id="q{{ $question->id }}_no" 
                                           name="responses[{{ $question->id }}]" 
                                           class="custom-control-input" 
                                           value="0"
                                           {{ $currentValue === '0' || !$currentValue ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="q{{ $question->id }}_no">
                                        <span class="text-danger">✗ No / Hapana</span>
                                    </label>
                                </div>
                            
                            @elseif($question->response_type === 'amount')
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">TSh</span>
                                    </div>
                                    <input type="number" 
                                           name="responses[{{ $question->id }}]" 
                                           class="form-control" 
                                           placeholder="Enter amount"
                                           min="0"
                                           step="100"
                                           value="{{ $currentValue ?? 0 }}">
                                </div>
                            
                            @else
                                <input type="text" 
                                       name="responses[{{ $question->id }}]" 
                                       class="form-control" 
                                       placeholder="Enter your response"
                                       value="{{ $currentValue }}">
                            @endif
                        </div>
                        @endforeach
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Update Report
                        </button>
                        <a href="{{ route('small-groups.reports.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-header">
                    <h5 class="card-title mb-0">ℹ️ Edit Mode</h5>
                </div>
                <div class="card-body">
                    <p><strong>Note:</strong></p>
                    <ul class="pl-3">
                        <li>Your original submission will be updated</li>
                        <li>Submission timestamp will be refreshed</li>
                        <li>Leaders can see updated responses</li>
                    </ul>
                    <hr>
                    <p><strong>Reporting Week:</strong></p>
                    <p class="mb-0">{{ $weekStartDate->format('l, M d, Y') }} - {{ $weekStartDate->copy()->addDays(6)->format('l, M d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
