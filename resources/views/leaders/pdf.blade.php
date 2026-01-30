<!DOCTYPE html>
<html>
<head>
    <title>{{ __('Church Leaders') }}</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }
        th { background-color: #f2f2f2; }
        h1 { text-align: center; color: #333; }
        .meta { margin-bottom: 20px; font-size: 12px; color: #666; text-align: center; }
    </style>
</head>
<body>
    <h1>{{ __('Church Leadership Team') }}</h1>
    <div class="meta">{{ __('Generated on') }} {{ date('F d, Y') }}</div>

    <table>
        <thead>
            <tr>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Type') }}</th>
                <th>{{ __('Role') }}</th>
                <th>{{ __('Context / Department') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Phone') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($leaders as $leader)
            <tr>
                <td>{{ $leader['name'] }}</td>
                <td>{{ $leader['type'] }}</td>
                <td>{{ $leader['role'] }}</td>
                <td>{{ $leader['context'] }}</td>
                <td>{{ $leader['email'] }}</td>
                <td>{{ $leader['phone'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
