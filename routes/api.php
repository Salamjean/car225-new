<?php

use App\Http\Controllers\Api\User\AuthController as UserAuthController;
use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\Api\User\ReservationApiController as UserReservationController;
use App\Http\Controllers\Api\User\WalletController;
use App\Http\Controllers\Api\User\CompagnieController;
use App\Http\Controllers\Api\User\SignalementApiController;
use App\Http\Controllers\Api\User\SupportApiController;
use App\Http\Controllers\Api\User\StatistiqueApiController;
use App\Http\Controllers\Api\Agent\AuthController as AgentAuthController;
use App\Http\Controllers\Api\Agent\AgentController;
use App\Http\Controllers\Api\Agent\ReservationApiController as AgentReservationController;
use App\Http\Controllers\Api\UnifiedAuthController;
use App\Http\Controllers\Api\PublicSignalementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Mobile Application
|--------------------------------------------------------------------------
|
| Ces routes sont destinées aux applications mobiles User et Agent.
| Toutes les routes authentifiées utilisent Laravel Sanctum.
|
*/

// ============================================================================
// CONNEXION UNIFIÉE (Mobile - toutes interfaces)
// ============================================================================

Route::post('/unified-login', [UnifiedAuthController::class, 'login']);

// ============================================================================
// USER API ROUTES
// ============================================================================

Route::prefix('user')->group(function () {
    
    // Routes publiques (sans authentification)
    Route::post('/login', [UserAuthController::class, 'login']);
    Route::post('/register', [UserAuthController::class, 'register']);
    Route::post('/google-auth', [UserAuthController::class, 'googleAuth']);
    
    // Mot de passe oublié
    Route::post('/password/send-otp', [UserAuthController::class, 'sendOtp']);
    Route::post('/password/verify-otp', [UserAuthController::class, 'verifyOtp']);
    Route::post('/password/reset', [UserAuthController::class, 'resetPassword']);
    
    // Vérification OTP téléphone (inscription/login)
    Route::post('/verify-phone-otp', [UserAuthController::class, 'verifyPhoneOtp']);
    Route::post('/resend-phone-otp', [UserAuthController::class, 'resendPhoneOtp']);
    
    // Routes protégées (authentification requise)
    Route::middleware('auth:sanctum')->group(function () {
        // Authentification
        Route::post('/logout', [UserAuthController::class, 'logout']);
        
        // Profil utilisateur
        Route::get('/profile', [UserController::class, 'profile']);
        Route::put('/profile', [UserController::class, 'updateProfile']);
        Route::post('/change-password', [UserController::class, 'changePassword']);
        Route::post('/fcm-token', [UserController::class, 'updateFcmToken']);
        Route::post('/test-notification', [UserController::class, 'testNotification']);

        // Gestion du compte
        Route::get('/devices', [UserController::class, 'getDevices']);
        Route::post('/deactivate', [UserController::class, 'deactivateAccount']);
        
        // Dashboard & Suivi temps réel
        Route::get('/dashboard', [UserController::class, 'dashboard']);
        Route::get('/tracking/location', [UserController::class, 'getTrackingLocation']);
        
        // Réservations
        Route::get('/reservations', [UserReservationController::class, 'index']);
        Route::post('/reservations', [UserReservationController::class, 'store']);
        Route::get('/reservations/{reservation}', [UserReservationController::class, 'show']);
        Route::delete('/reservations/{reservation}', [UserReservationController::class, 'cancel']);
        Route::get('/reservations/{reservation}/refund-preview', [UserReservationController::class, 'getRefundPreview']);
        Route::get('/reservations/{reservation}/ticket', [UserReservationController::class, 'ticket']);
        Route::get('/reservations/{reservation}/download', [UserReservationController::class, 'download']);
        Route::get('/reservations/{reservation}/round-trip-tickets', [UserReservationController::class, 'getRoundTripTickets']);
        
        // Modification de réservation
        Route::get('/reservations/{reservation}/modification-data', [UserReservationController::class, 'getModificationData']);
        Route::get('/reservations/{reservation}/modification-delta', [UserReservationController::class, 'calculateModificationDelta']);
        Route::put('/reservations/{reservation}/modify', [UserReservationController::class, 'processModification']);
        
        // Vérification du statut de paiement CinetPay (polling mobile)
        Route::get('/payment/status/{transactionId}', [UserReservationController::class, 'getPaymentStatus']);
        Route::post('/payment/verify/{transactionId}', [UserReservationController::class, 'verifyAndConfirmPayment']);
        
        // Portefeuille (Wallet)
        Route::get('/wallet', [WalletController::class, 'index']);
        Route::post('/wallet/recharge', [WalletController::class, 'recharge']);
        Route::post('/wallet/verify', [WalletController::class, 'verify']);

        // Signalements
        Route::get('/signalements/active-reservations', [SignalementApiController::class, 'getActiveReservations']);
        Route::get('/signalements', [SignalementApiController::class, 'index']);
        Route::post('/signalements', [SignalementApiController::class, 'store']);

        // Support Client
        Route::get('/support/categories', [SupportApiController::class, 'getCategories']);
        Route::get('/support/reservations', [SupportApiController::class, 'getReservations']);
        Route::get('/support', [SupportApiController::class, 'index']);
        Route::post('/support', [SupportApiController::class, 'store']);
        Route::post('/support/{supportRequest}/repondre', [SupportApiController::class, 'repondre']);

        // Statistiques
        Route::get('/stats', [StatistiqueApiController::class, 'index']);
        Route::get('/stats/trips', [StatistiqueApiController::class, 'tripStats']);
        Route::get('/stats/historique-voyages', [StatistiqueApiController::class, 'historiqueVoyages']);

        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\Api\User\NotificationApiController::class, 'index']);
        Route::get('/notifications/unread-count', [\App\Http\Controllers\Api\User\NotificationApiController::class, 'unreadCount']);
        Route::post('/notifications/{id}/mark-as-read', [\App\Http\Controllers\Api\User\NotificationApiController::class, 'markAsRead']);
        Route::post('/notifications/mark-all-as-read', [\App\Http\Controllers\Api\User\NotificationApiController::class, 'markAllAsRead']);
        Route::delete('/notifications/{id}', [\App\Http\Controllers\Api\User\NotificationApiController::class, 'destroy']);
    });

    // Routes Publiques (Recherche & Programmes)
    Route::get('/programmes', [UserReservationController::class, 'getAllProgrammes']);
    Route::get('/programmes/simple', [UserReservationController::class, 'getSimpleProgrammes']);
    Route::get('/programmes/aller-retour', [UserReservationController::class, 'getAllerRetourProgrammes']);

    // Compagnies (publiques)
    Route::get('/compagnies', [CompagnieController::class, 'index']);
    Route::get('/compagnies/{id}', [CompagnieController::class, 'show']);
    Route::get('/compagnies/{id}/programmes', [CompagnieController::class, 'programmes']);
    Route::get('/programmes/search', [UserReservationController::class, 'searchProgrammes']);
    Route::get('/programmes/{id}', [UserReservationController::class, 'getProgram']);
    Route::get('/programmes/{programId}/reserved-seats', [UserReservationController::class, 'getReservedSeats']);
    
    Route::get('/itineraires', [UserReservationController::class, 'getItineraires']);
    Route::post('/itineraires/search', [UserReservationController::class, 'searchProgrammesByItineraire']);
});

