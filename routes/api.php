<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/* ================================
 | Controllers
 ================================= */
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\LanguageController;

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FreeTranslationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Api\UserController;

/* ================================
 | AUTH
 ================================= */
Route::post('/admin/login', [AuthController::class, 'login']);

Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

/* ================================
 | PARTNERS
 ================================= */
Route::get('/partners', [PartnerController::class, 'publicIndex']);

Route::prefix('admin')->group(function () {
    Route::get('/partners', [PartnerController::class, 'index']);
    Route::get('/partners/{id}', [PartnerController::class, 'show']);
    Route::post('/partners', [PartnerController::class, 'store']);
    Route::post('/partners/{id}', [PartnerController::class, 'update']);
    Route::delete('/partners/{id}', [PartnerController::class, 'destroy']);
});


/* ================================
 | SERVICES (Admin)
 ================================= */
Route::get('/services', [ServiceController::class, 'index']);
Route::post('/services', [ServiceController::class, 'store']);
Route::get('/services/{id}', [ServiceController::class, 'show']);
Route::post('/services/{id}', [ServiceController::class, 'update']);
Route::delete('/services/{id}', [ServiceController::class, 'destroy']);

Route::get('public/services', [ServiceController::class, 'index']);


/* ================================
 | LANGUAGES (Admin)
 ================================= */
Route::get('/languages', [LanguageController::class, 'index']);
Route::get('/languages/{id}', [LanguageController::class, 'show']);
Route::post('/languages', [LanguageController::class, 'store']);
Route::post('/languages/{id}', [LanguageController::class, 'update']);
Route::delete('/languages/{id}', [LanguageController::class, 'destroy']);

/* ================================
 | CONTACT
 ================================= */
Route::post('/contact', [ContactController::class, 'store']);
Route::get('/contact-messages', [ContactController::class, 'index']);
Route::get('/contact-messages/{id}', [ContactController::class, 'show']);
Route::post('/contact-messages/{id}', [ContactController::class, 'update']);
Route::delete('/contact-messages/{id}', [ContactController::class, 'destroy']);
Route::post('/contact-messages/{id}/read-status', [ContactController::class, 'updateReadStatus']);


/* Dashboard */
Route::get('/articles', [ArticleController::class, 'index']);
Route::post('/articles', [ArticleController::class, 'store']);
Route::get('/articles/{id}', [ArticleController::class, 'show']);
Route::put('/articles/{id}', [ArticleController::class, 'update']);
Route::delete('/articles/{id}', [ArticleController::class, 'destroy']);

/* Website */
Route::get('/public/articles', [ArticleController::class, 'publicIndex']);
Route::get('/public/articles/{slug}', [ArticleController::class, 'publicShow']);

/* ================================
 | FREE TRANSLATION
 ================================= */
Route::post('/free-translation-request', [FreeTranslationController::class, 'store']);
Route::get('/free-translation-requests', [FreeTranslationController::class, 'index']);
Route::get('/free-translation-requests/{id}', [FreeTranslationController::class, 'show']);
Route::post('/free-translation-requests/{id}', [FreeTranslationController::class, 'updateStatus']);
Route::delete('/free-translation-requests/{id}', [FreeTranslationController::class, 'destroy']);
