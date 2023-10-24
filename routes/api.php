<?php

use App\Http\Controllers\TransferController;
use Illuminate\Support\Facades\Route;

Route::prefix('cards')->name('cards.')->group(function () {
    Route::post('transfer', TransferController::class)->name('transfer');
});
