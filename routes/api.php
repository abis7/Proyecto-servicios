<?php

use App\Http\Controllers\api\userController;
use App\Http\Controllers\api\ProductosController;
use App\Http\Controllers\api\pokemonesController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Ruta para pruebas
Route::post('hola', function(){
    return 'Hello World!';
});

// Ruta para login de usuario
Route::post('user/login', [userController::class, 'login']);
Route::post('user', [userController::class, 'create']); // Crear un nuevo usuario

// Rutas protegidas por middleware de autenticación
Route::group(['middleware' => ['auth:sanctum']], function() {

    // Rutas para usuarios
    Route::prefix('usuario')->group(function() {
        Route::get('', [userController::class, 'index']); // Obtener todos los usuarios
        Route::post('', [userController::class, 'store']); // Crear un nuevo usuario
        Route::get('/{id}', [userController::class, 'show'])->where('id', '[0-9]+'); // Mostrar un usuario por ID
        Route::patch('/{id}', [userController::class, 'update'])->where('id', '[0-9]+'); // Actualizar un usuario por ID
        Route::delete('/{id}', [userController::class, 'destroy'])->where('id', '[0-9]+'); // Eliminar un usuario por ID
    });

    // Rutas para productos
    Route::prefix('pokemones')->group(function() {
        Route::get('', [pokemonesController::class, 'index']); // Obtener todos los pokemones
        Route::post('', [pokemonesController::class, 'store']); // Crear un nuevo pokemon
        Route::get('/{id}', [pokemonesController::class, 'show'])->where('id', '[0-9]+'); // Mostrar un pokemon por ID
        Route::patch('/{id}', [pokemonesController::class, 'update'])->where('id', '[0-9]+'); // Actualizar un pokemon por ID
        Route::delete('/{id}', [pokemonesController::class, 'destroy'])->where('id', '[0-9]+'); // Eliminar un pokemon por ID
    });
    
});

// Obtener usuario autenticado
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');