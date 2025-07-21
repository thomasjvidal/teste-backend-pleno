<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use App\Services\ContactService;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    private $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth('api')->user();
        if (!$user) {
            return response()->json([
                'error' => 'Não autenticado.'
            ], 401);
        }
        try {
            $contacts = $this->contactService->getContactsByUser($user->id);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Contatos listados com sucesso',
                'data' => ContactResource::collection($contacts),
                'meta' => [
                    'total' => $contacts->count(),
                    'user_id' => $user->id,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao listar contatos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactRequest $request)
    {
        $user = auth('api')->user();
        if (!$user) {
            return response()->json([
                'error' => 'Não autenticado.'
            ], 401);
        }
        DB::beginTransaction();
        try {
            $contact = $this->contactService->createContact($request->validated(), $user);
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Contato criado com sucesso',
                'data' => new ContactResource($contact)
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao criar contato',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $contact = $this->contactService->getContact((int) $id);
            return response()->json(new ContactResource($contact));
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao buscar contato',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContactRequest $request, string $id)
    {
        $user = auth('api')->user();
        if (!$user) {
            return response()->json([
                'error' => 'Não autenticado.'
            ], 401);
        }
        if (strtoupper($user->role) !== 'ADMIN') {
            return response()->json([
                'error' => 'Acesso negado. Permissão insuficiente.'
            ], 403);
        }
        DB::beginTransaction();
        try {
            $contact = $this->contactService->getContact((int) $id);
            $contact = $this->contactService->updateContact($contact, $request->validated());
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Contato atualizado com sucesso',
                'data' => new ContactResource($contact)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao atualizar contato',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = auth('api')->user();
        if (!$user) {
            return response()->json([
                'error' => 'Não autenticado.'
            ], 401);
        }
        if (strtoupper($user->role) !== 'ADMIN') {
            return response()->json([
                'error' => 'Acesso negado. Permissão insuficiente.'
            ], 403);
        }
        DB::beginTransaction();
        try {
            $contact = $this->contactService->getContact((int) $id);
            $this->contactService->deleteContact($contact);
            
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Contato deletado com sucesso',
                'data' => [
                    'deleted_contact_id' => $id
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao deletar contato',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
