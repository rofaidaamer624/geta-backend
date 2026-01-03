<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;


use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

Route::get('/files/articles/{filename}', function ($filename) {
    $path = storage_path('app/public/articles/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $mime = File::mimeType($path) ?? 'application/octet-stream';
    return Response::file($path, [
        'Content-Type' => $mime,
        'Cache-Control' => 'public, max-age=86400',
    ]);
});


Route::get('/files/partners/{filename}', function ($filename) {
    $path = storage_path('app/public/partners/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    return response()->file($path);
});



/*

|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Route::get('/', function () {
//     return file_get_contents(public_path('index.html'));
// });

// Route::get('/{any}', function () {
//     return file_get_contents(public_path('index.html'));
// })->where('any', '.*');

Route::get('/', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API is running'
    ]);
});

Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');