<?php

use App\Http\Controllers\Api\User\AuthController as UserAuthController;
use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\Api\User\ReservationApiController as UserReservationController;
use App\Http\Controllers\Api\User\WalletController;
use App\Http\Controllers\Api\User\CompagnieController;
use App\Http\Controllers\Api\Agent\AuthController as AgentAuthController;
use App\Http\Controllers\Api\Agent\AgentController;
use App\Http\Controllers\Api\Agent\ReservationApiController as AgentReservationController;
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
// USER API ROUTES
// ============================================================================

Route::prefix('user')->group(function () {
    
    // Routes publiques (sans authentification)
    Route::post('/login', [UserAuthController::class, 'login']);
    Route::post('/register', [UserAuthController::class, 'register']);
    
    // Mot de passe oublié
    Route::post('/password/send-otp', [UserAuthController::class, 'sendOtp']);
    Route::post('/password/verify-otp', [UserAuthController::class, 'verifyOtp']);
    Route::post('/password/reset', [UserAuthController::class, 'resetPassword']);
    
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
        
        // Dashboard
        Route::get('/dashboard', [UserController::class, 'dashboard']);
        
        // Réservations
        Route::get('/reservations', [UserReservationController::class, 'index']);
        Route::post('/reservations', [UserReservationController::class, 'store']);
        Route::get('/reservations/{reservation}', [UserReservationController::class, 'show']);
        Route::delete('/reservations/{reservation}', [UserReservationController::class, 'cancel']);
        Route::get('/reservations/{reservation}/ticket', [UserReservationController::class, 'ticket']);
        Route::get('/reservations/{reservation}/download', [UserReservationController::class, 'download']);
        
        // Vérification du statut de paiement CinetPay (polling mobile)
        Route::get('/payment/status/{transactionId}', [UserReservationController::class, 'getPaymentStatus']);
        Route::post('/payment/verify/{transactionId}', [UserReservationController::class, 'verifyAndConfirmPayment']);
        
        // Portefeuille (Wallet)
        Route::get('/wallet', [WalletController::class, 'index']);
        Route::post('/wallet/recharge', [WalletController::class, 'recharge']);
        Route::post('/wallet/verify', [WalletController::class, 'verify']);
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
        
        // Véhicules et Programmes
        Route::get('/vehicles', [AgentReservationController::class, 'getVehicles']);
        Route::get('/programmes/today', [AgentReservationController::class, 'getProgrammesForScan']);
        Route::get('/programmes/{programmeId}/passengers', [AgentReservationController::class, 'getReservationsForProgramme']);
        
        // Détails réservation
        Route::get('/reservations/{reservationId}', [AgentReservationController::class, 'showReservation']);
    });
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
