<?php

namespace App\Http\Controllers\Portail;

use App\Http\Controllers\Controller;
use App\Models\Compagnie;
use App\Models\Agent;
use App\Models\Hotesse;
use App\Models\Personnel;
use App\Models\Gare;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PortailPasswordResetController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Affiche la page de réinitialisation.
     */
    public function showLinkRequestForm()
    {
        return view('portail.auth.password-reset');
    }

    /**
     * Étape 1 : Envoyer l'OTP par email/SMS.
     */
    public function sendOtpCode(Request $request)
    {
        $request->validate([
            'identity' => 'required|string'
        ]);

        $identity = $request->identity;
        $user = null;
        $guard = '';

        // Recherche multi-modèles
        $isEmail = filter_var($identity, FILTER_VALIDATE_EMAIL);

        if ($isEmail) {
            // Uniquement Compagnie par Email
            $user = Compagnie::where('email', $identity)->first();
            if ($user) $guard = 'compagnie';
        } else {
            // Uniquement Autres par Code ID
            $others = [
                'agent' => Agent::class,
                'hotesse' => Hotesse::class,
                'chauffeur' => Personnel::class,
                'gare' => Gare::class
            ];

            foreach ($others as $name => $class) {
                // Pour ces modèles, on sait qu'ils ont code_id (via HasCodeId trait)
                $user = $class::where('code_id', $identity)->first();
                if ($user) {
                    $guard = $name;
                    break;
                }
            }
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun compte associé à cet identifiant.'
            ], 404);
        }

        // Email et Contact pour l'envoi
        $email = $user->email;
        $contact = $user->contact;
        
        // Identifiant pour le token : on utilise STRICTEMENT ce que l'utilisateur a saisi
        // pour rester cohérent avec la règle Email (Compagnie) vs Code ID (Others)
        $tokenIdentifier = $identity;

        // Générer OTP (6 chiffres)
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Enregistrer le token
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $tokenIdentifier],
            [
                'token' => Hash::make($otp),
                'created_at' => now()
            ]
        );

        $sentByEmail = false;
        $sentBySms = false;

        // Envoi Email
        if ($email) {
            try {
                Mail::send('emails.otp', ['otp' => $otp], function ($message) use ($email) {
                    $message->to($email)->subject('Code de réinitialisation - Car225');
                });
                $sentByEmail = true;
            } catch (\Exception $e) {
                Log::error("Reset Password Mail Error ($guard): " . $e->getMessage());
            }
        }

        // Envoi SMS
        if ($contact) {
            try {
                $smsMessage = "Votre code de reinitialisation Car225 est : $otp. Valide 10 min.";
                $sentBySms = $this->smsService->sendSms($contact, $smsMessage);
            } catch (\Exception $e) {
                Log::error("Reset Password SMS Error ($guard): " . $e->getMessage());
            }
        }

        if ($sentByEmail || $sentBySms) {
            $channels = [];
            if ($sentByEmail) $channels[] = 'Email';
            if ($sentBySms) $channels[] = 'SMS';
            
            return response()->json([
                'success' => true,
                'message' => 'Code envoyé par ' . implode(' et ', $channels) . '.',
                'identifier' => $tokenIdentifier
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Impossible d\'envoyer le code. Vérifiez vos informations de contact.'
        ], 500);
    }

    /**
     * Étape 2 : Vérifier l'OTP.
     */
    public function verifyOtpCode(Request $request)
    {
        $request->validate([
            'identifier' => 'required',
            'otp' => 'required|string|size:6'
        ]);

        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->identifier)
            ->first();

        if (!$reset || !Hash::check($request->otp, $reset->token)) {
            return response()->json(['success' => false, 'message' => 'Code OTP incorrect.'], 422);
        }

        if (Carbon::parse($reset->created_at)->addMinutes(10)->isPast()) {
            return response()->json(['success' => false, 'message' => 'Code OTP expiré.'], 422);
        }

        return response()->json(['success' => true, 'message' => 'Code vérifié.']);
    }

    /**
     * Étape 3 : Réinitialiser.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'identifier' => 'required',
            'otp' => 'required',
            'password' => 'required|min:8|confirmed'
        ]);

        // Vérification finale OTP
        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->identifier)
            ->first();

        if (!$reset || !Hash::check($request->otp, $reset->token) || Carbon::parse($reset->created_at)->addMinutes(10)->isPast()) {
            return response()->json(['success' => false, 'message' => 'Session expirée ou code invalide.'], 422);
        }

        // Trouver l'utilisateur encore une fois pour la mise à jour
        $user = null;
        $isEmail = filter_var($request->identifier, FILTER_VALIDATE_EMAIL);
        
        if ($isEmail) {
            $user = Compagnie::where('email', $request->identifier)->first();
        } else {
            $others = [Agent::class, Hotesse::class, Personnel::class, Gare::class];
            foreach ($others as $class) {
                $user = $class::where('code_id', $request->identifier)->first();
                if ($user) break;
            }
        }

        if ($user) {
            Log::info("Réinitialisation mot de passe pour : " . get_class($user) . " ID: " . $user->id);
            
            // On vérifie si le modèle utilise le cast 'hashed' (Laravel 10+)
            $casts = $user->getCasts();
            $hasHashedCast = ($casts['password'] ?? '') === 'hashed';

            if ($hasHashedCast) {
                // Laravel 10+ avec cast 'hashed' hache automatiquement à l'assignation
                $user->password = $request->password;
            } else {
                // Sinon on hache manuellement
                $user->password = Hash::make($request->password);
            }
            
            $user->save();
            
            DB::table('password_reset_tokens')->where('email', $request->identifier)->delete();
            
            Log::info("Mot de passe mis à jour avec succès pour l'utilisateur.");
            
            return response()->json(['success' => true, 'message' => 'Mot de passe mis à jour avec succès !']);
        }

        return response()->json(['success' => false, 'message' => 'Erreur lors de la mise à jour.'], 500);
    }
}
