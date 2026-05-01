<?php

use Illuminate\Support\Facades\Route;
use App\Addons\Affiliation\Http\Controllers\Admin\AdminAffiliateController;

/*
|--------------------------------------------------------------------------
| Affiliation Admin Routes
|--------------------------------------------------------------------------
| Préfixe automatique : {admin_prefix}/affiliation
| Nom automatique : affiliation.admin.
| Middlewares automatiques : web, admin
*/

// Paramètres
Route::get('/settings', [AdminAffiliateController::class, 'settings'])->name('settings');
Route::put('/settings', [AdminAffiliateController::class, 'updateSettings'])->name('settings.update');

// Commissions
Route::get('/commissions', [AdminAffiliateController::class, 'commissions'])->name('commissions');
Route::post('/commissions/approve', [AdminAffiliateController::class, 'approveCommissions'])->name('commissions.approve');
Route::post('/commissions/pay', [AdminAffiliateController::class, 'payCommissions'])->name('commissions.pay');

// Gestion des affiliés (routes avec paramètres EN DERNIER)
Route::get('/', [AdminAffiliateController::class, 'index'])->name('index');
Route::get('/{affiliate}', [AdminAffiliateController::class, 'show'])->name('show');
Route::get('/{affiliate}/edit', [AdminAffiliateController::class, 'edit'])->name('edit');
Route::put('/{affiliate}', [AdminAffiliateController::class, 'update'])->name('update');
Route::delete('/{affiliate}', [AdminAffiliateController::class, 'destroy'])->name('destroy');
