<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['api', 'guest'])
    ->group(function () {
        Route::post('/login', [AuthController::class, 'store']);
        Route::post('/register', [UserController::class, 'store']);
    });


Route::middleware('auth:sanctum')
    ->group(function () {
        Route::get('/me', function (Request $request) {
            return [
                'success' => true,
                'data' => $request->user(),
                'errors' => null,
                'message' => 'OK'
            ];
        });

        Route::prefix('organizations')
            ->controller(OrganizationController::class)
            ->group(function() {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{organization}', 'show');
                Route::put('/{organization}', 'update');
                Route::delete('/{organization}', 'destroy');

                Route::prefix('/{organization}/members')
                    ->controller(MemberController::class)
                    ->group(function() {
                        Route::get('/', 'index');
                        Route::post('/', 'store');
                        Route::delete('/', 'destroy');
                    });

                Route::prefix('/{organization}/categories')
                    ->controller(CategoryController::class)
                    ->group(function() {
                        Route::get('/', 'index');
                        Route::post('/', 'store');
                        Route::get('/{category}', 'show');
                        Route::put('/{category}', 'update');
                        Route::delete('/{category}', 'destroy');
                    });

                Route::prefix('/{organization}/transactions')
                    ->controller(TransactionController::class)
                    ->group(function() {
                        Route::get('/', 'index');
                        Route::post('/', 'store');
                        Route::get('/{transaction}', 'show');
                        Route::put('/{transaction}', 'update');
                        Route::delete('/{transaction}', 'destroy');
                    });
            });
    });
