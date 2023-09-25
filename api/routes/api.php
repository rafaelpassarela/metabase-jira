<?php

use App\Http\Controllers\ApiTestController;
use App\Http\Controllers\IssuesController;
use Database\Factories\IssuesFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
| Swagger Gen: "php artisan l5-swagger:generate" or run "composer swagger" script.
| PS.: You need to run inside "timeline_ci" container, running bash script "./docker-run-phpfpm.sh"
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::get('/test', function () {
//     return response()->json([
//         'code' => 200,
//         'message' => 'This is a Test',
//     ]);
// });
Route::get('/v1/test', [ApiTestController::class, 'makeTest']);

Route::get('/v1/issue/import/{date}', function (IssuesController $controller, string $date) {
    $controller->setDate($date);
    $controller->setConsole(false);
    return $controller->ImportIssues();
});