// ============================================================================
// AGENT API ROUTES
// ============================================================================

Route::prefix('agent')->group(function () {
    
    // Routes publiques (sans authentification)
    Route::post('/login', [AgentAuthController::class, 'login']);

    // Mot de passe oublié
    Route::post('/password/send-otp', [\App\Http\Controllers\Api\Agent\PasswordResetController::class, 'sendOtp']);
    Route::post('/password/verify-otp', [\App\Http\Controllers\Api\Agent\PasswordResetController::class, 'verifyOtp']);
    Route::post('/password/reset', [\App\Http\Controllers\Api\Agent\PasswordResetController::class, 'resetPassword']);
    
    // Routes protégées (authentification requise)
    Route::middleware('auth:sanctum')->group(function () {
        // Authentification
        Route::post('/logout', [AgentAuthController::class, 'logout']);
        
        // Profil agent
        Route::get('/profile', [AgentController::class, 'profile']);
        Route::put('/profile', [AgentController::class, 'updateProfile']);
        Route::post('/change-password', [AgentController::class, 'changePassword']);
        Route::post('/fcm-token', [AgentController::class, 'updateFcmToken']);
        Route::post('/test-notification', [AgentController::class, 'testNotification']);
        
        // Dashboard
        Route::get('/dashboard', [AgentController::class, 'dashboard']);
        
        // Réservations
        Route::get('/reservations', [AgentReservationController::class, 'index']);
        Route::post('/reservations/search', [AgentReservationController::class, 'search']);
        Route::post('/reservations/confirm', [AgentReservationController::class, 'confirm']);
        Route::post('/reservations/search-by-reference', [AgentReservationController::class, 'searchByReference']);
        Route::get('/reservations/historique', [AgentReservationController::class, 'historique']);
        
        // Véhicules et Programmes
        Route::get('/vehicles', [AgentReservationController::class, 'getVehicles']);
        Route::get('/programmes/today', [AgentReservationController::class, 'getProgrammesForScan']);
        Route::get('/programmes/{programmeId}/passengers', [AgentReservationController::class, 'getReservationsForProgramme']);
        
        // Détails réservation
        Route::get('/reservations/{reservationId}', [AgentReservationController::class, 'showReservation']);
    });
});

