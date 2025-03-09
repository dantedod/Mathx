<?php

use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MainController::class, 'home'])->name('home');

Route::post('/generate-exercisies', [MainController::class, 'generateExercisies'])->name('generateExercisies');

Route::get('/print-exercisies', [MainController::class, 'printExercisies'])->name('printExercisies');

Route::get('/export-exercisies', [MainController::class, 'exportExercisies'])->name('exportExercisies');