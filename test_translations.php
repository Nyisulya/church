<?php

// Quick script to test language translations
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

// Start Laravel
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test locale
echo "Current Locale: " . app()->getLocale() . "\n";
echo "Config Locale: " . config('app.locale') . "\n\n";

// Test translation loading
echo "English Translation Test:\n";
app()->setLocale('en');
echo "'Dashboard' => " . __('Dashboard') . "\n";
echo "'My Profile' => " . __('My Profile') . "\n\n";

echo "Kiswahili Translation Test:\n";
app()->setLocale('sw');
echo "'Dashboard' => " . __('Dashboard') . "\n";
echo "'My Profile' => " . __('My Profile') . "\n\n";

// Check if translation files exist
echo "Translation Files:\n";
echo "en.json exists: " . (file_exists(__DIR__.'/lang/en.json') ? 'YES' : 'NO') . "\n";
echo "sw.json exists: " . (file_exists(__DIR__.'/lang/sw.json') ? 'YES' : 'NO') . "\n\n";

// Show content of sw.json (first 5 items)
if (file_exists(__DIR__.'/lang/sw.json')) {
    $sw = json_decode(file_get_contents(__DIR__.'/lang/sw.json'), true);
    echo "Sample Kiswahili Translations:\n";
    $count = 0;
    foreach ($sw as $key => $value) {
        echo "  '$key' => '$value'\n";
        if (++$count >= 5) break;
    }
}
