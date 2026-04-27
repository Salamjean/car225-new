<?php

namespace App\Http\Controllers\Onpc;

use App\Http\Controllers\Controller;
use App\Models\Onpc;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

/**
 * Réinitialisation du mot de passe d'un agent ONPC via OTP envoyé
 * par email. Utilise la table `password_reset_tokens` (commune à tout
 * le système Laravel).
 */
class OnpcPasswordResetController extends Controller
{
    public function showResetForm()
    {
        return view('onpc.auth.password-reset');
    }

    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $onpc = Onpc::where('email', $request->email)->first();
        if (!$onpc) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun agent ONPC n\'existe avec cette adresse email.',
            ]);
        }

        $email   = $request->email;
        $otpCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('password_reset_tokens')->where('email', $email)->delete();
        DB::table('password_reset_tokens')->insert([
            'email'      => $email,
            'token'      => Hash::make($otpCode),
            'created_at' => now(),
        ]);

        try {
            Mail::send('emails.onpc-otp', ['otp' => $otpCode, 'name' => $onpc->name], function ($message) use ($email) {
                $message->to($email)->subject('Réinitialisation ONPC - Car 225');
            });
            return response()->json(['success' => true, 'message' => 'OTP envoyé.', 'email' => $email]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Erreur d\'envoi de l\'email.'], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string|size:6',
        ]);

        $row = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$row || Carbon::parse($row->created_at)->addMinutes(10)->isPast()) {
            return response()->json(['success' => false, 'message' => 'OTP expiré.'], 422);
        }

        if (!Hash::check($request->otp, $row->token)) {
            return response()->json(['success' => false, 'message' => 'OTP incorrect.'], 422);
        }

        return response()->json(['success' => true, 'message' => 'OTP vérifié.']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|exists:onpcs,email',
            'otp'      => 'required|string|size:6',
            'password' => 'required|min:8|confirmed',
        ]);

        $row = DB::table('password_reset_tokens')->where('email', $request->email)->first();
        if (!$row || !Hash::check($request->otp, $row->token)) {
            return response()->json(['success' => false, 'message' => 'OTP invalide.'], 422);
        }

        Onpc::where('email', $request->email)->update(['password' => Hash::make($request->password)]);
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['success' => true, 'message' => 'Mot de passe réinitialisé.']);
    }
}
