<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ThreadController;
use App\Models\Forum;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('forums.index');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisterController::class, 'create'])->name('register');
    Route::post('register', [RegisterController::class, 'store']);

    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
    
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

// Search Route
Route::get('/search', [App\Http\Controllers\SearchController::class, 'index'])->name('search');

// Members Route
Route::get('/members', [App\Http\Controllers\MembersController::class, 'index'])->name('members.index');

// Forum Routes
Route::get('/forums', [ForumController::class, 'index'])->name('forums.index');
Route::get('/forums/{forum:slug}', [ForumController::class, 'show'])->name('forums.show');

// Thread Routes (Auth Required)
Route::middleware('auth')->group(function () {
    Route::get('/forums/{forum:slug}/threads/create', [ThreadController::class, 'create'])->name('threads.create');
    Route::post('/forums/{forum:slug}/threads', [ThreadController::class, 'store'])->name('threads.store');
    
    // Post Actions
    Route::post('/posts/{post}/like', [PostController::class, 'like'])->name('posts.like');
    Route::post('/posts/{post}/reply', [PostController::class, 'reply'])->name('posts.reply');
});

// This MUST come after threads/create to avoid conflicts
Route::get('/forums/{forum:slug}/threads/{thread:slug}', [ThreadController::class, 'show'])->name('threads.show');

