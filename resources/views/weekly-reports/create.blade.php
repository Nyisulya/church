@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h1 class="m-0 text-dark">📝 {{ __('Ripoti ya Wiki Binafsi') }} - {{ $weekRange }}</h1>
            <p class="text-muted">{{ __('Wasilisha ripoti ya shughuli zako za kiroho kwa wiki') }}</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">{{ __('Wiki ya') }} {{ $weekRange }}</h3>
                </div>
                <form action="{{ route('weekly-reports.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                        @endif

                        <p class="mb-4 text-info">
                            <i class="fas fa-info-circle"></i> {{ __('Tafadhali jibu maswali yote kwa uaminifu. Hii inatusaidia kufuatilia ukuaji wetu wa kiroho pamoja.') }}
                        </p>

                        <!-- Optional Kanda Selection -->
                        <div class="form-group border-bottom pb-3 mb-4">
                            <label for="small_group_id" class="font-weight-bold">{{ __('Kanda / Small Group') }} ({{ __('Optional / Hiari') }})</label>
                            <select name="small_group_id" id="small_group_id" class="form-control">
                                <option value="">-- {{ __('Chagua Kanda kama ulihudhuria') }} --</option>
                                @foreach($smallGroups as $group)
                                    <option value="{{ $group->id }}" {{ (isset($defaultGroup) && $defaultGroup->id == $group->id) ? 'selected' : '' }}>
                                        {{ $group->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-muted"><small>{{ __('Chagua kanda uliyohudhuria au uliyopo ili kuripoti ripoti yako chini ya kanda hiyo.') }}</small></span>
                        </div>

                        @foreach($questions as $question)
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
                                       value="{{ old('responses.'.$question->id, 0) }}">
                            
                            @elseif($question->response_type === 'yes_no')
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" 
                                           id="q{{ $question->id }}_yes" 
                                           name="responses[{{ $question->id }}]" 
                                           class="custom-control-input" 
                                           value="1"
                                           {{ old('responses.'.$question->id) === '1' ? 'checked' : '' }}>
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
                                           {{ old('responses.'.$question->id, '0') === '0' ? 'checked' : '' }}>
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
                                           value="{{ old('responses.'.$question->id, 0) }}">
                                </div>
                            
                            @else
                                <input type="text" 
                                       name="responses[{{ $question->id }}]" 
                                       class="form-control" 
                                       placeholder="Enter your response"
                                       value="{{ old('responses.'.$question->id) }}">
                            @endif
                        </div>
                        @endforeach
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-paper-plane"></i> {{ __('Wasilisha Ripoti') }}
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
                    <h5 class="card-title mb-0">ℹ️ {{ __('Maelekezo') }}</h5>
                </div>
                <div class="card-body">
                    <p><strong>Maelekezo / Instructions:</strong></p>
                    <ul class="pl-3">
                        <li>Jibu kila swali kwa uaminifu</li>
                        <li>Unaweza kuwasilisha ripoti mara moja tu kwa wiki</li>
                        <li>Unaweza kuhariri ripoti yako baadaye ikihitajika</li>
                    </ul>
                    <hr>
                    <p><strong>Reporting Week:</strong></p>
                    <p class="mb-0">{{ $currentWeek->format('l, M d, Y') }} - {{ $currentWeek->copy()->addDays(6)->format('l, M d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
