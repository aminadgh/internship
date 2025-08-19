<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('contacts', ContactController::class);
    Route::post('contacts/import-csv', [ContactController::class, 'importCsv'])->name('contacts.importCsv');
    Route::resource('messages', \App\Http\Controllers\MessageController::class)->only(['index', 'create', 'store']);
});

require __DIR__.'/auth.php';

// Admin routes
Route::middleware(['auth', 'is.admin'])->group(function () {
    Route::resource('users', UserController::class);
    // Sanctum: Issue API token
    Route::post('/api/token', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'token_name' => 'required|string|max:255',
        ]);
        $token = $request->user()->createToken($request->token_name);
        return response()->json(['token' => $token->plainTextToken]);
    });
});

Route::middleware(['auth', 'verified', 'is.admin'])->group(function () {
    Route::get('/settings', function () {
        return Inertia::render('SystemSettings');
    })->name('settings');
});
