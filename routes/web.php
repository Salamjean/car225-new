<?php

use App\Http\Controllers\Admin\AdminDashboard;
use App\Http\Controllers\Admin\AuthenticateAdmin;
use App\Http\Controllers\Admin\Itineraire\AdminItineraireController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Agent\AgentController;
use App\Http\Controllers\Agent\AgentDashboard;
use App\Http\Controllers\Agent\AuthenticateAgent;
use App\Http\Controllers\Agent\ReservationController as AgentReservationController;
use App\Http\Controllers\Agent\VoyageController as AgentVoyageController;
use App\Http\Controllers\User\WalletController;
use App\Http\Controllers\Compagnie\Agent\AgentCompagnieController;
use App\Http\Controllers\Compagnie\GareController;
use App\Http\Controllers\Compagnie\CompagnieAuthenticate;
use Illuminate\Http\Request;
use App\Http\Controllers\Compagnie\CompagnieController;
use App\Http\Controllers\Compagnie\CompagnieDashboard;
use App\Http\Controllers\Compagnie\Itineraire\ItineraireController;
use App\Http\Controllers\Compagnie\Personnel\PersonnelController;
use App\Http\Controllers\Compagnie\Programme\ProgrammeController;
use App\Http\Controllers\Compagnie\ReservationController as CompagnieReservationController;
use App\Http\Controllers\Compagnie\Vehicule\VehiculeController;
use App\Http\Controllers\Compagnie\SignalementController as CompagnieSignalementController;
use App\Http\Controllers\Home\AccueilController;
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\Compagnie\CompagniePasswordResetController;
use App\Http\Controllers\User\PasswordResetController;
use App\Http\Controllers\User\ConvoiController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\Reservation\ReservationController;
use App\Http\Controllers\User\UserAuthenticate;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Agent\PasswordResetController as AgentPasswordResetController;
use App\Http\Controllers\Caisse\PasswordResetController as CaissePasswordResetController;
use App\Http\Controllers\Hotesse\PasswordResetController as HotessePasswordResetController;
use App\Models\Vehicule;
use App\Models\Paiement;
use App\Http\Controllers\SignalementController;
use App\Http\Controllers\SapeurPompier\SapeurPompierController;
use App\Http\Controllers\SapeurPompier\SapeurPompierAuthenticate;
use App\Models\Signalement;
use App\Http\Controllers\Chauffeur\ChauffeurController;
use App\Http\Controllers\Chauffeur\ChauffeurAuthenticate;
use App\Http\Controllers\Chauffeur\VoyageController as ChauffeurVoyageController;
use App\Http\Controllers\Chauffeur\ChauffeurReservationController;
use App\Http\Controllers\PortailAuthController;
use App\Http\Controllers\Portail\PortailPasswordResetController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Routes Google Auth (Accessibles directement à la racine /auth/...)
Route::get('/auth/google', [UserAuthenticate::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [UserAuthenticate::class, 'handleGoogleCallback'])->name('auth.google.callback');

// ── Routes publiques — formulaire passagers convoi (lien envoyé par SMS/email) ──
Route::prefix('convoi/passagers')->name('public.convoi.passagers.')->group(function () {
    Route::get('/{token}', [\App\Http\Controllers\PublicConvoiPassagerController::class, 'show'])->name('form');
    Route::post('/{token}', [\App\Http\Controllers\PublicConvoiPassagerController::class, 'store'])->name('store');
    Route::get('/{token}/merci', [\App\Http\Controllers\PublicConvoiPassagerController::class, 'confirmation'])->name('confirmation');
});

//Les routes de la pages
Route::prefix('/')->group(function () {
    Route::get('/', [HomeController::class, 'home'])->name('home');
    Route::get('/itineraires/search', [HomeController::class, 'search'])->name('programmes.search');
    Route::get('/api/locations', [HomeController::class, 'getLocations'])->name('api.locations');
    Route::get('/itineraires', [HomeController::class, 'all'])->name('programmes.all');
    Route::get('/itineraires/{itineraire}', [HomeController::class, 'show'])->name('programmes.show');
    Route::get('/vehicule/details/{id}', [HomeController::class, 'getVehicleDetails'])->name('home.vehicle.details');

    //Les routes de la pages d'accueil
    Route::get('/home/about', [AccueilController::class, 'about'])->name('home.about');
    Route::get('/home/destination', [AccueilController::class, 'destination'])->name('home.destination');
    Route::get('/home/compagny', [AccueilController::class, 'compagny'])->name('home.compagny');
    Route::get('/home/infos', [AccueilController::class, 'infos'])->name('home.infos');
    Route::get('/home/services', [AccueilController::class, 'services'])->name('home.services');
    Route::get('/home/contact', [AccueilController::class, 'contact'])->name('home.contact');
    Route::get('/home/privacy', [AccueilController::class, 'privacy'])->name('home.privacy');
    Route::get('/home/deletion', [AccueilController::class, 'deletion'])->name('home.deletion');
    Route::get('/home/signaler-probleme', [AccueilController::class, 'signaler'])->name('home.signaler');
    Route::post('/home/signaler-probleme', [AccueilController::class, 'storeSignaler'])->name('home.signaler.store');
    Route::get('/home/mes-reservations', [AccueilController::class, 'mesReservations'])->name('home.reservations');
    Route::get('/home/mes-reservations/download/{reservation}', [AccueilController::class, 'downloadTicket'])->name('home.reservations.download');
    Route::post('/home/contact/store', [AccueilController::class, 'storeContact'])->name('home.contact.store');
    Route::get('/download-app', [AccueilController::class, 'downloadApp'])->name('home.download-app');
});

// Route de Connexion Unifiée
Route::prefix('portail')->group(function () {
    Route::get('/login', [PortailAuthController::class, 'showLogin'])->name('portail.login');
    Route::post('/login', [PortailAuthController::class, 'login'])->name('portail.login.submit');

    // Password Reset
    Route::get('/password/reset', [PortailPasswordResetController::class, 'showLinkRequestForm'])->name('portail.password.request');
    Route::post('/password/send-otp', [PortailPasswordResetController::class, 'sendOtpCode'])->name('portail.password.sendOtp');
    Route::post('/password/verify-otp', [PortailPasswordResetController::class, 'verifyOtpCode'])->name('portail.password.verifyOtp');
    Route::post('/password/reset', [PortailPasswordResetController::class, 'reset'])->name('portail.password.reset');
});

//Les routes de gestion du @admin
Route::prefix('admin')->group(function () {
    Route::get('/login', [AuthenticateAdmin::class, 'login'])->name('admin.login');
    Route::post('/', [AuthenticateAdmin::class, 'handleLogin'])->name('admin.handleLogin');
});

Route::middleware('admin')->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/logout', [AdminDashboard::class, 'logout'])->name('admin.logout');

    // Revenus Tickets détaillés (Placé en haut pour priorité)
    Route::get('/revenus-tickets', [AdminDashboard::class, 'revenusTickets'])->name('admin.revenus.tickets');

    //Les routes pour creer une compagnie 
    Route::prefix('company')->group(function () {
        Route::get('/index', [CompagnieController::class, 'index'])->name('compagnie.index');
        Route::get('/create', [CompagnieController::class, 'create'])->name('compagnie.create');
        Route::post('/create', [CompagnieController::class, 'store'])->name('compagnie.store');
        Route::get('/recharge', [CompagnieController::class, 'rechargeIndex'])->name('compagnie.recharge.index');
        Route::post('/recharge/{compagnie}', [CompagnieController::class, 'processRecharge'])->name('compagnie.recharge.process');
        Route::get('/{compagnie}', [CompagnieController::class, 'show'])->name('compagnie.show');
        Route::get('/{compagnie}/edit', [CompagnieController::class, 'edit'])->name('compagnie.edit');
        Route::put('/{compagnie}', [CompagnieController::class, 'update'])->name('compagnie.update');
        Route::delete('/{compagnie}', [CompagnieController::class, 'destroy'])->name('compagnie.destroy');
    });

    //Les routes pour voir les itineraires
    Route::get('/indeItinery', [AdminItineraireController::class, 'index'])->name('admin.itineraire.index');

    // Voyages en cours (temps réel)
    Route::get('/voyages-en-cours', [AdminSettingController::class, 'voyagesEnCours'])->name('admin.voyages.en-cours');
    Route::get('/voyages-en-cours/api', [AdminSettingController::class, 'voyagesEnCoursApi'])->name('admin.voyages.en-cours.api');

    // Gestion des Sapeurs Pompiers
    Route::resource('sapeur-pompier', SapeurPompierController::class);

    // Gestion des Hotesses
    Route::prefix('hotesse')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\HotesseController::class, 'index'])->name('admin.hotesse.index');
        Route::get('/create', [App\Http\Controllers\Admin\HotesseController::class, 'create'])->name('admin.hotesse.create');
        Route::post('/store', [App\Http\Controllers\Admin\HotesseController::class, 'store'])->name('admin.hotesse.store');
        Route::get('/{hotesse}', [App\Http\Controllers\Admin\HotesseController::class, 'show'])->name('admin.hotesse.show');
        Route::post('/{hotesse}/recharge', [App\Http\Controllers\Admin\HotesseController::class, 'recharge'])->name('admin.hotesse.recharge');
        Route::post('/{hotesse}/toggle-archive', [App\Http\Controllers\Admin\HotesseController::class, 'toggleArchive'])->name('admin.hotesse.toggle-archive');
        Route::delete('/{hotesse}', [App\Http\Controllers\Admin\HotesseController::class, 'destroy'])->name('admin.hotesse.destroy');
    });

    // Gestion des Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('admin.notifications.index');
        Route::post('/send', [App\Http\Controllers\Admin\NotificationController::class, 'send'])->name('admin.notifications.send');
        Route::get('/search-users', [App\Http\Controllers\Admin\NotificationController::class, 'searchUsers'])->name('admin.notifications.search');
    });

    // Gestion du Support Client
    Route::prefix('support')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\SupportController::class, 'index'])->name('admin.support.index');
        Route::get('/{supportRequest}', [App\Http\Controllers\Admin\SupportController::class, 'show'])->name('admin.support.show');
        Route::post('/{supportRequest}/repondre', [App\Http\Controllers\Admin\SupportController::class, 'repondre'])->name('admin.support.repondre');
        Route::patch('/{supportRequest}/statut', [App\Http\Controllers\Admin\SupportController::class, 'changeStatut'])->name('admin.support.statut');
    });



    // Gestion des Paramètres
    Route::prefix('settings')->group(function () {
        Route::get('/', [AdminSettingController::class, 'index'])->name('admin.settings.index');
        Route::post('/update', [AdminSettingController::class, 'update'])->name('admin.settings.update');
    });
});

