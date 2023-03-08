<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalendarController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/calendar',[CalendarController::class,'index'])->name('calendar.index');

Route::post('/calendar',[CalendarController::class,'store'])->name('calendar.store');

Route::patch('/calendar/update/{id}',[CalendarController::class,'update'])->name('calendar.update');

Route::delete('/calendar/delete/{id}',[CalendarController::class,'delete'])->name('calendar.destroy');
