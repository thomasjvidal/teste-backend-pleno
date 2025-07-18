<?php

namespace App\Services;

use App\Models\Phone;
use Illuminate\Support\Facades\Log;

class PhoneService
{
    /**
     * Criar telefones para um contato
     */
    public function createPhones(int $contactId, array $phones): void
    {
        foreach ($phones as $phoneData) {
            Phone::create([
                'id_contact' => $contactId,
                'phone' => $phoneData['phone'],
            ]);
        }

        Log::info('Telefones criados', [
            'contact_id' => $contactId,
            'count' => count($phones)
        ]);
    }

    /**
     * Atualizar telefones de um contato (deletar e recriar)
     */
    public function updatePhones(int $contactId, array $phones): void
    {
        // Deletar telefones existentes
        $this->deletePhones($contactId);

        // Criar novos telefones
        $this->createPhones($contactId, $phones);

        Log::info('Telefones atualizados', [
            'contact_id' => $contactId,
            'count' => count($phones)
        ]);
    }

    /**
     * Deletar telefones de um contato
     */
    public function deletePhones(int $contactId): bool
    {
        $deleted = Phone::where('id_contact', $contactId)->delete();
        
        if ($deleted > 0) {
            Log::info('Telefones deletados', [
                'contact_id' => $contactId,
                'count' => $deleted
            ]);
        }
        
        return $deleted > 0;
    }
} 