//Les routes de gestion du @admin
Route::prefix('company')->group(function () {
    Route::get('/login', [CompagnieAuthenticate::class, 'login'])->name('compagnie.login');
    Route::post('/', [CompagnieAuthenticate::class, 'handleLogin'])->name('compagnie.handleLogin');

    // Compagnie Password Reset Routes
    Route::get('/password/reset', [CompagniePasswordResetController::class, 'showResetForm'])->name('compagnie.password.request');
    Route::post('/password/send-otp', [CompagniePasswordResetController::class, 'sendOtp'])->name('compagnie.password.sendOtp');
    Route::post('/password/verify-otp', [CompagniePasswordResetController::class, 'verifyOtp'])->name('compagnie.password.verifyOtp');
    Route::post('/password/reset', [CompagniePasswordResetController::class, 'resetPassword'])->name('compagnie.password.reset');
});

Route::middleware('compagnie')->prefix('company')->group(function () {
    Route::get('/dashboard', [CompagnieDashboard::class, 'dashboard'])->name('compagnie.dashboard');
    Route::post('/tickets/add', [CompagnieDashboard::class, 'addTickets'])->name('compagnie.tickets.add');
    Route::get('/logout', [CompagnieDashboard::class, 'logout'])->name('compagnie.logout');

    //les routes pour la gestion des agents 
    Route::prefix('agent')->group(function () {
        Route::get('/Plist', [AgentController::class, 'index'])->name('compagnie.agents.index');
        Route::get('/AgentAdd', [AgentController::class, 'create'])->name('compagnie.agents.create');
        Route::post('/store', [AgentController::class, 'store'])->name('compagnie.agents.store');
        Route::get('/{agent}/edit', [AgentController::class, 'edit'])->name('compagnie.agents.edit');
        Route::put('/{agent}', [AgentController::class, 'update'])->name('compagnie.agents.update');
        Route::delete('/{agent}', [AgentController::class, 'destroy'])->name('compagnie.agents.destroy');
        Route::post('/send-message', [AgentController::class, 'sendMessage'])->name('compagnie.agents.send-message');
    });

    //Les routes pour la gestion des itineraires 
    Route::prefix('Itinerary')->group(function () {
        Route::get('/ItAll', [ItineraireController::class, 'index'])->name('itineraire.index');
        Route::get('/create', [ItineraireController::class, 'create'])->name('itineraire.create');
        Route::post('/create', [ItineraireController::class, 'store'])->name('itineraire.store');
        Route::get('/{itineraire}', [ItineraireController::class, 'show'])->name('itineraire.show');
        Route::get('/{itineraire}/edit', [ItineraireController::class, 'edit'])->name('itineraire.edit');
        Route::put('/{itineraire}', [ItineraireController::class, 'update'])->name('itineraire.update');
        Route::delete('/{itineraire}', [ItineraireController::class, 'destroy'])->name('itineraire.destroy');
    });

    //Les routes de gestion des vehicules
    Route::prefix('car')->group(function () {
        Route::get('/carindex', [VehiculeController::class, 'index'])->name('vehicule.index');
        Route::get('/carAdd', [VehiculeController::class, 'create'])->name('vehicule.create');
        Route::post('/create', [VehiculeController::class, 'store'])->name('vehicule.store');
        Route::get('/{vehicule}/edit', [VehiculeController::class, 'edit'])->name('vehicule.edit');
        Route::put('/{vehicule}', [VehiculeController::class, 'update'])->name('vehicule.update');
        Route::patch('/{vehicule}/activate', [VehiculeController::class, 'activate'])->name('vehicule.activate');
        Route::patch('/{vehicule}/deactivate', [VehiculeController::class, 'deactivate'])->name('vehicule.deactivate');
        Route::delete('/{vehicule}', [VehiculeController::class, 'destroy'])->name('vehicule.destroy');
    });

    //Les routes de gestion chauffeurs
    Route::prefix('personal')->group(function () {
        Route::get('/listps', [PersonnelController::class, 'index'])->name('personnel.index');
        Route::get('/addpersonal', [PersonnelController::class, 'create'])->name('personnel.create');
        Route::post('/create', [PersonnelController::class, 'store'])->name('personnel.store');
        Route::get('/personnels/{personnel}/api', [PersonnelController::class, 'showApi'])->name('personnels.api.show');
        Route::get('/{personnel}/edit', [PersonnelController::class, 'edit'])->name('personnels.edit');
        Route::put('/{personnel}', [PersonnelController::class, 'update'])->name('personnels.update');
        Route::delete('/{personnel}', [PersonnelController::class, 'destroy'])->name('personnels.destroy');
    });

    //Les routes de gestion des caissières
    Route::prefix('caisse')->group(function () {
        Route::get('/index', [App\Http\Controllers\Compagnie\CaisseController::class, 'index'])->name('compagnie.caisse.index');
        Route::get('/create', [App\Http\Controllers\Compagnie\CaisseController::class, 'create'])->name('compagnie.caisse.create');
        Route::post('/store', [App\Http\Controllers\Compagnie\CaisseController::class, 'store'])->name('compagnie.caisse.store');
        Route::get('/{caisse}/edit', [App\Http\Controllers\Compagnie\CaisseController::class, 'edit'])->name('compagnie.caisse.edit');
        Route::put('/{caisse}', [App\Http\Controllers\Compagnie\CaisseController::class, 'update'])->name('compagnie.caisse.update');
        Route::post('/{caisse}/recharge', [App\Http\Controllers\Compagnie\CaisseController::class, 'recharge'])->name('compagnie.caisse.recharge');
        Route::post('/{caisse}/toggle-archive', [App\Http\Controllers\Compagnie\CaisseController::class, 'toggleArchive'])->name('compagnie.caisse.toggle-archive');
        Route::delete('/{caisse}', [App\Http\Controllers\Compagnie\CaisseController::class, 'destroy'])->name('compagnie.caisse.destroy');
    });

    //Les routes de programmation 
    Route::prefix('programme')->group(function () {
        Route::get('/prgramme', [ProgrammeController::class, 'index'])->name('programme.index');
        Route::get('/hsi', [ProgrammeController::class, 'history'])->name('programme.history');
        Route::get('/addPro', [ProgrammeController::class, 'create'])->name('programme.create');
        Route::post('/createPro', [ProgrammeController::class, 'store'])->name('programme.store');
        Route::get('/{programme}/api', [ProgrammeController::class, 'showApi'])->name('api.show');
        Route::get('/{programme}/edit', [ProgrammeController::class, 'edit'])->name('programme.edit');
        Route::match(['put', 'patch'], '/{programme}', [ProgrammeController::class, 'update'])->name('programme.update');
        Route::delete('/{programme}', [ProgrammeController::class, 'destroy'])->name('programme.destroy');
    });

    // Dans le groupe programme
    Route::get('/programme/{programme}/chauffeurs-disponibles', [ProgrammeController::class, 'chauffeursDisponibles'])->name('programme.chauffeurs-disponibles');
    Route::get('/programme/{programme}/vehicules-disponibles', [ProgrammeController::class, 'vehiculesDisponibles'])->name('programme.vehicules-disponibles');
    Route::patch('/programme/{programme}/changer-chauffeur', [ProgrammeController::class, 'changerChauffeur'])->name('programme.changer-chauffeur');
    Route::patch('/programme/{programme}/changer-vehicule', [ProgrammeController::class, 'changerVehicule'])->name('programme.changer-vehicule');

    //Les routes de gestion des réservations
    Route::prefix('booking')->group(function () {
        Route::get('/all', [CompagnieReservationController::class, 'index'])->name('company.reservation.index');
        Route::get('/by-date/{date}', [CompagnieReservationController::class, 'byDate'])->name('company.reservation.by_date');
        Route::get('/by-month/{month}', [CompagnieReservationController::class, 'byMonth'])->name('company.reservation.by_month');
        Route::get('/details', [CompagnieReservationController::class, 'details'])->name('company.reservation.details');
        Route::get('/reservations/{reservation}', [CompagnieReservationController::class, 'show'])->name('company.reservation.show');
        Route::get('/occupied-seats', [CompagnieReservationController::class, 'getOccupiedSeats'])->name('company.reservation.occupied-seats');
        Route::get('/programme-vehicle', [CompagnieReservationController::class, 'getProgrammeVehicle'])->name('company.reservation.programme-vehicle');
        Route::get('/reservations-by-date', [CompagnieReservationController::class, 'getReservationsByDate'])->name('company.reservation.reservations-by-date');
    });

    // Convois reçus par la compagnie
    Route::prefix('convois')->name('compagnie.convois.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Compagnie\ConvoiController::class, 'index'])->name('index');
        Route::get('/{convoi}', [\App\Http\Controllers\Compagnie\ConvoiController::class, 'show'])->name('show');
        Route::get('/{convoi}/location', [\App\Http\Controllers\Compagnie\ConvoiController::class, 'location'])->name('location');
        Route::post('/{convoi}/valider', [\App\Http\Controllers\Compagnie\ConvoiController::class, 'valider'])->name('valider');
        Route::post('/{convoi}/refuser', [\App\Http\Controllers\Compagnie\ConvoiController::class, 'refuser'])->name('refuser');
        Route::post('/{convoi}/assigner-gare', [\App\Http\Controllers\Compagnie\ConvoiController::class, 'assignerGare'])->name('assigner-gare');
        Route::post('/{convoi}/annuler', [\App\Http\Controllers\Compagnie\ConvoiController::class, 'annuler'])->name('annuler');
    });

    // Routes de gestion des signalements pour la compagnie
    Route::prefix('signalements')->group(function () {
        Route::get('/', [CompagnieSignalementController::class, 'index'])->name('compagnie.signalements.index');
        Route::get('/{id}', [CompagnieSignalementController::class, 'show'])->name('compagnie.signalements.show');
        Route::post('/{id}/alert-gare', [CompagnieSignalementController::class, 'alertGare'])->name('compagnie.signalements.alert-gare');
        Route::post('/{id}/alert-pompier', [CompagnieSignalementController::class, 'alertPompier'])->name('compagnie.signalements.alert-pompier');
        Route::patch('/{id}/mark-traite', [CompagnieSignalementController::class, 'markAsTraite'])->name('compagnie.signalements.mark-traite');
        Route::patch('/{id}/mark-read', [CompagnieSignalementController::class, 'markAsRead'])->name('compagnie.signalements.mark-read');

        // Actions de gestion de voyage
        Route::post('/{id}/interrupt', [CompagnieSignalementController::class, 'interruptVoyage'])->name('compagnie.signalements.interrupt');
        Route::post('/{id}/resume', [CompagnieSignalementController::class, 'resumeVoyage'])->name('compagnie.signalements.resume');
        Route::post('/{id}/transbordement', [CompagnieSignalementController::class, 'transbordement'])->name('compagnie.signalements.transbordement');

        // Notification accident — contacts urgence des passagers évacués
        Route::get('/{id}/notification-accident', [CompagnieSignalementController::class, 'notificationAccident'])->name('compagnie.signalement.notification-accident');
        Route::post('/{id}/envoyer-notifications', [CompagnieSignalementController::class, 'envoyerNotifications'])->name('compagnie.signalement.envoyer-notifications');
    });

    // Routes de gestion des gares
    Route::prefix('gare')->group(function () {
        Route::get('/', [GareController::class, 'index'])->name('gare.index');
        Route::get('/create', [GareController::class, 'create'])->name('gare.create');
        Route::post('/store', [GareController::class, 'store'])->name('gare.store');
        Route::get('/{gare}/edit', [GareController::class, 'edit'])->name('gare.edit');
        Route::put('/{gare}', [GareController::class, 'update'])->name('gare.update');
        Route::delete('/{gare}', [GareController::class, 'destroy'])->name('gare.destroy');
    });

    // Routes de profil de la compagnie
    Route::get('/profile', [CompagnieDashboard::class, 'profile'])->name('compagnie.profile');
    Route::post('/profile/update', [CompagnieDashboard::class, 'updateProfile'])->name('compagnie.profile.update');
    Route::post('/profile/password', [CompagnieDashboard::class, 'updatePassword'])->name('compagnie.profile.password');

    // Routes de messagerie
    Route::prefix('messages')->name('compagnie.messages.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Compagnie\CompanyMessageController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Compagnie\CompanyMessageController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Compagnie\CompanyMessageController::class, 'store'])->name('store');
        Route::get('/recipients', [\App\Http\Controllers\Compagnie\CompanyMessageController::class, 'getRecipients'])->name('recipients');
        Route::get('/received/{id}', [\App\Http\Controllers\Compagnie\CompanyMessageController::class, 'showReceived'])->name('show-received');
        Route::get('/{message}', [\App\Http\Controllers\Compagnie\CompanyMessageController::class, 'show'])->name('show');
    });

    // Routes de suivi GPS en temps réel
    Route::prefix('tracking')->group(function () {
        Route::get('/', [\App\Http\Controllers\Compagnie\TrackingController::class, 'index'])->name('compagnie.tracking.index');
        Route::get('/locations', [\App\Http\Controllers\Compagnie\TrackingController::class, 'getActiveLocations'])->name('compagnie.tracking.locations');
    });

    // Demandes de localisation GPS des gares
    Route::prefix('gare-location-requests')->name('compagnie.gare-location-requests.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Compagnie\GareLocationRequestController::class, 'index'])->name('index');
        Route::post('/{gareLocationRequest}/approve', [\App\Http\Controllers\Compagnie\GareLocationRequestController::class, 'approve'])->name('approve');
        Route::post('/{gareLocationRequest}/reject', [\App\Http\Controllers\Compagnie\GareLocationRequestController::class, 'reject'])->name('reject');
        Route::post('/gare/{gare}/update-location', [\App\Http\Controllers\Compagnie\GareLocationRequestController::class, 'updateGareLocation'])->name('updateGareLocation');
    });
});