// ============================================================================
// CHAUFFEUR API ROUTES
// ============================================================================

Route::prefix('chauffeur')->group(function () {

    // Routes publiques (sans authentification)
    Route::post('/login', [\App\Http\Controllers\Api\Chauffeur\AuthController::class, 'login']);
    
    // Mot de passe oublié
    Route::post('/password/send-otp', [\App\Http\Controllers\Api\Chauffeur\PasswordResetController::class, 'sendOtp']);
    Route::post('/password/verify-otp', [\App\Http\Controllers\Api\Chauffeur\PasswordResetController::class, 'verifyOtp']);
    Route::post('/password/reset', [\App\Http\Controllers\Api\Chauffeur\PasswordResetController::class, 'resetPassword']);

    // Routes protégées (authentification requise)
    Route::middleware('auth:sanctum')->group(function () {
        // Authentification
        Route::post('/logout', [\App\Http\Controllers\Api\Chauffeur\AuthController::class, 'logout']);

        // Profil chauffeur
        Route::get('/profile', [\App\Http\Controllers\Api\Chauffeur\ChauffeurApiController::class, 'profile']);
        Route::put('/profile', [\App\Http\Controllers\Api\Chauffeur\ChauffeurApiController::class, 'updateProfile']);
        Route::post('/change-password', [\App\Http\Controllers\Api\Chauffeur\ChauffeurApiController::class, 'changePassword']);
        Route::post('/fcm-token', [\App\Http\Controllers\Api\Chauffeur\ChauffeurApiController::class, 'updateFcmToken']);

        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Api\Chauffeur\ChauffeurApiController::class, 'dashboard']);

        // Voyages
        Route::get('/voyages', [\App\Http\Controllers\Api\Chauffeur\VoyageApiController::class, 'index']);
        Route::get('/voyages/history', [\App\Http\Controllers\Api\Chauffeur\VoyageApiController::class, 'history']);
        Route::post('/voyages/{voyage}/confirm', [\App\Http\Controllers\Api\Chauffeur\VoyageApiController::class, 'confirm']);
        Route::post('/voyages/{voyage}/start', [\App\Http\Controllers\Api\Chauffeur\VoyageApiController::class, 'start']);
        Route::post('/voyages/{voyage}/complete', [\App\Http\Controllers\Api\Chauffeur\VoyageApiController::class, 'complete']);
        Route::post('/voyages/{voyage}/annuler', [\App\Http\Controllers\Api\Chauffeur\VoyageApiController::class, 'annuler']);
        Route::post('/voyages/{voyage}/update-location', [\App\Http\Controllers\Api\Chauffeur\VoyageApiController::class, 'updateLocation']);

        // Scan QR des réservations
        Route::get('/reservations/scan-info', [\App\Http\Controllers\Api\Chauffeur\ReservationApiController::class, 'scanInfo']);
        Route::post('/reservations/search', [\App\Http\Controllers\Api\Chauffeur\ReservationApiController::class, 'search']);
        Route::post('/reservations/confirm', [\App\Http\Controllers\Api\Chauffeur\ReservationApiController::class, 'confirm']);

        // Signalements
        Route::get('/signalements', [\App\Http\Controllers\Api\Chauffeur\SignalementApiController::class, 'index']);
        Route::get('/signalements/voyages', [\App\Http\Controllers\Api\Chauffeur\SignalementApiController::class, 'getVoyagesForSignalement']);
        Route::post('/signalements', [\App\Http\Controllers\Api\Chauffeur\SignalementApiController::class, 'store']);
        Route::get('/signalements/{signalement}', [\App\Http\Controllers\Api\Chauffeur\SignalementApiController::class, 'show']);

        // Messages
        Route::get('/messages', [\App\Http\Controllers\Api\Chauffeur\MessageApiController::class, 'index']);
        Route::post('/messages', [\App\Http\Controllers\Api\Chauffeur\MessageApiController::class, 'store']);
        Route::get('/messages/{id}', [\App\Http\Controllers\Api\Chauffeur\MessageApiController::class, 'show']);
    });
});

