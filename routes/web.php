<?php

use App\Http\Controllers\Admin\AdminDashboard;
use App\Http\Controllers\Admin\AuthenticateAdmin;
use App\Http\Controllers\Admin\Itineraire\AdminItineraireController;
use App\Http\Controllers\Agent\AgentController;
use App\Http\Controllers\Agent\AgentDashboard;
use App\Http\Controllers\Agent\AuthenticateAgent;
use App\Http\Controllers\Agent\ReservationController as AgentReservationController;
use App\Http\Controllers\User\WalletController;
use App\Http\Controllers\Compagnie\Agent\AgentCompagnieController;
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
use App\Http\Controllers\User\PasswordResetController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\Reservation\ReservationController;
use App\Http\Controllers\User\UserAuthenticate;
use App\Http\Controllers\User\UserController;
use App\Models\Vehicule;
use App\Http\Controllers\SignalementController;
use App\Http\Controllers\SapeurPompier\SapeurPompierController;
use App\Http\Controllers\SapeurPompier\SapeurPompierAuthenticate;
use App\Models\Signalement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

//Les routes de la pages 
Route::prefix('/')->group(function () {
    Route::get('/', [HomeController::class, 'home'])->name('home');
    Route::get('/itineraires/search', [HomeController::class, 'search'])->name('programmes.search');
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
    Route::post('/home/contact/store', [AccueilController::class, 'storeContact'])->name('home.contact.store');
});

//Les routes de gestion du @admin
Route::prefix('admin')->group(function () {
    Route::get('/login', [AuthenticateAdmin::class, 'login'])->name('admin.login');
    Route::post('/', [AuthenticateAdmin::class, 'handleLogin'])->name('admin.handleLogin');
});

Route::middleware('admin')->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/logout', [AdminDashboard::class, 'logout'])->name('admin.logout');

    //Les routes pour creer une compagnie 
    Route::prefix('company')->group(function () {
        Route::get('/index', [CompagnieController::class, 'index'])->name('compagnie.index');
        Route::get('/create', [CompagnieController::class, 'create'])->name('compagnie.create');
        Route::post('/create', [CompagnieController::class, 'store'])->name('compagnie.store');
        Route::get('/{compagnie}', [CompagnieController::class, 'show'])->name('compagnie.show');
        Route::get('/{compagnie}/edit', [CompagnieController::class, 'edit'])->name('compagnie.edit');
        Route::put('/{compagnie}', [CompagnieController::class, 'update'])->name('compagnie.update');
        Route::delete('/{compagnie}', [CompagnieController::class, 'destroy'])->name('compagnie.destroy');
    });

    //Les routes pour voir les itineraires
    Route::get('/indeItinery', [AdminItineraireController::class, 'index'])->name('admin.itineraire.index');

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
});

//Les routes de gestion du @admin
Route::prefix('company')->group(function () {
    Route::get('/login', [CompagnieAuthenticate::class, 'login'])->name('compagnie.login');
    Route::post('/', [CompagnieAuthenticate::class, 'handleLogin'])->name('compagnie.handleLogin');
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
        Route::delete('/{agent}', [AgentController::class, 'destroy'])->name('compagnie.agents.destroy');
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
        Route::get('/details', [CompagnieReservationController::class, 'details'])->name('company.reservation.details');
        Route::get('/occupied-seats', [CompagnieReservationController::class, 'getOccupiedSeats'])->name('company.reservation.occupied-seats');
    });

    // Routes de gestion des signalements pour la compagnie
    Route::prefix('signalements')->group(function () {
        Route::get('/', [CompagnieSignalementController::class, 'index'])->name('compagnie.signalements.index');
        Route::get('/{id}', [CompagnieSignalementController::class, 'show'])->name('compagnie.signalements.show');
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
    Route::get('/vente-success', [App\Http\Controllers\Caisse\CaisseController::class, 'venteSuccess'])->name('caisse.vente-success');
    
    // Sales history and printing
    Route::get('/ventes', [App\Http\Controllers\Caisse\CaisseController::class, 'ventes'])->name('caisse.ventes');
    Route::get('/ticket/{reservation}/imprimer', [App\Http\Controllers\Caisse\CaisseController::class, 'imprimerTicket'])->name('caisse.ticket.imprimer');
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
    
    // API Route for Return Trips (Shared Logic with User)
    Route::get('/api/return-trips', [App\Http\Controllers\User\Reservation\ReservationController::class, 'apiReturnTrips'])->name('hotesse.api.return-trips');
});