//Les routes de gestion des @caissières
Route::prefix('caisse')->group(function () {
    // Public routes (before authentication)
    Route::get('/verify-otp', [App\Http\Controllers\Caisse\CaisseAuthController::class, 'showOtpVerification'])->name('caisse.auth.verify-otp');
    Route::post('/verify-otp', [App\Http\Controllers\Caisse\CaisseAuthController::class, 'verifyOtp'])->name('caisse.auth.verify-otp.submit');
    Route::post('/resend-otp', [App\Http\Controllers\Caisse\CaisseAuthController::class, 'resendOtp'])->name('caisse.auth.resend-otp');

    Route::get('/setup-password', [App\Http\Controllers\Caisse\CaisseAuthController::class, 'showPasswordSetup'])->name('caisse.auth.setup-password');
    Route::post('/setup-password', [App\Http\Controllers\Caisse\CaisseAuthController::class, 'setupPassword'])->name('caisse.auth.setup-password.submit');

    Route::get('/login', [App\Http\Controllers\Caisse\CaisseAuthController::class, 'showLogin'])->name('caisse.auth.login');
    Route::post('/login', [App\Http\Controllers\Caisse\CaisseAuthController::class, 'login'])->name('caisse.auth.login.submit');

    // Caisse Password Reset Routes
    Route::get('/password/reset', [CaissePasswordResetController::class, 'showResetForm'])->name('caisse.password.request');
    Route::post('/password/send-otp', [CaissePasswordResetController::class, 'sendOtp'])->name('caisse.password.sendOtp');
    Route::post('/password/verify-otp', [CaissePasswordResetController::class, 'verifyOtp'])->name('caisse.password.verifyOtp');
    Route::post('/password/reset', [CaissePasswordResetController::class, 'resetPassword'])->name('caisse.password.reset');
});

Route::middleware('caisse')->prefix('caisse')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Caisse\CaisseController::class, 'dashboard'])->name('caisse.dashboard');
    Route::get('/logout', [App\Http\Controllers\Caisse\CaisseAuthController::class, 'logout'])->name('caisse.logout');

    // Profile routes
    Route::get('/profile', [App\Http\Controllers\Caisse\CaisseController::class, 'profile'])->name('caisse.profile');
    Route::post('/profile/update', [App\Http\Controllers\Caisse\CaisseController::class, 'updateProfile'])->name('caisse.profile.update');
    Route::post('/profile/password', [App\Http\Controllers\Caisse\CaisseController::class, 'updatePassword'])->name('caisse.profile.password');

    // Ticket selling routes
    Route::get('/vendre-ticket', [App\Http\Controllers\Caisse\CaisseController::class, 'vendreTicket'])->name('caisse.vendre-ticket');
    Route::post('/vendre-ticket', [App\Http\Controllers\Caisse\CaisseController::class, 'vendreTicketSubmit'])->name('caisse.vendre-ticket.submit');
    Route::get('/vente', [App\Http\Controllers\Caisse\CaisseController::class, 'vente'])->name('caisse.vente');
    Route::post('/vente', [App\Http\Controllers\Caisse\CaisseController::class, 'venteSubmit'])->name('caisse.vente.submit');
    Route::get('/vente-success', [App\Http\Controllers\Caisse\CaisseController::class, 'venteSuccess'])->name('caisse.vente-success');

    // API Route for Vehicle Details (Shared Logic)
    Route::get('/api/vehicle/{id}', [App\Http\Controllers\User\Reservation\ReservationController::class, 'showVehicle'])->name('caisse.api.vehicle');

    // Sales history and printing
    Route::get('/ventes', [App\Http\Controllers\Caisse\CaisseController::class, 'ventes'])->name('caisse.ventes');
    Route::get('/ticket/{reservation}/imprimer', [App\Http\Controllers\Caisse\CaisseController::class, 'imprimerTicket'])->name('caisse.ticket.imprimer');

    // Inbox for Caisse
    Route::prefix('messages')->name('caisse.messages.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Caisse\CaisseMessageController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\Caisse\CaisseMessageController::class, 'show'])->name('show');
    });
});

