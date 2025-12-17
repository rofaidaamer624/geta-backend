<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Admin\AuthController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FreeTranslationController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('/admin/login', [AuthController::class, 'login']);
// /partners
Route::get('/partners', [PartnerController::class, 'index']);
Route::post('/partners', [PartnerController::class, 'store']);
Route::post('/partners/{id}', [PartnerController::class, 'update']);
Route::delete('/partners/{id}', [PartnerController::class, 'destroy']);

//services
use App\Http\Controllers\Admin\ServiceController;

// Admin routes
Route::get('/services', [ServiceController::class, 'index']); // GET /admin/services
Route::post('/services', [ServiceController::class, 'store']);
Route::get('/services/{id}', [ServiceController::class, 'show']);    // ←← الجديد
Route::post('/services/{id}', [ServiceController::class, 'update']); // PUT
Route::delete('/services/{id}', [ServiceController::class, 'destroy']);

use App\Http\Controllers\Admin\LanguageController;

// Admin Languages
Route::get('/languages',       [LanguageController::class, 'index']);
Route::get('/languages/{id}',  [LanguageController::class, 'show']);
Route::post('/languages',      [LanguageController::class, 'store']);
Route::post('/languages/{id}', [LanguageController::class, 'update']); // أو put
Route::delete('/languages/{id}', [LanguageController::class, 'destroy']);

Route::post('/contact', [ContactController::class, 'store']);
Route::get('/contact-messages', [ContactController::class, 'index']);
Route::get('/contact-messages/{id}', [ContactController::class, 'show']);
Route::post('/contact-messages/{id}', [ContactController::class, 'update']); // ← أضيفي السطر ده
Route::delete('/contact-messages/{id}', [ContactController::class, 'destroy']);
Route::post('/contact-messages/{id}/read-status', [ContactController::class, 'updateReadStatus']);







// Public (form from website)
Route::post('/free-translation-request', [FreeTranslationController::class, 'store']);
Route::get('/free-translation-requests', [FreeTranslationController::class, 'index']);
Route::get('/free-translation-requests/{id}', [FreeTranslationController::class, 'show']);
Route::post('/free-translation-requests/{id}', [FreeTranslationController::class, 'updateStatus']);
Route::delete('/free-translation-requests/{id}', [FreeTranslationController::class, 'destroy']);

// Route::get('/languages', [LanguageController::class, 'publicIndex']);