// ============================================================================
// HOTESSE API ROUTES
// ============================================================================

Route::prefix('hotesse')->group(function () {
    
    // Routes publiques (sans authentification)
    Route::post('/login', [\App\Http\Controllers\Api\Hotesse\HotesseAuthController::class, 'login']);
    Route::post('/verify-otp', [\App\Http\Controllers\Api\Hotesse\HotesseAuthController::class, 'verifyOtp']);
    Route::post('/resend-otp', [\App\Http\Controllers\Api\Hotesse\HotesseAuthController::class, 'resendOtp']);
    
    // Setup Password requires a specific ability 'setup-password' issued by verifyOtp
    Route::middleware('auth:sanctum')->post('/setup-password', [\App\Http\Controllers\Api\Hotesse\HotesseAuthController::class, 'setupPassword']);
    
    // Mot de passe oublié
    Route::post('/password/send-otp', [\App\Http\Controllers\Api\Hotesse\PasswordResetController::class, 'sendOtp']);
    Route::post('/password/verify-otp', [\App\Http\Controllers\Api\Hotesse\PasswordResetController::class, 'verifyOtp']);
    Route::post('/password/reset', [\App\Http\Controllers\Api\Hotesse\PasswordResetController::class, 'resetPassword']);
    
    // Routes protégées (authentification requise)
    Route::middleware('auth:sanctum')->group(function () {
        // Authentification
        Route::post('/logout', [\App\Http\Controllers\Api\Hotesse\HotesseAuthController::class, 'logout']);
        
        // Profil
        Route::get('/profile', [\App\Http\Controllers\Api\Hotesse\HotesseController::class, 'profile']);
        Route::post('/profile', [\App\Http\Controllers\Api\Hotesse\HotesseController::class, 'updateProfile']); // Using POST for file uploads
        Route::post('/change-password', [\App\Http\Controllers\Api\Hotesse\HotesseController::class, 'updatePassword']);
        
        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Api\Hotesse\HotesseController::class, 'dashboard']);
        
        // Ventes et Billetterie
        Route::get('/ventes', [\App\Http\Controllers\Api\Hotesse\HotesseController::class, 'ventes']);
        Route::get('/vendre-ticket', [\App\Http\Controllers\Api\Hotesse\HotesseController::class, 'vendreTicket']);
        Route::post('/vendre-ticket', [\App\Http\Controllers\Api\Hotesse\HotesseController::class, 'vendreTicketSubmit']);
        
        // Détails réservation (par ex. pour imprimer)
        Route::get('/reservations/{id}', [\App\Http\Controllers\Api\Hotesse\HotesseController::class, 'showReservation']);
    });
});

// ============================================================================
// CAISSE API ROUTES
// ============================================================================
Route::prefix('caisse')->group(function () {
    // Routes publiques
    Route::post('/login', [\App\Http\Controllers\Api\Caisse\AuthController::class, 'login']);
    
    // Mot de passe oublié
    Route::post('/password/send-otp', [\App\Http\Controllers\Api\Caisse\PasswordResetController::class, 'sendOtp']);
    Route::post('/password/verify-otp', [\App\Http\Controllers\Api\Caisse\PasswordResetController::class, 'verifyOtp']);
    Route::post('/password/reset', [\App\Http\Controllers\Api\Caisse\PasswordResetController::class, 'resetPassword']);

    // Routes protégées
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [\App\Http\Controllers\Api\Caisse\AuthController::class, 'logout']);
    });
});

// ============================================================================
// PUBLIC ROUTES (sans authentification - accessible depuis l'app)
// ============================================================================

Route::prefix('public')->group(function () {
    // Signalement d'accident anonyme (pas besoin d'être connecté)
    Route::post('/signalement-accident', [PublicSignalementController::class, 'store']);
});

// ============================================================================
// ROUTE DE TEST
// ============================================================================

Route::get('/ping', function () {
    return response()->json([
        'success' => true,
        'message' => 'API Car225 Mobile is running',
        'timestamp' => now()->toISOString(),
    ]);
});// Webhooks (routes publiques)
Route::post('/user/payment/notify', [UserReservationController::class, 'handlePaymentNotification']);
Route::post('/user/wallet/notify', [WalletController::class, 'notify']);
