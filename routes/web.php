<?php

use App\Models\Post;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('users', 'pages.users.index')->name('users.index');
    Route::view('posts', 'pages.posts.index')->name('posts.index');
    Route::view('tags', 'pages.tags.index')->name('tags.index');
});

require __DIR__.'/settings.php';
