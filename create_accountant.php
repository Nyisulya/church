<?php

/**
 * ==============================================
 * CREATE ACCOUNTANT (MHASIBU) - Church Management System
 * ==============================================
 * Creates a user with the restricted "accountant" role.
 * This role only sees giving/contributions (sadaka) and members.
 *
 * Usage:
 *   php create_accountant.php
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
$name     = 'Mhasibu';
$email    = 'accountant@sdachurch.com';   // ← email ya kulogin
$password = 'Accountant@2025!';            // ← neno siri (badilisha baadaye)
// ─────────────────────────────────────────────────────────────────

echo "\n==========================================\n";
echo "  Church CMS - Accountant (Mhasibu) Setup\n";
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

// 3. Hakikisha role 'accountant' ipo
$role = Role::firstOrCreate(
    ['name' => 'accountant', 'guard_name' => 'web']
);
echo "✅ Role 'accountant' ipo.\n";

// 4. Weka permissions chache tu (sadaka + washiriki)
$role->syncPermissions([
    'member-view',
    'finance-view',
    'finance-create',
]);
echo "✅ Permissions zimewekwa (member-view, finance-view, finance-create).\n";

// 5. Ongeza role kwa user
if (! $user->hasRole('accountant')) {
    $user->assignRole('accountant');
    echo "✅ Role 'accountant' imewekwa kwa {$user->name}.\n";
} else {
    echo "ℹ️  {$user->name} tayari ana role 'accountant'.\n";
}

// 6. Weka cache ya permissions upya
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

echo "\n==========================================\n";
echo "  ✅ MHASIBU YUKO TAYARI!\n";
echo "==========================================\n";
echo "  URL      : " . config('app.url') . "\n";
echo "  Email    : {$email}\n";
echo "  Password : {$password}\n";
echo "  \n";
echo "  ⚠️  BADILISHA password baada ya kulogin!\n";
echo "==========================================\n\n";
