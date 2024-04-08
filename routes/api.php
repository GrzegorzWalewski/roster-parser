<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventsController;

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

Route::get('/events', [EventsController::class, 'getEvents']);
// Route::get('/flights/next-week', 'EventController@getFlightsForNextWeek');
// Route::get('/standby/next-week', 'EventController@getStandbyForNextWeek');
// Route::get('/flights/{location}', 'EventController@getFlightsByLocation');
Route::post('/roster/upload', [EventsController::class, 'uploadRoster']);