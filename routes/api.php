<?php

use App\Http\Controllers\API\LeadWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/lead-collect', [LeadWebhookController::class, 'handle'])->name('lead-collect');