//Les routes de gestion des @hotesses
Route::prefix('hotesse')->group(function () {
    // Public routes (before authentication)
    Route::get('/verify-otp', [App\Http\Controllers\Hotesse\HotesseAuthController::class, 'showOtpVerification'])->name('hotesse.auth.verify-otp');
    Route::post('/verify-otp', [App\Http\Controllers\Hotesse\HotesseAuthController::class, 'verifyOtp'])->name('hotesse.auth.verify-otp.submit');
    Route::post('/resend-otp', [App\Http\Controllers\Hotesse\HotesseAuthController::class, 'resendOtp'])->name('hotesse.auth.resend-otp');

    Route::get('/setup-password', [App\Http\Controllers\Hotesse\HotesseAuthController::class, 'showPasswordSetup'])->name('hotesse.auth.setup-password');
    Route::post('/setup-password', [App\Http\Controllers\Hotesse\HotesseAuthController::class, 'setupPassword'])->name('hotesse.auth.setup-password.submit');

    Route::get('/login', [App\Http\Controllers\Hotesse\HotesseAuthController::class, 'showLogin'])->name('hotesse.auth.login');
    Route::post('/login', [App\Http\Controllers\Hotesse\HotesseAuthController::class, 'login'])->name('hotesse.auth.login.submit');

    // Hotesse Password Reset Routes
    Route::get('/password/reset', [HotessePasswordResetController::class, 'showResetForm'])->name('hotesse.password.request');
    Route::post('/password/send-otp', [HotessePasswordResetController::class, 'sendOtp'])->name('hotesse.password.sendOtp');
    Route::post('/password/verify-otp', [HotessePasswordResetController::class, 'verifyOtp'])->name('hotesse.password.verifyOtp');
    Route::post('/password/reset', [HotessePasswordResetController::class, 'resetPassword'])->name('hotesse.password.reset');
});

Route::middleware('hotesse')->prefix('hotesse')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Hotesse\HotesseController::class, 'dashboard'])->name('hotesse.dashboard');
    Route::get('/logout', [App\Http\Controllers\Hotesse\HotesseAuthController::class, 'logout'])->name('hotesse.logout');

    // Profile routes
    Route::get('/profile', [App\Http\Controllers\Hotesse\HotesseController::class, 'profile'])->name('hotesse.profile');
    Route::post('/profile/update', [App\Http\Controllers\Hotesse\HotesseController::class, 'updateProfile'])->name('hotesse.profile.update');
    Route::post('/profile/password', [App\Http\Controllers\Hotesse\HotesseController::class, 'updatePassword'])->name('hotesse.profile.password');

    // Ticket selling routes
    Route::get('/vendre-ticket', [App\Http\Controllers\Hotesse\HotesseController::class, 'vendreTicket'])->name('hotesse.vendre-ticket');
    Route::post('/vendre-ticket', [App\Http\Controllers\Hotesse\HotesseController::class, 'vendreTicketSubmit'])->name('hotesse.vendre-ticket.submit');
    Route::get('/vente-success', [App\Http\Controllers\Hotesse\HotesseController::class, 'venteSuccess'])->name('hotesse.vente-success');

    // Sales history and printing
    Route::get('/ventes', [App\Http\Controllers\Hotesse\HotesseController::class, 'ventes'])->name('hotesse.ventes');
    Route::get('/ticket/{reservation}/imprimer', [App\Http\Controllers\Hotesse\HotesseController::class, 'imprimerTicket'])->name('hotesse.ticket.imprimer');
    Route::get('/ticket/{reservation}/imprimer-thermal', [App\Http\Controllers\Hotesse\HotesseController::class, 'imprimerThermal'])->name('hotesse.ticket.thermal');

    // API Route for Return Trips (Shared Logic with User)
    Route::get('/api/return-trips', [App\Http\Controllers\User\Reservation\ReservationController::class, 'apiReturnTrips'])->name('hotesse.api.return-trips');
    Route::get('/api/vehicle/{id}', [App\Http\Controllers\User\Reservation\ReservationController::class, 'showVehicle'])->name('hotesse.api.vehicle');
});


//Les routes de gestion des @agents
Route::prefix('agent')->group(function () {
    Route::get('/login', [AuthenticateAgent::class, 'login'])->name('agent.login');
    Route::post('/login', [AuthenticateAgent::class, 'handleLogin'])->name('agent.handleLogin');

    // Agent Password Reset Routes
    Route::get('/password/reset', [AgentPasswordResetController::class, 'showResetForm'])->name('agent.password.request');
    Route::post('/password/send-otp', [AgentPasswordResetController::class, 'sendOtp'])->name('agent.password.sendOtp');
    Route::post('/password/verify-otp', [AgentPasswordResetController::class, 'verifyOtp'])->name('agent.password.verifyOtp');
    Route::post('/password/reset', [AgentPasswordResetController::class, 'resetPassword'])->name('agent.password.reset');
});
Route::middleware('agent')->prefix('agent')->name('agent.')->group(function () {
    Route::get('/dashboard', [AgentDashboard::class, 'dashboard'])->name('dashboard');
    Route::get('/logout', [AgentDashboard::class, 'logout'])->name('logout');

    // Profile routes
    Route::get('/profile', [AgentDashboard::class, 'profile'])->name('profile');
    Route::post('/profile/update', [AgentDashboard::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [AgentDashboard::class, 'updatePassword'])->name('profile.password');

    // Gestion des réservations
    Route::prefix('reservations')->name('reservations.')->group(function () {
        Route::get('/', [AgentReservationController::class, 'index'])->name('index');
        Route::get('/recherche', [AgentReservationController::class, 'recherchePage'])->name('recherche');
        Route::get('/historique', [AgentReservationController::class, 'historique'])->name('historique');
        Route::get('/programmes-for-scan', [AgentReservationController::class, 'getProgrammesForScan'])->name('programmes.for-scan');
        Route::post('/scan', [AgentReservationController::class, 'scan'])->name('scan');
        Route::post('/search', [AgentReservationController::class, 'search'])->name('search');
        Route::post('/search-by-reference', [AgentReservationController::class, 'searchByReference'])->name('search-by-reference');
        Route::post('/confirm', [AgentReservationController::class, 'confirm'])->name('confirm');
        // Route::post('/assign-voyage-manual', [AgentReservationController::class, 'assignVoyageManual'])->name('assign-voyage-manual');
    });

    /* Gestion des voyages supprimée
    Route::prefix('voyages')->name('voyages.')->group(function () {
        Route::get('/', [AgentVoyageController::class, 'index'])->name('index');
        Route::get('/history', [AgentVoyageController::class, 'history'])->name('history');
        Route::post('/', [AgentVoyageController::class, 'store'])->name('store');
        Route::delete('/{voyage}', [AgentVoyageController::class, 'destroy'])->name('destroy');
    }); */

    // Gestion des messages
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Agent\AgentMessageController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Agent\AgentMessageController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\Agent\AgentMessageController::class, 'show'])->name('show');
        Route::patch('/{id}/read', [\App\Http\Controllers\Agent\AgentMessageController::class, 'markAsRead'])->name('read');
    });
});


//Les routes de gestion du @user
Route::prefix('user')->group(function () {
    Route::get('/login', [UserAuthenticate::class, 'login'])->name('login');
    Route::post('/login', [UserAuthenticate::class, 'handleLogin'])->name('user.handleLogin');
    Route::get('/register', [UserAuthenticate::class, 'register'])->name('user.register');
    Route::post('/register', [UserAuthenticate::class, 'handleRegister'])->name('user.handleRegister');
    Route::get('/verify-otp', [UserAuthenticate::class, 'showVerifyOtp'])->name('user.verify-otp');
    Route::post('/verify-otp', [UserAuthenticate::class, 'handleVerifyOtp'])->name('user.verify-otp.submit');
    Route::post('/resend-otp', [UserAuthenticate::class, 'resendOtp'])->name('user.resend-otp');

    // Route publique pour le retour de paiement Wallet (Mobile & Web)
    Route::get('/wallet', [\App\Http\Controllers\User\WalletController::class, 'paymentResult'])->name('wallet.payment.result');

    // Password Reset Routes
    Route::get('/password/reset', [PasswordResetController::class, 'showResetForm'])->name('password.request');
    Route::post('/password/send-otp', [PasswordResetController::class, 'sendOtp'])->name('password.sendOtp');
    Route::post('/password/verify-otp', [PasswordResetController::class, 'verifyOtp'])->name('password.verifyOtp');
    Route::post('/password/reset', [PasswordResetController::class, 'resetPassword'])->name('password.reset');
});

