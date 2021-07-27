<?php

use App\Http\Controllers\ExcelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('test-job', [ExcelController::class, 'test_job']);
Route::get('roro-sheets', [ExcelController::class, 'roro_sheets']);
Route::get('excel', [ExcelController::class, 'excel']);
Route::get('merge', [ExcelController::class, 'mergeExternalSheet']);
Route::get('merge2', [ExcelController::class, 'mergeExternalInOne']);
Route::get('merge-test', [ExcelController::class, 'mergeExternalInOne_test']);
Route::get('merge-test2', [ExcelController::class, 'mergeExternalInOne_styled']);