//Les routes de gestion des @agents
Route::prefix('agent')->group(function () {
    Route::get('/login', [AuthenticateAgent::class, 'login'])->name('agent.login');
    Route::post('/login', [AuthenticateAgent::class, 'handleLogin'])->name('agent.handleLogin');
});
Route::middleware('agent')->prefix('agent')->group(function () {
    Route::get('/dashboard', [AgentDashboard::class, 'dashboard'])->name('agent.dashboard');
    Route::get('/logout', [AgentDashboard::class, 'logout'])->name('agent.logout');

    // Gestion des réservations
    Route::prefix('reservations')->group(function () {
        Route::get('/', [AgentReservationController::class, 'index'])->name('agent.reservations.index');
        Route::get('/recherche', [AgentReservationController::class, 'recherchePage'])->name('agent.reservations.recherche');
        Route::get('/historique', [AgentReservationController::class, 'historique'])->name('agent.reservations.historique');
        Route::get('/programmes-for-scan', [AgentReservationController::class, 'getProgrammesForScan'])->name('agent.programmes.for-scan');
        Route::post('/scan', [AgentReservationController::class, 'scan'])->name('agent.reservations.scan');
        Route::post('/search', [AgentReservationController::class, 'search'])->name('agent.reservations.search');
        Route::post('/search-by-reference', [AgentReservationController::class, 'searchByReference'])->name('agent.reservations.search-by-reference');
        Route::post('/confirm', [AgentReservationController::class, 'confirm'])->name('agent.reservations.confirm');
    });
});

//Les routes de gestion du @user
Route::prefix('user')->group(function () {
    Route::get('/login', [UserAuthenticate::class, 'login'])->name('login');
    Route::post('/login', [UserAuthenticate::class, 'handleLogin'])->name('user.handleLogin');
    Route::get('/register', [UserAuthenticate::class, 'register'])->name('user.register');
    Route::post('/register', [UserAuthenticate::class, 'handleRegister'])->name('user.handleRegister');
    
    // Password Reset Routes
    Route::get('/password/reset', [PasswordResetController::class, 'showResetForm'])->name('password.request');
    Route::post('/password/send-otp', [PasswordResetController::class, 'sendOtp'])->name('password.sendOtp');
    Route::post('/password/verify-otp', [PasswordResetController::class, 'verifyOtp'])->name('password.verifyOtp');
    Route::post('/password/reset', [PasswordResetController::class, 'resetPassword'])->name('password.reset');
});

Route::middleware('auth')->prefix('user')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
    Route::get('/logout', [UserController::class, 'logout'])->name('user.logout');

    // Wallet Routes
    Route::get('/compte', [WalletController::class, 'index'])->name('user.wallet.index');
    Route::post('/compte/recharge', [WalletController::class, 'recharge'])->name('user.wallet.recharge');
    Route::post('/compte/verify', [WalletController::class, 'verifyRecharge'])->name('user.wallet.verify');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('user.profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('user.profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('user.profile.password');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('user.profile.photo');


    //Les routes pour faire une reservation 
    Route::prefix('booking')->group(function () {
        Route::get('/allReserve', [ReservationController::class, 'index'])->name('reservation.index');
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
        Route::get('/api/programmes', [ReservationController::class, 'apiProgrammes'])->name('api.programmes');
        Route::get('/api/grouped-routes', [ReservationController::class, 'apiGroupedRoutes'])->name('api.grouped-routes');
        Route::get('/api/route-dates', [ReservationController::class, 'apiRouteDates'])->name('api.route-dates');
        Route::get('/api/route-schedules', [ReservationController::class, 'apiRouteSchedules'])->name('api.route-schedules');
        Route::get('/api/return-trips', [ReservationController::class, 'apiReturnTrips'])->name('api.return-trips');
    });
    Route::get('/programmes/{programme}/recalculate-status', [ReservationController::class, 'recalculateProgramStatus'])->name('programmes.recalculate-status');
    Route::get('/programmes/{programme}/status-for-date/{date}', [ReservationController::class, 'getProgramStatusForDate'])->name('programmes.status-for-date');

    // Signalement de problèmes
    Route::prefix('signalement')->group(function () {
        Route::get('/create', [SignalementController::class, 'create'])->name('signalement.create');
        Route::post('/store', [SignalementController::class, 'store'])->name('signalement.store');
    });
});