Route::middleware('auth')->prefix('user')->group(function () {
    // Routes pour finaliser le profil (Accessibles même sans contact)
    Route::get('/complete-profile', [UserAuthenticate::class, 'showCompleteProfile'])->name('user.complete-profile');
    Route::post('/complete-profile', [UserAuthenticate::class, 'updateContact'])->name('user.update-contact');
    Route::get('/logout', [UserController::class, 'logout'])->name('user.logout');

    // Routes protégées qui nécessitent un numéro de téléphone
    Route::middleware('check_contact')->group(function () {
        Route::get('/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
        Route::get('/tracking/location', [UserController::class, 'getTrackingLocation'])->name('user.tracking.location');
        Route::get('/logout', [UserController::class, 'logout'])->name('user.logout');

        // Wallet Routes
        Route::get('/compte', [WalletController::class, 'index'])->name('user.wallet.index');
        Route::get('/compte/recharges', [WalletController::class, 'rechargeHistory'])->name('user.wallet.recharges');
        Route::post('/compte/recharge', [WalletController::class, 'recharge'])->name('user.wallet.recharge');
        Route::post('/compte/retrait', [WalletController::class, 'withdraw'])->name('user.wallet.withdraw');
        Route::post('/compte/verify', [WalletController::class, 'verifyRecharge'])->name('user.wallet.verify');
        Route::get('/compte/payment/success', [WalletController::class, 'paymentSuccess'])->name('user.wallet.payment.success');
        Route::get('/compte/payment/failed', [WalletController::class, 'paymentFailed'])->name('user.wallet.payment.failed');

        // Profile Routes
        Route::get('/profile', [ProfileController::class, 'index'])->name('user.profile');
        Route::post('/profile/request-update', [ProfileController::class, 'requestUpdate'])->name('user.profile.request_update');
        Route::post('/profile/update', [ProfileController::class, 'update'])->name('user.profile.update');
        Route::post('/profile/update-emergency', [ProfileController::class, 'updateEmergencyContact'])->name('user.profile.update-emergency');
        Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('user.profile.password');
        Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('user.profile.photo');


        //Les routes pour faire une reservation 
        Route::prefix('booking')->group(function () {
            Route::get('/allReserve', [ReservationController::class, 'index'])->name('reservation.index');
            Route::get('/group/{transaction_id}', [ReservationController::class, 'showGroup'])->name('user.reservation.group');
            Route::get('/reservation', [ReservationController::class, 'create'])->name('reservation.create');
            Route::get('/vehicle/{id}', [ReservationController::class, 'showVehicle'])->name('user.reservation.vehicle');
            Route::get('/program/{id}', [ReservationController::class, 'getProgram'])->name('user.reservation.program');
            Route::get('/program/{id}/default-vehicle', [ReservationController::class, 'getDefaultVehicle'])->name('user.reservation.default-vehicle');
            Route::get('/reservation/reserved-seats/{programId}', [ReservationController::class, 'getReservedSeats'])->name('user.reservation.reserved-seats');
            Route::post('/reservation', [ReservationController::class, 'store'])->name('reservation.store');
            Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
            Route::get('/reservations/{reservation}/download', [ReservationController::class, 'download'])->name('reservations.download');
            Route::get('/reservations/{reservation}/ticket', [ReservationController::class, 'ticket'])->name('reservations.ticket');
            Route::delete('/reservations/{reservation}', [ReservationController::class, 'cancel'])->name('reservations.cancel');
            Route::get('/reservations/{reservation}/refund-preview', [ReservationController::class, 'getRefundPreview'])->name('reservations.refund-preview');
            Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancelReservation'])->name('reservations.cancel-refund');
            Route::get('/reservations/{reservation}/modifier', [ReservationController::class, 'showModificationPage'])->name('reservations.modifier');
            Route::post('/reservations/{reservation}/modify', [ReservationController::class, 'processModification'])->name('reservations.modify');
            Route::get('/api/programmes', [ReservationController::class, 'apiProgrammes'])->name('api.programmes');
            Route::get('/api/grouped-routes', [ReservationController::class, 'apiGroupedRoutes'])->name('api.grouped-routes');
            Route::get('/api/route-dates', [ReservationController::class, 'apiRouteDates'])->name('api.route-dates');
            Route::get('/api/route-schedules', [ReservationController::class, 'apiRouteSchedules'])->name('api.route-schedules');
            Route::get('/api/return-trips', [ReservationController::class, 'apiReturnTrips'])->name('api.return-trips');

            // Modification API endpoints
            Route::get('/reservations/{reservation}/modification-data', [ReservationController::class, 'getModificationData'])->name('reservations.modification-data');
            Route::get('/programmes/{programme}/available-dates', [ReservationController::class, 'getAvailableDates'])->name('programmes.available-dates');
            Route::get('/programmes/{programme}/available-times', [ReservationController::class, 'getAvailableTimes'])->name('programmes.available-times');
            Route::get('/programmes/{programme}/seats', [ReservationController::class, 'getSeats'])->name('programmes.seats');
            Route::post('/reservations/{reservation}/calculate-delta', [ReservationController::class, 'calculateModificationDelta'])->name('reservations.calculate-delta');
        });
        Route::prefix('convoi')->name('user.convoi.')->group(function () {
            Route::get('/mes-convois', [ConvoiController::class, 'index'])->name('index');
            Route::get('/', [ConvoiController::class, 'create'])->name('create');
            Route::get('/compagnie/{compagnie}/itineraires', [ConvoiController::class, 'itinerairesByCompagnie'])->name('itineraires-by-compagnie');
            Route::get('/compagnie/{compagnie}/gares', [ConvoiController::class, 'garesByCompagnie'])->name('gares-by-compagnie');
            Route::post('/store', [ConvoiController::class, 'store'])->name('store');
            Route::get('/{convoi}', [ConvoiController::class, 'show'])->name('show');
            Route::get('/{convoi}/recu-pdf', [ConvoiController::class, 'downloadRecu'])->name('recu-pdf');
            Route::post('/{convoi}/pay', [ConvoiController::class, 'pay'])->name('pay');
            Route::post('/{convoi}/accepter', [ConvoiController::class, 'accepterMontant'])->name('accepter');
            Route::post('/{convoi}/refuser-montant', [ConvoiController::class, 'refuserMontant'])->name('refuser-montant');
            Route::get('/itineraire/{itineraire}/gares', [ConvoiController::class, 'garesByItineraire'])->name('gares-by-itineraire');
            Route::post('/{convoi}/passengers', [ConvoiController::class, 'storePassengers'])->name('store-passengers');
            Route::post('/{convoi}/lieu-rassemblement', [ConvoiController::class, 'storeLieuRassemblement'])->name('lieu-rassemblement');
        });
        Route::get('/programmes/{programme}/recalculate-status', [ReservationController::class, 'recalculateProgramStatus'])->name('programmes.recalculate-status');
        Route::post('/reservations/{reservation}/update-emergency', [ReservationController::class, 'updateEmergencyContact'])->name('user.reservation.update-emergency');
        Route::get('/programmes/{programme}/status-for-date/{date}', [ReservationController::class, 'getProgramStatusForDate'])->name('programmes.status-for-date');

        // Signalement de problèmes
        Route::prefix('signalement')->group(function () {
            Route::get('/create', [SignalementController::class, 'create'])->name('signalement.create');
            Route::post('/store', [SignalementController::class, 'store'])->name('signalement.store');
        });

        // Support Client
        Route::prefix('support')->group(function () {
            Route::get('/', [App\Http\Controllers\User\SupportController::class, 'index'])->name('user.support.index');
            Route::get('/mes-declarations', [App\Http\Controllers\User\SupportController::class, 'mesDeclarations'])->name('user.support.mes-declarations');
            Route::get('/create', [App\Http\Controllers\User\SupportController::class, 'create'])->name('user.support.create');
            Route::post('/store', [App\Http\Controllers\User\SupportController::class, 'store'])->name('user.support.store');
            Route::post('/{supportRequest}/repondre', [App\Http\Controllers\User\SupportController::class, 'repondre'])->name('user.support.repondre');
            Route::post('/{supportRequest}/mark-read', [App\Http\Controllers\User\SupportController::class, 'markAsRead'])->name('user.support.mark-read');
        });

        // Notifications
        Route::post('/notifications/mark-read', [UserController::class, 'markNotificationRead'])->name('user.notifications.mark-read');
        Route::post('/notifications/mark-all-read', [UserController::class, 'markAllNotificationsRead'])->name('user.notifications.mark-all-read');
        Route::get('/notifications/{id}/go', [UserController::class, 'goToNotification'])->name('user.notifications.go');
    });
});

// Paiement Wave (Hors Auth pour le webhook)
Route::prefix('user')->group(function () {
    Route::post('/payment/wave/notify', [App\Http\Controllers\PaymentController::class, 'waveNotify'])->name('payment.notify');
    Route::get('/payment/wave/return', [App\Http\Controllers\PaymentController::class, 'waveReturn'])->name('payment.return');
    Route::get('/payment/wave/cancel', [App\Http\Controllers\PaymentController::class, 'waveCancel'])->name('payment.cancel');

    // Wallet Wave
    Route::get('/compte/wave/return', [App\Http\Controllers\User\WalletController::class, 'waveReturn'])->name('wallet.wave.return');
    Route::get('/compte/wave/cancel', [App\Http\Controllers\User\WalletController::class, 'waveCancel'])->name('wallet.wave.cancel');

    // Route publique pour le retour de paiement Réservation (Mobile & Web)
    Route::get('/reservation/payment-result', [App\Http\Controllers\PaymentController::class, 'paymentResult'])->name('reservation.payment.result');

    Route::post('/compte/notify', [App\Http\Controllers\User\WalletController::class, 'notify'])->name('cinetpay.notify');
    Route::match(['get', 'post'], '/payment/notify/transfer', [App\Http\Controllers\User\WalletController::class, 'notifyTransfer'])->name('wallet.notify.transfer');
});


// Routes Sapeur Pompier (Interface dédiée)
Route::prefix('sapeur-pompier')->group(function () {
    Route::get('/login', [SapeurPompierAuthenticate::class, 'login'])->name('sapeur-pompier.login');
    Route::post('/login', [SapeurPompierAuthenticate::class, 'handleLogin']);
    Route::get('/define-access/{email}', [SapeurPompierAuthenticate::class, 'defineAccess'])->name('sapeur-pompier.define-access');
    Route::post('/define-access', [SapeurPompierAuthenticate::class, 'submitDefineAccess'])->name('sapeur-pompier.submit-define-access');
    Route::get('/logout', [SapeurPompierAuthenticate::class, 'logout'])->name('sapeur-pompier.logout');

    Route::middleware('sapeur_pompier')->group(function () {
        Route::get('/profile', [App\Http\Controllers\SapeurPompier\SapeurPompierDashboard::class, 'profile'])->name('sapeur-pompier.profile');
        Route::put('/profile', [App\Http\Controllers\SapeurPompier\SapeurPompierDashboard::class, 'updateProfile'])->name('sapeur-pompier.profile.update');

        Route::get('/dashboard', function (\Illuminate\Http\Request $request) {
            $query = Signalement::where('sapeur_pompier_id', Auth::guard('sapeur_pompier')->id())
                ->with(['user', 'personnel', 'compagnie', 'programme.compagnie']);

            if ($request->has('status')) {
                $query->where('statut', $request->status);
                $filterTitle = $request->status == 'nouveau' ? 'Nouveaux Signalements' : 'Signalements Traités';
            } else {
                // Par défaut : Tous les signalements non traités
                $query->where('statut', 'nouveau');
                $filterTitle = 'Nouveaux Signalements';
            }

            $signalements = $query->latest()->get();
            return view('sapeur_pompier.dashboard', compact('signalements', 'filterTitle'));
        })->name('sapeur-pompier.dashboard');

        Route::post('/update-location', function (\Illuminate\Http\Request $request) {
            $user = Auth::guard('sapeur_pompier')->user();
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'commune' => 'nullable|string',
                'adresse' => 'nullable|string',
            ]);
            $user->update([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            // On ne met à jour l'adresse et commune que s'ils sont fournis (Reverse Geocoding réussi côté JS)
            if ($request->filled('commune') && $request->filled('adresse')) {
                $user->update([
                    'commune' => $request->commune,
                    'adresse' => $request->adresse,
                ]);
            }
            return response()->json(['success' => true]);
        })->name('sapeur-pompier.update-location');

        Route::get('/signalements/{signalement}', function (Signalement $signalement) {
            // Vérifier que le signalement appartient bien à ce pompier
            if ($signalement->sapeur_pompier_id !== Auth::guard('sapeur_pompier')->id()) {
                abort(403);
            }
            $signalement->load(['user', 'personnel', 'compagnie', 'voyage', 'programme.compagnie', 'programme.gareDepart', 'vehicule']);
            return view('sapeur_pompier.signalement.show', compact('signalement'));
        })->name('sapeur-pompier.signalement.show');

        // Page bilan d'intervention (nouvelle page pleine)
        Route::get('/signalements/{signalement}/bilan', function (Signalement $signalement) {
            if ($signalement->sapeur_pompier_id !== Auth::guard('sapeur_pompier')->id()) {
                abort(403);
            }
            if ($signalement->statut === 'traite') {
                return redirect()->route('sapeur-pompier.signalement.show', $signalement->id)
                    ->with('info', 'Ce signalement a déjà été clôturé.');
            }
            $signalement->load(['user', 'personnel', 'compagnie', 'voyage', 'programme.compagnie', 'vehicule']);

            $reservations = collect();
            if ($signalement->voyage_id) {
                $reservations = \App\Models\Reservation::where('voyage_id', $signalement->voyage_id)
                    ->whereIn('statut', ['confirmee', 'terminee'])
                    ->with('user')
                    ->get();
            } elseif ($signalement->programme_id) {
                $dateVoyage = $signalement->created_at->format('Y-m-d');
                $reservations = \App\Models\Reservation::where('programme_id', $signalement->programme_id)
                    ->whereDate('date_voyage', $dateVoyage)
                    ->whereIn('statut', ['confirmee', 'terminee'])
                    ->with('user')
                    ->get();
            }

            return view('sapeur_pompier.signalement.bilan', compact('signalement', 'reservations'));
        })->name('sapeur-pompier.signalement.bilan');

        // Route pour marquer comme traité avec bilan passagers
        Route::patch('/signalements/{signalement}/mark-as-treated', function (\Illuminate\Http\Request $request, \App\Models\Signalement $signalement) {
            if ($signalement->sapeur_pompier_id !== Auth::guard('sapeur_pompier')->id()) {
                abort(403);
            }

            $signalement->details_intervention = $request->input('details_intervention');

            // Bilan passagers (indemne / evacue / blesse / mort)
            $bilanPassagers = [];
            if ($request->has('passagers')) {
                foreach ($request->input('passagers') as $resId => $data) {
                    $statut = $data['statut'] ?? 'indemne';
                    $entry = [
                        'reservation_id' => (int) $resId,
                        'statut' => $statut,
                    ];
                    if ($statut === 'evacue') {
                        $entry['hopital_nom']     = $data['hopital_nom'] ?? '';
                        $entry['hopital_adresse'] = $data['hopital_adresse'] ?? '';
                    }
                    $bilanPassagers[] = $entry;
                }
            }

            // Calcul automatique depuis les sélections passagers
            $signalement->nombre_morts   = collect($bilanPassagers)->where('statut', 'mort')->count()
                ?: (int) $request->input('nombre_morts', 0);
            $signalement->nombre_blesses = collect($bilanPassagers)->where('statut', 'blesse')->count()
                ?: (int) $request->input('nombre_blesses', 0);
            $signalement->bilan_passagers = $bilanPassagers;
            $signalement->statut = 'traite';
            $signalement->save();

            // Envoyer un message interne à la compagnie
            if ($signalement->compagnie_id) {
                $evacuees = collect($bilanPassagers)->where('statut', 'evacue');
                $indemnes = collect($bilanPassagers)->where('statut', 'indemne');

                $hopitaux = $evacuees->pluck('hopital_nom')->unique()->filter()->implode(', ');

                $messageBody = "🚨 **BILAN D'INTERVENTION — Signalement #{$signalement->id}**\n\n";
                $messageBody .= "📊 **Résumé :**\n";
                $messageBody .= "• Morts : {$signalement->nombre_morts}\n";
                $messageBody .= "• Blessés : {$signalement->nombre_blesses}\n";
                $messageBody .= "• Évacués : {$evacuees->count()}\n";
                $messageBody .= "• Indemnes : {$indemnes->count()}\n\n";

                if ($evacuees->count() > 0) {
                    $messageBody .= "🏥 **Hôpitaux :** {$hopitaux}\n\n";
                    $messageBody .= "⚠️ **Action requise :** Des passagers ont été évacués. ";
                    $messageBody .= "Veuillez notifier les contacts d'urgence des passagers évacués.\n\n";
                    $messageBody .= "👉 Cliquez sur le lien ci-dessous pour accéder à la page de notification :\n";
                    $messageBody .= route('compagnie.signalement.notification-accident', $signalement->id);
                }

                if ($signalement->details_intervention) {
                    $messageBody .= "\n\n📝 **Rapport :** {$signalement->details_intervention}";
                }

                \App\Models\CompanyMessage::create([
                    'compagnie_id' => $signalement->compagnie_id,
                    'recipient_id' => $signalement->compagnie_id,
                    'recipient_type' => 'App\\Models\\Compagnie',
                    'subject' => "🚨 Bilan d'accident — Signalement #{$signalement->id}",
                    'message' => $messageBody,
                    'is_read' => false,
                ]);
            }

            // Envoyer SMS directement aux contacts d'urgence des passagers évacués
            $evacuees = collect($bilanPassagers)->where('statut', 'evacue');
            if ($evacuees->count() > 0) {
                $smsService = app(\App\Services\SmsService::class);
                $signalement->loadMissing(['compagnie', 'programme']);
                $programme  = $signalement->programme;
                $compagnieNom = $signalement->compagnie->name ?? 'Votre compagnie de transport';
                $dateAccident = \Carbon\Carbon::parse($signalement->created_at)->format('d/m/Y a H:i');
                $trajet = ($programme->point_depart ?? 'Depart') . ' -> ' . ($programme->point_arrive ?? 'Arrivee');

                foreach ($evacuees as $evacuee) {
                    $reservation = \App\Models\Reservation::find($evacuee['reservation_id']);
                    if (!$reservation || empty($reservation->passager_urgence)) continue;

                    $hopitalNom    = $evacuee['hopital_nom'] ?? 'un hopital proche';
                    $hopitalAdresse = !empty($evacuee['hopital_adresse']) ? "\nLocalisation : " . $evacuee['hopital_adresse'] : '';
                    $nomPassager   = trim(($reservation->passager_prenom ?? '') . ' ' . ($reservation->passager_nom ?? '')) ?: 'Votre proche';

                    $smsBody = "ACCIDENT - {$compagnieNom}\n"
                        . "Le {$dateAccident} sur le trajet {$trajet}.\n"
                        . "{$nomPassager} a ete evacue(e) a : {$hopitalNom}.{$hopitalAdresse}\n"
                        . "Presentez-vous au plus vite.";

                    try {
                        $smsService->sendSms($reservation->passager_urgence, $smsBody);
                        \Illuminate\Support\Facades\Log::info('SMS accident (SP) envoyé', [
                            'to' => $reservation->passager_urgence,
                            'reservation_id' => $reservation->id,
                            'hopital' => $hopitalNom,
                        ]);
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Échec SMS accident (SP)', [
                            'to' => $reservation->passager_urgence,
                            'reservation_id' => $reservation->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }

            return redirect()->route('sapeur-pompier.signalement.show', $signalement->id)
                ->with('success', 'Intervention clôturée. Bilan enregistré, la compagnie a été notifiée et les contacts d\'urgence alertés par SMS.');
        })->name('sapeur-pompier.signalement.mark-as-treated');
    });
});



Route::prefix('api')->group(function () {
    Route::get('/company/{compagnieId}/vehicles', [SignalementController::class, 'getCompanyVehicles']);
    Route::get('/program/{programmeId}/occupancy', [SignalementController::class, 'apiProgramOccupancy']);

    Route::get('/vehicules/{id}', function ($id) {
        $vehicule = Vehicule::find($id);

        if (!$vehicule) {
            return response()->json(['error' => 'Véhicule non trouvé'], 404);
        }

        return response()->json([
            'id' => $vehicule->id,
            'marque' => $vehicule->marque,
            'modele' => $vehicule->modele,
            'immatriculation' => $vehicule->immatriculation,
            'numero_serie' => $vehicule->numero_serie,
            'type_range' => $vehicule->type_range,
            'nombre_place' => $vehicule->nombre_place,
            'is_active' => $vehicule->is_active,
            'motif_desactivation' => $vehicule->motif_desactivation,
        ]);
    });
});

//Les routes definition du accès 
Route::get('/validate-compagny-account/{email}', [CompagnieAuthenticate::class, 'defineAccess']);
Route::post('/validate-compagny-account/{email}', [CompagnieAuthenticate::class, 'submitDefineAccess'])->name('compagnie.validate');
Route::get('/validate-agent-account/{email}', [AuthenticateAgent::class, 'defineAccess']);
Route::post('/validate-agent-account/{email}', [AuthenticateAgent::class, 'submitDefineAccess'])->name('agent.validate');

// ==========================================
// Routes Espace Gare
// ==========================================
Route::prefix('gare-espace')->name('gare-espace.')->group(function () {
    // Auth routes (publiques)
    Route::get('/login', [App\Http\Controllers\GareEspace\AuthenticateGare::class, 'login'])->name('login');
    Route::post('/login', [App\Http\Controllers\GareEspace\AuthenticateGare::class, 'handleLogin'])->name('handleLogin');
    Route::get('/verify-otp', [App\Http\Controllers\GareEspace\AuthenticateGare::class, 'verifyOtp'])->name('verifyOtp');
    Route::post('/verify-otp', [App\Http\Controllers\GareEspace\AuthenticateGare::class, 'handleVerifyOtp'])->name('handleVerifyOtp');
    Route::get('/define-access/{email}', [App\Http\Controllers\GareEspace\AuthenticateGare::class, 'defineAccess'])->name('defineAccess');
    Route::post('/define-access/{email}', [App\Http\Controllers\GareEspace\AuthenticateGare::class, 'submitDefineAccess'])->name('validate');

    // Password Reset Routes
    Route::get('/password/reset', [App\Http\Controllers\GareEspace\PasswordResetController::class, 'showResetForm'])->name('password.request');
    Route::post('/password/send-otp', [App\Http\Controllers\GareEspace\PasswordResetController::class, 'sendOtp'])->name('password.sendOtp');
    Route::post('/password/verify-otp', [App\Http\Controllers\GareEspace\PasswordResetController::class, 'verifyOtp'])->name('password.verifyOtp');
    Route::post('/password/reset', [App\Http\Controllers\GareEspace\PasswordResetController::class, 'resetPassword'])->name('password.reset');

    // Routes protégées (gare middleware)
    Route::middleware('gare')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\GareEspace\GareDashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [App\Http\Controllers\GareEspace\AuthenticateGare::class, 'logout'])->name('logout');

        // Notifications
        Route::post('/notifications/mark-all-read', function () {
            auth('gare')->user()->unreadNotifications->markAsRead();
            return back();
        })->name('notifications.markAllRead');

        Route::get('/notifications/{id}/go', function (string $id) {
            $gare         = auth('gare')->user();
            $notification = $gare->notifications()->findOrFail($id);
            $notification->markAsRead();

            $data = $notification->data;
            $type = $data['type'] ?? null;

            $url = match($type) {
                'convoi_assigne_gare'
                    => isset($data['convoi_id'])
                        ? route('gare-espace.convois.show', $data['convoi_id'])
                        : route('gare-espace.convois.index'),
                default => route('gare-espace.convois.index'),
            };

            return redirect($url);
        })->name('notifications.go');

        // Profile routes
        Route::get('/profile', [App\Http\Controllers\GareEspace\GareDashboardController::class, 'profile'])->name('profile');
        Route::post('/profile/update', [App\Http\Controllers\GareEspace\GareDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/password', [App\Http\Controllers\GareEspace\GareDashboardController::class, 'updatePassword'])->name('profile.password');
        Route::post('/profile/request-location', [App\Http\Controllers\GareEspace\GareDashboardController::class, 'requestLocationUpdate'])->name('profile.requestLocation');
        Route::post('/profile/mark-location-notified', [App\Http\Controllers\GareEspace\GareDashboardController::class, 'markLocationNotified'])->name('profile.markLocationNotified');

        // Voyages
        Route::prefix('voyages')->name('voyages.')->group(function () {
            Route::get('/', [App\Http\Controllers\GareEspace\GareVoyageController::class, 'index'])->name('index');
            Route::get('/history', [App\Http\Controllers\GareEspace\GareVoyageController::class, 'history'])->name('history');
            Route::post('/', [App\Http\Controllers\GareEspace\GareVoyageController::class, 'store'])->name('store');
            Route::delete('/{voyage}', [App\Http\Controllers\GareEspace\GareVoyageController::class, 'destroy'])->name('destroy');
            // Actions de gestion des voyages bloqués
            Route::post('/{voyage}/force-complete', [App\Http\Controllers\GareEspace\GareVoyageController::class, 'forceComplete'])->name('force-complete');
            Route::post('/{voyage}/send-reminder', [App\Http\Controllers\GareEspace\GareVoyageController::class, 'sendReminder'])->name('send-reminder');
        });

        // Réservations
        Route::prefix('reservations')->name('reservations.')->group(function () {
            Route::get('/', [App\Http\Controllers\GareEspace\GareReservationController::class, 'index'])->name('index');
            Route::get('/by-date/{date}', [App\Http\Controllers\GareEspace\GareReservationController::class, 'byDate'])->name('by_date');
            Route::get('/by-month/{month}', [App\Http\Controllers\GareEspace\GareReservationController::class, 'byMonth'])->name('by_month');
            Route::get('/program/{programme}', [App\Http\Controllers\GareEspace\GareReservationController::class, 'programDetails'])->name('program');
            Route::get('/reservations-by-date', [App\Http\Controllers\GareEspace\GareReservationController::class, 'getReservationsByDate'])->name('reservations-by-date');
            Route::get('/occupied-seats', [App\Http\Controllers\GareEspace\GareReservationController::class, 'getOccupiedSeats'])->name('occupied-seats');
            Route::get('/programme-vehicle', [App\Http\Controllers\GareEspace\GareReservationController::class, 'getProgrammeVehicle'])->name('programme-vehicle');
            Route::get('/{reservation}', [App\Http\Controllers\GareEspace\GareReservationController::class, 'show'])->name('show');
        });

        // Convois reçus par la gare
        Route::prefix('convois')->name('convois.')->group(function () {
            Route::get('/', [App\Http\Controllers\GareEspace\GareConvoiController::class, 'index'])->name('index');
            Route::get('/creer', [App\Http\Controllers\GareEspace\GareConvoiController::class, 'createWalkin'])->name('create');
            Route::post('/creer', [App\Http\Controllers\GareEspace\GareConvoiController::class, 'storeWalkin'])->name('store');
            Route::get('/{convoi}/recu-pdf', [App\Http\Controllers\GareEspace\GareConvoiController::class, 'downloadRecu'])->name('recu-pdf');
            Route::post('/{convoi}/passagers', [App\Http\Controllers\GareEspace\GareConvoiController::class, 'storeWalkinPassengers'])->name('store-passagers');
            Route::get('/{convoi}', [App\Http\Controllers\GareEspace\GareConvoiController::class, 'show'])->name('show');
            Route::get('/{convoi}/location', [App\Http\Controllers\GareEspace\GareConvoiController::class, 'location'])->name('location');
            Route::post('/{convoi}/assign', [App\Http\Controllers\GareEspace\GareConvoiController::class, 'assign'])->name('assign');
            Route::post('/{convoi}/reassign', [App\Http\Controllers\GareEspace\GareConvoiController::class, 'reassign'])->name('reassign');
            Route::post('/{convoi}/unassign', [App\Http\Controllers\GareEspace\GareConvoiController::class, 'unassign'])->name('unassign');
            Route::post('/{convoi}/valider', [App\Http\Controllers\GareEspace\GareConvoiController::class, 'valider'])->name('valider');
            Route::post('/{convoi}/refuser', [App\Http\Controllers\GareEspace\GareConvoiController::class, 'refuser'])->name('refuser');
            Route::post('/{convoi}/solder', [App\Http\Controllers\GareEspace\GareConvoiController::class, 'solder'])->name('solder');
            Route::post('/{convoi}/payer-walkin', [App\Http\Controllers\GareEspace\GareConvoiController::class, 'payerWalkin'])->name('payer-walkin');
            Route::post('/{convoi}/save-full', [App\Http\Controllers\GareEspace\GareConvoiController::class, 'saveFull'])->name('save-full');
        });

        // Personnel (CRUD)
        Route::prefix('personnel')->name('personnel.')->group(function () {
            Route::get('/', [App\Http\Controllers\GareEspace\GarePersonnelController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\GareEspace\GarePersonnelController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\GareEspace\GarePersonnelController::class, 'store'])->name('store');
            Route::get('/{personnel}/edit', [App\Http\Controllers\GareEspace\GarePersonnelController::class, 'edit'])->name('edit');
            Route::put('/{personnel}', [App\Http\Controllers\GareEspace\GarePersonnelController::class, 'update'])->name('update');
            Route::delete('/{personnel}', [App\Http\Controllers\GareEspace\GarePersonnelController::class, 'destroy'])->name('destroy');
        });

        // Véhicules (CRUD)
        Route::prefix('vehicules')->name('vehicules.')->group(function () {
            Route::get('/', [App\Http\Controllers\GareEspace\GareVehiculeController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\GareEspace\GareVehiculeController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\GareEspace\GareVehiculeController::class, 'store'])->name('store');
            Route::get('/{vehicule}/edit', [App\Http\Controllers\GareEspace\GareVehiculeController::class, 'edit'])->name('edit');
            Route::put('/{vehicule}', [App\Http\Controllers\GareEspace\GareVehiculeController::class, 'update'])->name('update');
            Route::delete('/{vehicule}', [App\Http\Controllers\GareEspace\GareVehiculeController::class, 'destroy'])->name('destroy');
        });

        // Caisse (CRUD)
        Route::prefix('caisse')->name('caisse.')->group(function () {
            Route::get('/', [App\Http\Controllers\GareEspace\GareCaisseController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\GareEspace\GareCaisseController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\GareEspace\GareCaisseController::class, 'store'])->name('store');
            Route::get('/{caisse}/edit', [App\Http\Controllers\GareEspace\GareCaisseController::class, 'edit'])->name('edit');
            Route::put('/{caisse}', [App\Http\Controllers\GareEspace\GareCaisseController::class, 'update'])->name('update');
            Route::delete('/{caisse}', [App\Http\Controllers\GareEspace\GareCaisseController::class, 'destroy'])->name('destroy');
        });

        // Agents (CRUD)
        Route::prefix('agents')->name('agents.')->group(function () {
            Route::get('/', [App\Http\Controllers\GareEspace\GareAgentController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\GareEspace\GareAgentController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\GareEspace\GareAgentController::class, 'store'])->name('store');
            Route::get('/{agent}/edit', [App\Http\Controllers\GareEspace\GareAgentController::class, 'edit'])->name('edit');
            Route::put('/{agent}', [App\Http\Controllers\GareEspace\GareAgentController::class, 'update'])->name('update');
            Route::delete('/{agent}', [App\Http\Controllers\GareEspace\GareAgentController::class, 'destroy'])->name('destroy');
        });

        // Itinéraires (CRUD)
        Route::prefix('itineraire')->name('itineraire.')->group(function () {
            Route::get('/', [App\Http\Controllers\GareEspace\GareItineraireController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\GareEspace\GareItineraireController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\GareEspace\GareItineraireController::class, 'store'])->name('store');
            Route::get('/{itineraire}/edit', [App\Http\Controllers\GareEspace\GareItineraireController::class, 'edit'])->name('edit');
            Route::put('/{itineraire}', [App\Http\Controllers\GareEspace\GareItineraireController::class, 'update'])->name('update');
            Route::delete('/{itineraire}', [App\Http\Controllers\GareEspace\GareItineraireController::class, 'destroy'])->name('destroy');
        });

        // Programmes
        Route::prefix('programme')->name('programme.')->group(function () {
            Route::get('/', [App\Http\Controllers\GareEspace\GareProgrammeController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\GareEspace\GareProgrammeController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\GareEspace\GareProgrammeController::class, 'store'])->name('store');
            Route::post('/update-route', [App\Http\Controllers\GareEspace\GareProgrammeController::class, 'updateRoute'])->name('updateRoute');
            Route::delete('/{id}', [App\Http\Controllers\GareEspace\GareProgrammeController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/annuler', [App\Http\Controllers\GareEspace\GareProgrammeController::class, 'annuler'])->name('annuler');
        });

        // Tracking temps réel
        Route::prefix('tracking')->name('tracking.')->group(function () {
            Route::get('/', [App\Http\Controllers\GareEspace\GareTrackingController::class, 'index'])->name('index');
            Route::get('/locations', [App\Http\Controllers\GareEspace\GareTrackingController::class, 'getActiveLocations'])->name('locations');
        });

        // Messages (Boîte de réception)
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/', [App\Http\Controllers\GareEspace\GareMessageController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\GareEspace\GareMessageController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\GareEspace\GareMessageController::class, 'store'])->name('store');
            Route::get('/recipients', [App\Http\Controllers\GareEspace\GareMessageController::class, 'getRecipients'])->name('recipients');
            Route::get('/{id}', [App\Http\Controllers\GareEspace\GareMessageController::class, 'show'])->name('show');
            Route::patch('/{id}/mark-read', [App\Http\Controllers\GareEspace\GareMessageController::class, 'markStaffRead'])->name('markStaffRead');
        });
    });
});

// On change Route::get en Route::match(['get', 'post'])
Route::match(['get', 'post'], '/payment/callback', function (Request $request) {
    $transactionId = $request->get('transactionId') ?? $request->get('transaction_id');
    $cancel = $request->get('cancel');

    if ($cancel) {
        // Optionnel : Créer une vue 'payment.result' ou retourner un JSON simple pour tester
        return response()->json([
            'success' => false,
            'message' => 'Paiement annulé',
            'transaction_id' => $transactionId
        ]);
    }

    // Vérifier le paiement
    $paiement = Paiement::where('transaction_id', $transactionId)->first();

    // Si le paiement est déjà success, super.
    if ($paiement && $paiement->status === 'success') {
        return response()->json([
            'success' => true,
            'message' => 'Paiement réussi ! Vos réservations ont été confirmées.',
            'transaction_id' => $transactionId
        ]);
    }

    // Sinon, on dit à l'utilisateur d'attendre (le webhook arrivera quelques secondes après)
    return response()->json([
        'success' => true, // On met true pour que l'app ne panique pas
        'message' => 'Paiement en cours de validation...',
        'transaction_id' => $transactionId
    ]);
})->name('payment.callback');

// Routes Chauffeur
Route::prefix('chauffeur')->name('chauffeur.')->group(function () {
    Route::get('/login', [ChauffeurAuthenticate::class, 'login'])->name('login');
    Route::post('/login', [ChauffeurAuthenticate::class, 'handleLogin'])->name('login.submit');

    // OTP Verification routes
    Route::get('/verify-otp', [\App\Http\Controllers\Chauffeur\OtpVerificationController::class, 'showVerifyForm'])->name('verify-otp');
    Route::post('/verify-otp', [\App\Http\Controllers\Chauffeur\OtpVerificationController::class, 'verify'])->name('verify-otp.submit');
    Route::post('/verify-otp/resend', [\App\Http\Controllers\Chauffeur\OtpVerificationController::class, 'resend'])->name('verify-otp.resend');

    Route::get('/password/forgot', [\App\Http\Controllers\Chauffeur\ChauffeurForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/forgot', [\App\Http\Controllers\Chauffeur\ChauffeurForgotPasswordController::class, 'sendOtp'])->name('password.email');
    Route::get('/password/reset', [\App\Http\Controllers\Chauffeur\ChauffeurForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [\App\Http\Controllers\Chauffeur\ChauffeurForgotPasswordController::class, 'resetPassword'])->name('password.update');

    Route::middleware('chauffeur')->group(function () {
        Route::get('/dashboard', [ChauffeurController::class, 'dashboard'])->name('dashboard');
        Route::get('/logout', [ChauffeurAuthenticate::class, 'logout'])->name('logout');
        Route::get('/voyages-history', [ChauffeurController::class, 'myVoyages'])->name('voyages.history');

        // Profil routes
        Route::get('/profile', [ChauffeurController::class, 'profile'])->name('profile');
        Route::post('/profile/update', [ChauffeurController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/password', [ChauffeurController::class, 'updatePassword'])->name('profile.password');

        // Voyage management routes
        Route::prefix('voyages')->name('voyages.')->group(function () {
            Route::get('/', [ChauffeurVoyageController::class, 'index'])->name('index');
            Route::post('/{voyage}/confirm', [ChauffeurVoyageController::class, 'confirm'])->name('confirm');
            Route::post('/{voyage}/start', [ChauffeurVoyageController::class, 'start'])->name('start');
            Route::post('/{voyage}/complete', [ChauffeurVoyageController::class, 'complete'])->name('complete');
            Route::post('/{voyage}/update-location', [ChauffeurVoyageController::class, 'updateLocation'])->name('update-location');
            Route::post('/convois/{convoi}/update-location', [ChauffeurVoyageController::class, 'updateConvoiLocation'])->name('convois.update-location');
            Route::post('/{voyage}/annuler', [ChauffeurVoyageController::class, 'annuler'])->name('annuler');
            Route::get('/{voyage}/tracking', [ChauffeurVoyageController::class, 'tracking'])->name('tracking');
            Route::post('/convois/{convoi}/start', [ChauffeurVoyageController::class, 'startConvoi'])->name('convois.start');
            Route::post('/convois/{convoi}/complete', [ChauffeurVoyageController::class, 'completeConvoi'])->name('convois.complete');
            Route::post('/convois/{convoi}/annuler', [ChauffeurVoyageController::class, 'annulerConvoi'])->name('convois.annuler');
        });

        // Inbox for Chauffeur
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Chauffeur\ChauffeurMessageController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Chauffeur\ChauffeurMessageController::class, 'store'])->name('store');
            Route::get('/{id}', [\App\Http\Controllers\Chauffeur\ChauffeurMessageController::class, 'show'])->name('show');
        });

        // Signalements for Chauffeur
        Route::prefix('signalements')->name('signalements.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Chauffeur\ChauffeurSignalementController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Chauffeur\ChauffeurSignalementController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Chauffeur\ChauffeurSignalementController::class, 'store'])->name('store');
            Route::get('/{signalement}', [\App\Http\Controllers\Chauffeur\ChauffeurSignalementController::class, 'show'])->name('show');
        });

        // Scan QR des réservations pour le chauffeur
        Route::prefix('reservations')->name('reservations.')->group(function () {
            Route::get('/scan', [ChauffeurReservationController::class, 'scanPage'])->name('scan');
            Route::post('/search', [ChauffeurReservationController::class, 'search'])->name('search');
            Route::post('/confirm', [ChauffeurReservationController::class, 'confirm'])->name('confirm');
        });
    });
});
