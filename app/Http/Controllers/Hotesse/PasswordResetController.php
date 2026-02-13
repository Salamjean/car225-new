<?php

namespace App\Http\Controllers\Hotesse;

use App\Http\Controllers\Controller;
use App\Models\Hotesse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    public function showResetForm()
    {
        return view('hotesse.auth.password-reset');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $hotesse = Hotesse::where('email', $request->email)->first();
        if (!$hotesse) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune hôtesse n\'existe avec cette adresse email.'
            ]);
        }

        $email = $request->email;
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        DB::table('password_reset_tokens')->where('email', $email)->delete();
        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => Hash::make($otpCode),
            'created_at' => now()
        ]);

        try {
            Mail::send('emails.otp', ['otp' => $otpCode], function ($message) use ($email) {
                $message->to($email)->subject('Code de réinitialisation Hôtesse - Car225');
            });
            return response()->json(['success' => true, 'message' => 'OTP envoyé.', 'email' => $email]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur envoi email.'], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['email' => 'required|email', 'otp' => 'required|string|size:6']);
        $resetRecord = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$resetRecord || Carbon::parse($resetRecord->created_at)->addMinutes(10)->isPast()) {
            return response()->json(['success' => false, 'message' => 'OTP expiré.'], 422);
        }

        if (!Hash::check($request->otp, $resetRecord->token)) {
            return response()->json(['success' => false, 'message' => 'OTP incorrect.'], 422);
        }

        return response()->json(['success' => true, 'message' => 'OTP vérifié.']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:hotesses,email',
            'otp' => 'required|string|size:6',
            'password' => 'required|min:8|confirmed'
        ]);

        $resetRecord = DB::table('password_reset_tokens')->where('email', $request->email)->first();
        if (!$resetRecord || !Hash::check($request->otp, $resetRecord->token)) {
            return response()->json(['success' => false, 'message' => 'OTP invalide.'], 422);
        }

        Hotesse::where('email', $request->email)->update(['password' => Hash::make($request->password)]);
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['success' => true, 'message' => 'Mot de passe réinitialisé.']);
    }
}
