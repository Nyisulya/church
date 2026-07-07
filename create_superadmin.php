<?php

/**
 * ==============================================
 * CREATE SUPERADMIN - Church Management System
 * ==============================================
 * Tumia script hii kwenye VPS:
 *   php create_superadmin.php
 * ==============================================
 */

require __DIR__ . '/vendor/autoload.php';

$app    = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

// ─── BADILISHA HAPA ──────────────────────────────────────────────
$name     = 'Super Admin';
$email    = 'admin@manzesesda.com';   // ← email yako ya kulogin
$password = 'Admin@2025!';            // ← neno siri (badilisha baadaye)
// ─────────────────────────────────────────────────────────────────

echo "\n==========================================\n";
echo "  Church CMS - Superadmin Setup\n";
echo "==========================================\n\n";

// 1. Angalia kama user tayari yupo
$existing = User::where('email', $email)->first();
if ($existing) {
    echo "⚠️  User '{$email}' tayari yupo (ID: {$existing->id}).\n";
    echo "   Inaendelea kuongeza role tu...\n\n";
    $user = $existing;
} else {
    // 2. Unda user mpya
    $user = User::create([
        'name'              => $name,
        'email'             => $email,
        'password'          => Hash::make($password),
        'email_verified_at' => now(),
    ]);
    echo "✅ User ameundwa:\n";
    echo "   📧 Email : {$email}\n";
    echo "   🔑 Neno  : {$password}\n\n";
}

// 3. Hakikisha role 'super_admin' ipo
$role = Role::firstOrCreate(
    ['name' => 'super_admin', 'guard_name' => 'web']
);
echo "✅ Role 'super_admin' ipo.\n";

// 4. Ongeza role kwa user
if (! $user->hasRole('super_admin')) {
    $user->assignRole('super_admin');
    echo "✅ Role 'super_admin' imewekwa kwa {$user->name}.\n";
} else {
    echo "ℹ️  {$user->name} tayari ana role 'super_admin'.\n";
}

// 5. Weka cache ya permissions upya
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

echo "\n==========================================\n";
echo "  ✅ SUPERADMIN YUKO TAYARI!\n";
echo "==========================================\n";
echo "  URL      : " . config('app.url') . "\n";
echo "  Email    : {$email}\n";
echo "  Password : {$password}\n";
echo "  \n";
echo "  ⚠️  BADILISHA password baada ya kulogin!\n";
echo "==========================================\n\n";
