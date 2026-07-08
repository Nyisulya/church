@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h1 class="m-0 text-dark">📊 {{ __('Ripoti za Wiki za Kanda zote') }} (Church Analytics Dashboard)</h1>
            <p class="text-muted">{{ __('Takwimu kuu za ripoti za wiki za kanisa zote - Wiki ya') }} {{ $weekRange }}</p>
        </div>
    </div>

    <!-- Church-Wide Statistics -->
    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $churchStats['participation_rate'] }}%</h3>
                    <p>{{ __('Kiwango cha Ushiriki') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $churchStats['total_evangelism_visits'] }}</h3>
                    <p>{{ __('Jumla ya Mitembeleo ya Uinjilisti') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-plus"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $churchStats['total_community_help'] }}</h3>
                    <p>{{ __('Matendo ya Misaada kwa Jamii') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-hands-helping"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $churchStats['total_submissions'] }}/{{ $churchStats['total_members'] }}</h3>
                    <p>{{ __('Ripoti Zilizowasilishwa') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-alt"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Group Comparison Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Ushiriki wa Ripoti Kikanda') }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('small-groups.questions.index') }}" class="btn btn-sm btn-info">
                            <i class="fas fa-cog"></i> {{ __('Simamia Maswali ya Ripoti') }}
                        </a>
                        <a href="{{ route('weekly-reports.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('Ripoti Kuu') }}
                        </a>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('Jina la Kanda') }}</th>
                                <th>{{ __('Kiongozi') }}</th>
                                <th>{{ __('Ripoti ya Jumla (Group)') }}</th>
                                <th>{{ __('Ripoti za Binafsi') }}</th>
                                <th>{{ __('Kiwango cha Ushiriki Binafsi') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($groups as $groupData)
                            <tr>
                                <td><strong>{{ $groupData['group']->name }}</strong></td>
                                <td>{{ $groupData['group']->leader->full_name ?? 'Hakuna' }}</td>
                                <td>
                                    @if($groupData['has_group_report'])
                                        <span class="badge badge-success"><i class="fas fa-check"></i> {{ __('Imewasilishwa') }}</span>
                                    @else
                                        <span class="badge badge-secondary"><i class="fas fa-times"></i> {{ __('Haijawasilishwa') }}</span>
                                    @endif
                                </td>
                                <td>{{ $groupData['submitted_count'] }} / {{ $groupData['total_members'] }}</td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar 
                                            @if($groupData['participation_rate'] >= 80) bg-success
                                            @elseif($groupData['participation_rate'] >= 50) bg-warning
                                            @else bg-danger
                                            @endif" 
                                            role="progressbar" 
                                            style="width: {{ $groupData['participation_rate'] }}%">
                                            {{ $groupData['participation_rate'] }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">{{ __('Hakuna kanda zilizopo kwa sasa.') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Insights -->
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">💡 {{ __('Dondoo za Wiki hii') }}</h5>
                </div>
                <div class="card-body">
                    <h6>Highlights:</h6>
                    <ul>
                        <li><strong>{{ $churchStats['total_evangelism_visits'] }}</strong> {{ __('mitembeleo ya uinjilisti imefanywa kanisa zima') }}</li>
                        <li><strong>{{ $churchStats['total_community_help'] }}</strong> {{ __('matendo ya misaada kwa jamii yaliyofanyika') }}</li>
                        <li><strong>{{ $churchStats['participation_rate'] }}%</strong> {{ __('ya washiriki wamewasilisha ripoti zao') }}</li>
                    </ul>

                    @if($churchStats['participation_rate'] < 50)
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="fas fa-exclamation-triangle"></i> <strong>Himizeni Viongozi:</strong> 
                        Ushiriki wa jumla wa washiriki bado uko chini ya 50%. Tafadhali wahimize viongozi wa kanda wasaidie kukusanya ripoti.
                    </div>
                    @elseif($churchStats['participation_rate'] >= 80)
                    <div class="alert alert-success mt-3 mb-0">
                        <i class="fas fa-trophy"></i> <strong>Hongera sana!</strong> 
                        Ushiriki wa wiki hii ni bora na wa kiwango cha juu sana. Keep up the great work!
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-header">
                    <h5 class="card-title mb-0">📅 {{ __('Muhtasari wa Kipindi') }}</h5>
                </div>
                <div class="card-body">
                    <p><strong>{{ __('Wiki:') }}</strong> {{ $weekRange }}</p>
                    <p><strong>{{ __('Jumla ya Kanda:') }}</strong> {{ $groups->count() }}</p>
                    <p><strong>{{ __('Jumla ya Washiriki:') }}</strong> {{ $churchStats['total_members'] }}</p>
                    <p><strong>{{ __('Jumla ya Ripoti:') }}</strong> {{ $churchStats['total_submissions'] }}</p>
                    <hr>
                    <p class="text-muted mb-0"><small>Ripoti zinakusanywa kuanzia Sabato hadi Ijumaa (Wiki ya Waadventista)</small></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
