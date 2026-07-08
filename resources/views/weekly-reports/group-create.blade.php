@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h1 class="m-0 text-dark">👥 {{ __('Ripoti ya Jumla ya Kanda (Group Weekly Report)') }}</h1>
            <p class="text-muted">{{ __('Wasilisha idadi ya jumla ya wanakanda waliofanya shughuli mbalimbali za kiroho') }}</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title">{{ __('Muda wa Ripoti: Wiki ya') }} {{ $weekRange }}</h3>
                </div>
                <form action="{{ route('weekly-reports.group.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                        @endif

                        <p class="mb-4 text-info">
                            <i class="fas fa-info-circle"></i> {{ __('Hapa unaingiza IDADI YA JUMLA (Total Counts) ya wanakanda wote waliohudhuria au kufanya shughuli husika kwa wiki hii (ikiwa ni pamoja na wasio na simu janja).') }}
                        </p>

                        <!-- Kanda Selection -->
                        <div class="form-group border-bottom pb-3 mb-4">
                            <label for="small_group_id" class="font-weight-bold">{{ __('Kanda / Small Group') }}</label>
                            @if(count($smallGroups) > 1)
                                <select name="small_group_id" id="small_group_id" class="form-control" onchange="window.location.href = '{{ route('weekly-reports.group.create') }}?small_group_id=' + this.value">
                                    @foreach($smallGroups as $group)
                                        <option value="{{ $group->id }}" {{ $selectedGroup->id == $group->id ? 'selected' : '' }}>
                                            {{ $group->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input type="hidden" name="small_group_id" value="{{ $selectedGroup->id }}">
                                <input type="text" class="form-control" value="{{ $selectedGroup->name }}" readonly>
                            @endif
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

                            <!-- For group report, YES_NO questions become NUMBER inputs (how many did yes) -->
                            @if($question->response_type === 'number' || $question->response_type === 'yes_no')
                                <div class="input-group">
                                    <input type="number" 
                                           name="responses[{{ $question->id }}]" 
                                           class="form-control" 
                                           placeholder="{{ $question->response_type === 'yes_no' ? 'Idadi ya waumini waliofanya hivyo' : 'Weka namba' }}"
                                           min="0"
                                           value="{{ $currentValue ?? 0 }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text">{{ __('Waumini / Watu') }}</span>
                                    </div>
                                </div>
                            
                            @elseif($question->response_type === 'amount')
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">TSh</span>
                                    </div>
                                    <input type="number" 
                                           name="responses[{{ $question->id }}]" 
                                           class="form-control" 
                                           placeholder="Enter total amount"
                                           min="0"
                                           step="100"
                                           value="{{ $currentValue ?? 0 }}">
                                </div>
                            
                            @else
                                <input type="text" 
                                       name="responses[{{ $question->id }}]" 
                                       class="form-control" 
                                       placeholder="Enter response details"
                                       value="{{ $currentValue }}">
                            @endif
                        </div>
                        @endforeach
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-save"></i> {{ __('Hifadhi Ripoti ya Jumla') }}
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
                    <h5 class="card-title mb-0">👥 {{ __('Ripoti ya Kikundi') }}</h5>
                </div>
                <div class="card-body">
                    <p><strong>Note:</strong></p>
                    <ul class="pl-3">
                        <li>Jaza ripoti baada ya kukusanya idadi za jumla kutoka kwa wanakanda wako wakati wa mkutano wenu wa kanda.</li>
                        <li>Ripoti hii itahesabika kama ripoti rasmi ya kanda na itatumika kwenye takwimu kuu za kanisa.</li>
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
