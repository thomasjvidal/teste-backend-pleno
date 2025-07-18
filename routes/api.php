<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;

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

// Rotas públicas de autenticação
Route::post('/login', [AuthController::class, 'login']);

// Rota de teste simples
Route::get('/test', function() {
    return response()->json(['message' => 'API funcionando!']);
});

// Rota de debug para contatos (temporária)
Route::get('/debug/contacts', function() {
    try {
        $totalContacts = \App\Models\Contact::count();
        $contacts = \App\Models\Contact::with(['address', 'phones', 'emails'])->get();
        
        return response()->json([
            'debug_info' => [
                'total_contacts' => $totalContacts,
                'database_connection' => 'OK',
                'contacts_data' => $contacts->toArray()
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Rotas protegidas
Route::middleware(['auth:api'])->group(function () {
    // Rotas de autenticação
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Rotas de contatos (todos os usuários autenticados podem acessar)
    Route::get('/contacts', [ContactController::class, 'index']);
    Route::post('/contacts', [ContactController::class, 'store']);
    
    // Rotas administrativas (apenas ADMIN)
    Route::middleware(['checkRole:ADMIN'])->group(function () {
        Route::put('/contacts/{id}', [ContactController::class, 'update']);
        Route::delete('/contacts/{id}', [ContactController::class, 'destroy']);
    });
    
    // Rotas específicas para ADMIN (se necessário no futuro)
    Route::middleware(['checkRole:ADMIN'])->group(function () {
        // Rotas administrativas específicas podem ser adicionadas aqui
    });
});

// Rota padrão do Laravel (mantida para compatibilidade)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
