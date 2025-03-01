<?php

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use XBigDaddyx\Gatekeeper\Controllers\ApprovalController;

if ((bool) config('gatekeeper.use_multi_db_tenant')) {
    Route::middleware([
        'signed',
        InitializeTenancyByDomain::class,
        PreventAccessFromCentralDomains::class,
    ])->group(function () {
        Route::get('/gatekeeper/approve/{approvable_type}/{approvable_id}/{user_id}/{tenant_slug}', [ApprovalController::class, 'approve'])
            ->name('gatekeeper.approve');
        Route::get('/gatekeeper/reject/{approvable_type}/{approvable_id}/{user_id}/{tenant_slug}', [ApprovalController::class, 'reject'])
            ->name('gatekeeper.reject');
    });
}
Route::middleware(['signed'])->group(function () {
    Route::get('/gatekeeper/approve/{approvable_type}/{approvable_id}/{user_id}/{tenant_slug}', [ApprovalController::class, 'approve'])
        ->name('gatekeeper.approve');
    Route::get('/gatekeeper/reject/{approvable_type}/{approvable_id}/{user_id}/{tenant_slug}', [ApprovalController::class, 'reject'])
        ->name('gatekeeper.reject');
});
