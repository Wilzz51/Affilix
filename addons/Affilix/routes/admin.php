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

// Tableau de bord global
Route::get('/dashboard', [AdminAffiliateController::class, 'dashboard'])->name('dashboard');

// Paramètres
Route::get('/settings', [AdminAffiliateController::class, 'settings'])->name('settings');
Route::put('/settings', [AdminAffiliateController::class, 'updateSettings'])->name('settings.update');

// Demandes de paiement
Route::get('/withdrawals', [AdminAffiliateController::class, 'withdrawalRequests'])->name('withdrawals');
Route::post('/withdrawals/{withdrawal}/pay', [AdminAffiliateController::class, 'payWithdrawal'])->name('withdrawals.pay');
Route::post('/withdrawals/{withdrawal}/reject', [AdminAffiliateController::class, 'rejectWithdrawal'])->name('withdrawals.reject');

// Commissions
Route::get('/commissions', [AdminAffiliateController::class, 'commissions'])->name('commissions');
Route::get('/commissions/export', [AdminAffiliateController::class, 'exportCommissions'])->name('commissions.export');
Route::post('/commissions/approve', [AdminAffiliateController::class, 'approveCommissions'])->name('commissions.approve');
Route::post('/commissions/pay', [AdminAffiliateController::class, 'payCommissions'])->name('commissions.pay');
Route::post('/commissions/{commission}/cancel', [AdminAffiliateController::class, 'cancelCommission'])->name('commissions.cancel');

// Export
Route::get('/export', [AdminAffiliateController::class, 'exportCsv'])->name('export');

// Gestion des affiliés (routes avec paramètres EN DERNIER)
Route::post('/bulk', [AdminAffiliateController::class, 'bulkAction'])->name('bulk');
Route::get('/create', [AdminAffiliateController::class, 'create'])->name('create');
Route::post('/create', [AdminAffiliateController::class, 'storeAdmin'])->name('store');
Route::get('/', [AdminAffiliateController::class, 'index'])->name('index');
Route::get('/{affiliate}', [AdminAffiliateController::class, 'show'])->name('show');
Route::get('/{affiliate}/edit', [AdminAffiliateController::class, 'edit'])->name('edit');
Route::put('/{affiliate}', [AdminAffiliateController::class, 'update'])->name('update');
Route::delete('/{affiliate}', [AdminAffiliateController::class, 'destroy'])->name('destroy');
Route::post('/{affiliate}/commission', [AdminAffiliateController::class, 'createCommission'])->name('commission.create');
