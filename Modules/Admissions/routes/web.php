<?php

use Illuminate\Support\Facades\Route;
use Modules\Admissions\Http\Controllers\AdmissionsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('admissions', AdmissionsController::class)->names('admissions');
});
