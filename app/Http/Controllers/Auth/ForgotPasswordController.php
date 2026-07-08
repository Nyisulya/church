<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    /**
     * Show the form to request a password reset link.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send a reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $email = $request->email;
        $user = User::where('email', $email)->first();

        // Generate token
        $token = Str::random(60);

        // Save token to database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now()
            ]
        );

        // Generate reset link
        $resetLink = route('password.reset', ['token' => $token, 'email' => $email]);

        try {
            // Send the email using Laravel Mail
            Mail::send([], [], function ($message) use ($email, $resetLink) {
                $message->to($email)
                    ->subject('Kuweka Upya Nenosiri - Manzese SDA')
                    ->html('
                        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px;">
                            <h2 style="color: #1e3a8a; text-align: center;">Manzese SDA Church</h2>
                            <p>Habari,</p>
                            <p>Umepokea barua pepe hii kwa sababu ulituma ombi la kuweka upya nenosiri la akaunti yako.</p>
                            <div style="text-align: center; margin: 30px 0;">
                                <a href="' . $resetLink . '" style="background-color: #1e3a8a; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">Weka Upya Nenosiri</a>
                            </div>
                            <p>Link hii itaisha muda wake baada ya dakika 60.</p>
                            <p>Kama hukutuma ombi hili, hakuna hatua nyingine inayohitajika.</p>
                            <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;">
                            <p style="font-size: 12px; color: #64748b; text-align: center;">&copy; ' . date('Y') . ' Manzese Seventh-day Adventist Church. Haki zote zimehifadhiwa.</p>
                        </div>
                    ');
            });

            return back()->with('status', 'Link ya kuweka upya nenosiri imetumwa kwenye barua pepe yako (Email).');

        } catch (\Exception $e) {
            // Log error
            logger()->error('Failed to send password reset email: ' . $e->getMessage());

            // User-friendly fallback message for SMTP failures
            return back()->with('error', 'Mfumo umeshindwa kutuma barua pepe ya kujiunga upya (SMTP Config Error). Tafadhali wasiliana na Katibu wa Kanisa au Mtunza Mfumo (Admin) ili akubadilishie nenosiri lako moja kwa moja.');
        }
    }

    /**
     * Show the password reset form.
     */
    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Reset the given user's password.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'Token hii si sahihi au imeisha muda wake.']);
        }

        // Check token expiration (e.g. 60 minutes)
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Link hii imeisha muda wake (iliisha baada ya dakika 60). Tafadhali omba nyingine.']);
        }

        // Update password
        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Delete token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Nenosiri lako limewekwa upya kikamilifu! Sasa unaweza kuingia.');
    }
}
