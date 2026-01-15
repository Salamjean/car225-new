<?php

use App\Http\Controllers\Admin\AdminDashboard;
use App\Http\Controllers\Admin\AuthenticateAdmin;
use App\Http\Controllers\Admin\Itineraire\AdminItineraireController;
use App\Http\Controllers\Compagnie\CompagnieAuthenticate;
use App\Http\Controllers\Compagnie\CompagnieController;
use App\Http\Controllers\Compagnie\CompagnieDashboard;
use App\Http\Controllers\Compagnie\Itineraire\ItineraireController;
use App\Http\Controllers\Compagnie\Personnel\PersonnelController;
use App\Http\Controllers\Compagnie\Programme\ProgrammeController;
use App\Http\Controllers\Compagnie\Vehicule\VehiculeController;
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\User\Reservation\ReservationController;
use App\Http\Controllers\User\UserAuthenticate;
use App\Http\Controllers\User\UserController;
use App\Models\Vehicule;
use Illuminate\Support\Facades\Route;

//Les routes de la pages 
Route::prefix('/')->group(function () {
    Route::get('/', [HomeController::class, 'home'])->name('home');
    Route::get('/itineraires/search', [HomeController::class, 'search'])->name('programmes.search');
    Route::get('/itineraires', [HomeController::class, 'all'])->name('programmes.all');
    Route::get('/itineraires/{itineraire}', [HomeController::class, 'show'])->name('programmes.show');
});
Route::get('/vehicule/details/{id}', [HomeController::class, 'getVehicleDetails'])->name('home.vehicle.details');
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
});

//Les routes de gestion du @admin
Route::prefix('company')->group(function () {
    Route::get('/login', [CompagnieAuthenticate::class, 'login'])->name('compagnie.login');
    Route::post('/', [CompagnieAuthenticate::class, 'handleLogin'])->name('compagnie.handleLogin');
});

Route::middleware('compagnie')->prefix('company')->group(function () {
    Route::get('/dashboard', [CompagnieDashboard::class, 'dashboard'])->name('compagnie.dashboard');
    Route::get('/logout', [CompagnieDashboard::class, 'logout'])->name('compagnie.logout');

    //Les routes pour la gestion des itineraires 
    Route::prefix('Itinerary')->group(function () {
        Route::get('/all', [ItineraireController::class, 'index'])->name('itineraire.index');
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

    //Les routes de programmation 
    Route::prefix('programme')->group(function () {
        Route::get('/prgramme', [ProgrammeController::class, 'index'])->name('programme.index');
        Route::get('/hsi', [ProgrammeController::class, 'history'])->name('programme.history');
        Route::get('/createPro', [ProgrammeController::class, 'create'])->name('programme.create');
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
});

//Les routes de gestion du @user
Route::prefix('user')->group(function () {
    Route::get('/login', [UserAuthenticate::class, 'login'])->name('login');
    Route::post('/login', [UserAuthenticate::class, 'handleLogin'])->name('user.handleLogin');
    Route::get('/register', [UserAuthenticate::class, 'register'])->name('user.register');
    Route::post('/register', [UserAuthenticate::class, 'handleRegister'])->name('user.handleRegister');
});
Route::middleware('auth')->prefix('user')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
    Route::get('/logout', [UserController::class, 'logout'])->name('user.logout');

    //Les routes pour faire une reservation 
    Route::prefix('booking')->group(function () {
        Route::get('/allReserve', [ReservationController::class, 'index'])->name('reservation.index');
        Route::get('/reservation', [ReservationController::class, 'create'])->name('reservation.create');
        Route::get('/vehicle/{id}', [ReservationController::class, 'showVehicle'])->name('user.reservation.vehicle');
        Route::get('/program/{id}', [ReservationController::class, 'getProgram'])->name('user.reservation.program');
        Route::get('/reservation/reserved-seats/{programId}', [ReservationController::class, 'getReservedSeats'])->name('user.reservation.reserved-seats');
        Route::post('/reservation', [ReservationController::class, 'store'])->name('reservation.store');
        Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
        Route::get('/reservations/{reservation}/download', [ReservationController::class, 'download'])->name('reservations.download');
        Route::get('/reservations/{reservation}/ticket', [ReservationController::class, 'ticket'])->name('reservations.ticket');
        Route::delete('/reservations/{reservation}', [ReservationController::class, 'cancel'])->name('reservations.cancel');
    });
    Route::get('/programmes/{programme}/recalculate-status', [ReservationController::class, 'recalculateProgramStatus'])->name('programmes.recalculate-status');
    Route::get('/programmes/{programme}/status-for-date/{date}', [ReservationController::class, 'getProgramStatusForDate'])->name('programmes.status-for-date');
});

//Les routes definition du accès 
Route::get('/validate-compagny-account/{email}', [CompagnieAuthenticate::class, 'defineAccess']);
Route::post('/validate-compagny-account/{email}', [CompagnieAuthenticate::class, 'submitDefineAccess'])->name('compagnie.validate');



Route::prefix('api')->group(function () {
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
