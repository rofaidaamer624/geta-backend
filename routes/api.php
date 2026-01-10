<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/* ================================
 | Controllers
 ================================= */
// use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\LanguageController;
// use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;

use App\Http\Controllers\AdminAuthController;

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\FreeTranslationController;
use App\Http\Controllers\Auth\PasswordResetController;

/* ================================
 | AUTH
 ================================= *

/* ================================
 | PARTNERS (Public)
 ================================= */

Route::post('/contact', [ContactController::class, 'send']);

 Route::get('/partners', [PartnerController::class, 'publicIndex']);

/* لو partner بيضرب /partner بدل /partners */
Route::get('/partner', [PartnerController::class, 'publicIndex']);


/* ================================
 | PARTNERS (Admin)
 ================================= */
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
 /* ================================
 | SERVICES (Public)
 ================================= */
Route::get('/services', [ServiceController::class, 'index']);

/* لو عايزة تحافظي على ال endpoint القديم */
Route::get('/public/services', [ServiceController::class, 'index']);

Route::prefix('admin')->group(function () {
    Route::get('/services', [ServiceController::class, 'index']);
    Route::post('/services', [ServiceController::class, 'store']);
    Route::get('/services/{id}', [ServiceController::class, 'show']);
    Route::post('/services/{id}', [ServiceController::class, 'update']);
    Route::delete('/services/{id}', [ServiceController::class, 'destroy']);
});


/* ================================
 | LANGUAGES (Admin)
 ================================= */
Route::prefix('admin')->group(function () {
    Route::get('/languages', [LanguageController::class, 'index']);
    Route::get('/languages/{id}', [LanguageController::class, 'show']);
    Route::post('/languages', [LanguageController::class, 'store']);
    Route::post('/languages/{id}', [LanguageController::class, 'update']);
    Route::delete('/languages/{id}', [LanguageController::class, 'destroy']);
});


/* ================================
 | CONTACT
 ================================= */
Route::post('/contact', [ContactController::class, 'store']);

Route::prefix('admin')->group(function () {
    Route::get('/contact-messages', [ContactController::class, 'index']);
    Route::get('/contact-messages/{id}', [ContactController::class, 'show']);
    Route::post('/contact-messages/{id}', [ContactController::class, 'update']);
    Route::delete('/contact-messages/{id}', [ContactController::class, 'destroy']);
    Route::post('/contact-messages/{id}/read-status', [ContactController::class, 'updateReadStatus']);
});


/* ================================
 | ARTICLES
 ================================= */
/* Dashboard */
/* PUBLIC */
Route::prefix('public')->group(function () {
    Route::get('/articles', [ArticleController::class, 'publicIndex']);
    Route::get('/articles/{identifier}', [ArticleController::class, 'publicShowBySlugOrId']);
});

/* ADMIN */

Route::prefix('articles')->group(function () {
    Route::get('/', [ArticleController::class, 'index']); 
    Route::get('{id}', [ArticleController::class, 'show']); 
    Route::post('/', [ArticleController::class, 'store']); 

    // ✅ update
    Route::post('{id}', [ArticleController::class, 'update']); 
    Route::patch('{id}', [ArticleController::class, 'update']); 

    // ✅ delete
    Route::delete('{id}', [ArticleController::class, 'destroy']); 
});



/* Alias - keep old endpoint */
Route::get('/articles', [ArticleController::class, 'publicIndex']);
Route::get('/articles/{identifier}', [ArticleController::class, 'publicShowBySlugOrId']);

/* ================================
 | FREE TRANSLATION
 ================================= */
/* بدل FreeTransController اللي مش موجود */
Route::post('/free-translation', [FreeTranslationController::class, 'store']);

Route::prefix('admin')->group(function () {
    Route::post('/free-translation-request', [FreeTranslationController::class, 'store']);
    Route::get('/free-translation-requests', [FreeTranslationController::class, 'index']);
    Route::get('/free-translation-requests/{id}', [FreeTranslationController::class, 'show']);
    Route::post('/free-translation-requests/{id}', [FreeTranslationController::class, 'updateStatus']);
    Route::delete('/free-translation-requests/{id}', [FreeTranslationController::class, 'destroy']);
});


Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/admin/forgot-password', [AdminAuthController::class, 'forgotPassword']);
Route::post('/admin/reset-password', [AdminAuthController::class, 'resetPassword']);

Route::middleware('auth:admin-api')->group(function () {
    Route::post('/admin/logout', [AdminAuthController::class, 'logout']);

    // ✅ test route
    Route::get('/admin/me', function (Request $request) {
        return response()->json([
            'success' => true,
            'admin' => $request->user()
        ]);
    });
});
