@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h1 class="m-0 text-dark">✏️ {{ __('Hariri Ripoti ya Wiki') }} - {{ $weekRange }}</h1>
            <p class="text-muted">{{ __('Marekebisho ya ripoti yako ya wiki') }}</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h3 class="card-title">{{ __('Wiki ya') }} {{ $weekRange }}</h3>
                </div>
                <form action="{{ route('weekly-reports.update', $weekStartDate->format('Y-m-d')) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                        @endif

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> {{ __('Unahariri ripoti uliyoiwasilisha awali. Mabadiliko yatahifadhiwa mara moja.') }}
                        </div>

                        <!-- Optional Kanda Selection -->
                        <div class="form-group border-bottom pb-3 mb-4">
                            <label for="small_group_id" class="font-weight-bold">{{ __('Kanda / Small Group') }} ({{ __('Optional / Hiari') }})</label>
                            <select name="small_group_id" id="small_group_id" class="form-control">
                                <option value="">-- {{ __('Chagua Kanda kama ulihudhuria') }} --</option>
                                @foreach($smallGroups as $group)
                                    <option value="{{ $group->id }}" {{ $selectedGroupId == $group->id ? 'selected' : '' }}>
                                        {{ $group->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-muted"><small>{{ __('Unaweza kubadilisha au kuondoa kanda uliyohudhuria.') }}</small></span>
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
                            <i class="fas fa-save"></i> {{ __('Hifadhi Mabadiliko') }}
                        </button>
                        <a href="{{ route('weekly-reports.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> {{ __('Ghairi') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-header">
                    <h5 class="card-title mb-0">ℹ️ {{ __('Hariri Mode') }}</h5>
                </div>
                <div class="card-body">
                    <p><strong>Note:</strong></p>
                    <ul class="pl-3">
                        <li>Marekebisho yatahifadhiwa na kubadilisha yale ya awali</li>
                        <li>Muda wa uwasilishaji utasasishwa kuwa wa sasa</li>
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
