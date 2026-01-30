@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h1 class="m-0 text-dark">📊 Smart Analytics Dashboard</h1>
            <p class="text-muted">Real-time insights into church growth, finance, and attendance.</p>
        </div>
    </div>

    <!-- Key Metrics Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $memberGrowth->sum('count') }}</h3>
                    <p>New Members (Last 12 Mo)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-plus"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($financials->where('type', 'income')->sum('total')) }}</h3>
                    <p>Total Income (Last 6 Mo)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format($attendance->avg('count')) }}</h3>
                    <p>Avg Weekly Attendance</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $ageGroups->where('age_group', '18-35')->sum('total') }}</h3>
                    <p>Youth (18-35)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-child"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row">
        <!-- Member Growth -->
        <div class="col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Member Growth Trend</h3>
                </div>
                <div class="card-body">
                    <canvas id="memberGrowthChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
        <!-- Financial Overview -->
        <div class="col-md-6">
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title">Income vs Expenses</h3>
                </div>
                <div class="card-body">
                    <canvas id="financialChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row">
        <!-- Demographics -->
        <div class="col-md-4">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title">Gender Distribution</h3>
                </div>
                <div class="card-body">
                    <canvas id="genderChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-warning card-outline">
                <div class="card-header">
                    <h3 class="card-title">Age Groups</h3>
                </div>
                <div class="card-body">
                    <canvas id="ageChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-danger card-outline">
                <div class="card-header">
                    <h3 class="card-title">Marital Status</h3>
                </div>
                <div class="card-body">
                    <canvas id="maritalChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Member Growth Chart
    const memberCtx = document.getElementById('memberGrowthChart').getContext('2d');
    new Chart(memberCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($memberGrowth->pluck('month_year')) !!},
            datasets: [{
                label: 'New Members',
                data: {!! json_encode($memberGrowth->pluck('count')) !!},
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // 2. Financial Chart
    const financialCtx = document.getElementById('financialChart').getContext('2d');
    // Process data for chart
    const financialLabels = [...new Set({!! json_encode($financials->pluck('month_year')) !!})].sort();
    const incomeData = financialLabels.map(label => {
        const item = {!! json_encode($financials) !!}.find(f => f.month_year === label && f.type === 'income');
        return item ? item.total : 0;
    });
    const expenseData = financialLabels.map(label => {
        const item = {!! json_encode($financials) !!}.find(f => f.month_year === label && f.type === 'expense');
        return item ? item.total : 0;
    });

    new Chart(financialCtx, {
        type: 'bar',
        data: {
            labels: financialLabels,
            datasets: [
                {
                    label: 'Income',
                    data: incomeData,
                    backgroundColor: '#28a745'
                },
                {
                    label: 'Expenses',
                    data: expenseData,
                    backgroundColor: '#dc3545'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // 3. Gender Chart
    new Chart(document.getElementById('genderChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($genderStats->pluck('gender')) !!},
            datasets: [{
                data: {!! json_encode($genderStats->pluck('total')) !!},
                backgroundColor: ['#36a2eb', '#ff6384', '#ffcd56']
            }]
        },
        options: { maintainAspectRatio: false }
    });

    // 4. Age Chart
    new Chart(document.getElementById('ageChart'), {
        type: 'pie',
        data: {
            labels: {!! json_encode($ageGroups->pluck('age_group')) !!},
            datasets: [{
                data: {!! json_encode($ageGroups->pluck('total')) !!},
                backgroundColor: ['#17a2b8', '#28a745', '#ffc107', '#dc3545']
            }]
        },
        options: { maintainAspectRatio: false }
    });

    // 5. Marital Chart
    new Chart(document.getElementById('maritalChart'), {
        type: 'polarArea',
        data: {
            labels: {!! json_encode($maritalStats->pluck('marital_status')) !!},
            datasets: [{
                data: {!! json_encode($maritalStats->pluck('total')) !!},
                backgroundColor: ['#6610f2', '#e83e8c', '#fd7e14', '#20c997', '#6c757d']
            }]
        },
        options: { maintainAspectRatio: false }
    });
});
</script>
@endsection
