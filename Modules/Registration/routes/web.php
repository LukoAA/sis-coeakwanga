<?php

use Illuminate\Support\Facades\Route;
use Modules\Registration\Http\Controllers\RegistrationController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('registrations', RegistrationController::class)->names('registration');
});
