<?php

use App\Http\Controllers\ForumController;
use App\Http\Controllers\ThreadController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('forums.index');
});

Route::get('/forums', [ForumController::class, 'index'])->name('forums.index');
Route::get('/forums/{forum:slug}', [ForumController::class, 'show'])->name('forums.show');
Route::get('/forums/{forum:slug}/threads/{thread:slug}', [ThreadController::class, 'show'])->name('threads.show');
