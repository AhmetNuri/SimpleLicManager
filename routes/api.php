<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\LicenseCheckController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// API V1 Routes
Route::prefix('v1')->group(function () {
    // License check endpoint - Rate limited
    Route::post('/license/check', [LicenseCheckController::class, 'check'])
        ->middleware('throttle:60,1'); // 60 requests per minute
});