// Paiement CinetPay (Hors Auth pour le webhook)
Route::prefix('user')->group(function () {
    Route::post('/payment/notify', [App\Http\Controllers\PaymentController::class, 'notify'])->name('payment.notify');
    Route::post('/compte/notify', [App\Http\Controllers\User\WalletController::class, 'notify'])->name('cinetpay.notify');
    Route::get('/payment/return', [App\Http\Controllers\PaymentController::class, 'return'])->name('payment.return');
});


// Routes Sapeur Pompier (Interface dédiée)
Route::prefix('sapeur-pompier')->group(function () {
    Route::get('/login', [SapeurPompierAuthenticate::class, 'login'])->name('sapeur-pompier.login');
    Route::post('/login', [SapeurPompierAuthenticate::class, 'handleLogin']);
    Route::get('/define-access/{email}', [SapeurPompierAuthenticate::class, 'defineAccess'])->name('sapeur-pompier.define-access');
    Route::post('/define-access', [SapeurPompierAuthenticate::class, 'submitDefineAccess'])->name('sapeur-pompier.submit-define-access');
    Route::get('/logout', [SapeurPompierAuthenticate::class, 'logout'])->name('sapeur-pompier.logout');

    Route::middleware('sapeur_pompier')->group(function () {
        Route::get('/dashboard', function (\Illuminate\Http\Request $request) {
            $query = Signalement::where('sapeur_pompier_id', Auth::guard('sapeur_pompier')->id());

            if ($request->has('status')) {
                $query->where('statut', $request->status);
                $filterTitle = $request->status == 'nouveau' ? 'Nouveaux Signalements' : 'Signalements Traités';
            } else {
                // Par défaut : Seulement les accidents non traités
                $query->where('type', 'accident')->where('statut', 'nouveau');
                $filterTitle = 'Accidents en cours (Non traités)';
            }

            $signalements = $query->latest()->get();
            return view('sapeur_pompier.dashboard', compact('signalements', 'filterTitle'));
        })->name('sapeur-pompier.dashboard');

        Route::get('/signalements/{signalement}', function (Signalement $signalement) {
            // Vérifier que le signalement appartient bien à ce pompier
            if ($signalement->sapeur_pompier_id !== Auth::guard('sapeur_pompier')->id()) {
                abort(403);
            }
            return view('sapeur_pompier.signalement.show', compact('signalement'));
        })->name('sapeur-pompier.signalement.show');

        // Route pour marquer comme traité
        Route::patch('/signalements/{signalement}/mark-as-treated', function (\Illuminate\Http\Request $request, \App\Models\Signalement $signalement) {
            if ($signalement->sapeur_pompier_id !== Auth::guard('sapeur_pompier')->id()) {
                abort(403);
            }

            $signalement->nombre_morts = $request->input('nombre_morts', 0);
            $signalement->nombre_blesses = $request->input('nombre_blesses', 0);
            $signalement->details_intervention = $request->input('details_intervention');
            $signalement->statut = 'traite';

            $signalement->save();
            return back()->with('success', 'Intervention clôturée et bilan enregistré avec succès.');
        })->name('sapeur-pompier.signalement.mark-as-treated');
    });
});



Route::prefix('api')->group(function () {
    Route::get('/company/{compagnieId}/vehicles', [SignalementController::class, 'getCompanyVehicles']);

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