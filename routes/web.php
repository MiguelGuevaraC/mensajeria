<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\web\CompanyController;
use App\Http\Controllers\web\DashboardController;
use App\Http\Controllers\web\GroupMenuController;
use App\Http\Controllers\web\GroupSendController;
use App\Http\Controllers\web\MessageController;
use App\Http\Controllers\web\MigrationController;
use App\Http\Controllers\web\OptionMenuController;
use App\Http\Controllers\web\TypeUserController;
use App\Http\Controllers\web\UserController;
use App\Http\Controllers\web\WhatsappSendController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});
Route::get('/login', function () {
    return view('auth.login');
});
Route::get('index.html', function () {
    return view('auth.login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware(['ensureTokenIsValid'])->group(function () {
    return view('auth.login');
});

Route::post('login', [AuthController::class, 'login'])->name('login');

Route::group(['middleware' => ['auth']], function () {

    Route::get('logout', [AuthController::class, 'logout']);

    Route::resource('vistaInicio', 'App\Http\Controllers\InicioController');
    Route::get('vistaInicio', [InicioController::class, 'index'])->name('vistaInicio');

    //USER
    Route::get('user', [UserController::class, 'index']);
    Route::get('userAll', [UserController::class, 'all']);
    Route::get('user/{id}', [UserController::class, 'show']);
    Route::post('user', [UserController::class, 'store']);
    Route::put('user/{id}', [UserController::class, 'update']);
    Route::delete('user/{id}', [UserController::class, 'destroy']);

    //GROUP MENU
    Route::get('groupmenu', [GroupMenuController::class, 'index']);
    Route::get('groupmenu/{id}', [GroupMenuController::class, 'show']);
    Route::post('groupmenu', [GroupMenuController::class, 'store']);
    Route::put('groupmenu/{id}', [GroupMenuController::class, 'update']);
    Route::delete('groupmenu/{id}', [GroupMenuController::class, 'destroy']);

    //GROUP MENU
    Route::get('options', [OptionMenuController::class, 'index']);
    Route::get('options/{id}', [OptionMenuController::class, 'show']);
    Route::post('options', [OptionMenuController::class, 'store']);
    Route::put('options/{id}', [OptionMenuController::class, 'update']);
    Route::delete('options/{id}', [OptionMenuController::class, 'destroy']);

    //TYPE USER
    Route::get('access', [TypeUserController::class, 'index']);
    Route::get('accessAll', [TypeUserController::class, 'all']);
    Route::get('access/{id}', [TypeUserController::class, 'show']);
    Route::post('access', [TypeUserController::class, 'store']);
    Route::put('access/{id}', [TypeUserController::class, 'update']);
    Route::delete('access/{id}', [TypeUserController::class, 'destroy']);
    Route::post('access/setAccess', [TypeUserController::class, 'setAccess']);
    Route::get('allTypeUserAndCompanies', [TypeUserController::class, 'allTypeUserAndCompanies']);

    //MIGRATION
    Route::get('migracion', [MigrationController::class, 'index']);
    Route::get('migracionAll', [MigrationController::class, 'all']);

    Route::get('migracion/{id}', [MigrationController::class, 'show']);
    Route::post('migracion', [MigrationController::class, 'store']);
    Route::put('migracion/{id}', [MigrationController::class, 'update']);
    Route::delete('migracion/{id}', [MigrationController::class, 'destroy']);

    //COMPANY
    Route::get('company', [CompanyController::class, 'index']);
    Route::get('company/{id}', [CompanyController::class, 'show']);
    Route::get('companyAll', [CompanyController::class, 'all']);

    Route::post('company', [CompanyController::class, 'store']);
    Route::put('company/{id}', [CompanyController::class, 'update']);
    Route::delete('company/{id}', [CompanyController::class, 'destroy']);

    //MESSAGE
    Route::get('message', [MessageController::class, 'index']);
    Route::get('messageAll', [MessageController::class, 'all']);


    Route::get('message/showExample/{id}', [MessageController::class, 'showExample']);
    Route::get('message/{id}', [MessageController::class, 'show']);
    Route::post('message', [MessageController::class, 'store']);
    Route::post('message/{id}', [MessageController::class, 'update']);
    Route::delete('message/{id}', [MessageController::class, 'destroy']);

    Route::get('pdfExport', [WhatsappSendController::class, 'pdfExport'])->name('pdf.export');
    Route::get('excelExport', [WhatsappSendController::class, 'excelExport'])->name('excel.export');

    //DASHBOARD
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::get('dataDashboard', [DashboardController::class, 'dataDashboard']);

    Route::get('groupSend', [GroupSendController::class, 'index']);
    Route::get('groupSendAll', [GroupSendController::class, 'all']);
    Route::get('groupSend/{id}', [GroupSendController::class, 'show']);
    Route::post('groupSend', [GroupSendController::class, 'store']);
    Route::put('groupSend/{id}', [GroupSendController::class, 'update']);
    Route::delete('groupSend/{id}', [GroupSendController::class, 'destroy']);


    Route::get('searchByDni/{dni}', [UserController::class, 'searchByDni']);
    Route::get('searchByRuc/{ruc}', [UserController::class, 'searchByRuc']);

});
