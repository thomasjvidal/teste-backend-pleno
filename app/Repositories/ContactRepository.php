<?php

namespace App\Repositories;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Collection;

class ContactRepository
{
    /**
     * Buscar contatos de um usuário com relacionamentos
     */
    public function getContactsByUser(int $userId): Collection
    {
        return Contact::where('user_id', $userId)
            ->whereNull('deleted_at')
            ->with(['address', 'phones', 'emails'])
            ->get();
    }

    /**
     * Buscar contato por ID com relacionamentos
     */
    public function findByIdWithRelations(int $id): ?Contact
    {
        return Contact::with(['address', 'phones', 'emails'])->find($id);
    }

    /**
     * Buscar contato por ID ou falhar
     */
    public function findByIdOrFail(int $id): Contact
    {
        return Contact::where('id', $id)->whereNull('deleted_at')->firstOrFail();
    }

    /**
     * Criar contato
     */
    public function create(array $data): Contact
    {
        return Contact::create($data);
    }

    /**
     * Atualizar contato
     */
    public function update(Contact $contact, array $data): bool
    {
        return $contact->update($data);
    }

    /**
     * Deletar contato
     */
    public function delete(Contact $contact): bool
    {
        return $contact->delete();
    }

    /**
     * Contar contatos de um usuário
     */
    public function countByUser(int $userId): int
    {
        return Contact::where('user_id', $userId)->count();
    }
} 