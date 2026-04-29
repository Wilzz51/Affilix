<?php

use Illuminate\Support\Facades\Route;
use App\Addons\Affiliation\Http\Controllers\AffiliateController;

/*
|--------------------------------------------------------------------------
| Affiliation Client Routes
|--------------------------------------------------------------------------
| Nom automatique : affiliation.
| Middleware automatique : web
*/

// Route publique pour tracker les clics (60 requêtes/minute max par IP)
Route::get('/ref/{code}', [AffiliateController::class, 'trackClick'])
    ->middleware('throttle:60,1')
    ->name('track');

// Routes client (authentification requise)
Route::middleware(['auth'])->prefix('affiliation')->group(function () {
    Route::get('/register', [AffiliateController::class, 'register'])->name('register');
    Route::post('/register', [AffiliateController::class, 'store'])->name('store');
    Route::get('/dashboard', [AffiliateController::class, 'dashboard'])->name('dashboard');
    Route::get('/commissions', [AffiliateController::class, 'commissions'])->name('commissions');
    Route::get('/referrals', [AffiliateController::class, 'referrals'])->name('referrals');
    Route::get('/settings', [AffiliateController::class, 'settings'])->name('settings');
    Route::put('/settings', [AffiliateController::class, 'updateSettings'])->name('settings.update');
});
