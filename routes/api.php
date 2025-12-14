<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CertificateController;
use App\Http\Controllers\Api\CertificateTemplateController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\ParticipantController;
use App\Http\Controllers\Api\VerificationController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::get('/verify/{certificateId}', [VerificationController::class, 'verify']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Event routes
    Route::apiResource('events', EventController::class);

    // Participant routes
    Route::prefix('events/{event}')->group(function () {
        Route::get('/participants', [ParticipantController::class, 'index']);
        Route::post('/participants', [ParticipantController::class, 'store']);
        Route::post('/participants/upload-csv', [ParticipantController::class, 'uploadCsv']);
        Route::post('/participants/bulk-delete', [ParticipantController::class, 'bulkDestroy']);
    });
    Route::apiResource('participants', ParticipantController::class)->except(['index', 'store']);

    // Certificate routes
    Route::prefix('events/{event}')->group(function () {
        Route::post('/certificates/generate', [CertificateController::class, 'generateForEvent']);
    });
    Route::prefix('participants/{participant}')->group(function () {
        Route::post('/certificate/generate', [CertificateController::class, 'generateForParticipant']);
    });
    Route::get('/certificates/{certificate}/download', [CertificateController::class, 'download']);

    // Certificate Template routes
    Route::apiResource('certificate-templates', CertificateTemplateController::class);
    Route::get('/certificate-templates/defaults/list', [CertificateTemplateController::class, 'getDefaults']);
});

