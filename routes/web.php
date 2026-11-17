<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

// Главная страница со списком задач
Route::get('/', [TaskController::class, 'index'])->name('tasks.index');

// Сохранение новой задачи
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');

// Обновление существующей задачи
Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');

// Удаление задачи
Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');