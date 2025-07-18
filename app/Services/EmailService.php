<?php

namespace App\Services;

use App\Models\Email;
use Illuminate\Support\Facades\Log;

class EmailService
{
    /**
     * Criar emails para um contato
     */
    public function createEmails(int $contactId, array $emails): void
    {
        foreach ($emails as $emailData) {
            Email::create([
                'id_contact' => $contactId,
                'email' => $emailData['email'],
            ]);
        }

        Log::info('Emails criados', [
            'contact_id' => $contactId,
            'count' => count($emails)
        ]);
    }

    /**
     * Atualizar emails de um contato (deletar e recriar)
     */
    public function updateEmails(int $contactId, array $emails): void
    {
        // Deletar emails existentes
        $this->deleteEmails($contactId);

        // Criar novos emails
        $this->createEmails($contactId, $emails);

        Log::info('Emails atualizados', [
            'contact_id' => $contactId,
            'count' => count($emails)
        ]);
    }

    /**
     * Deletar emails de um contato
     */
    public function deleteEmails(int $contactId): bool
    {
        $deleted = Email::where('id_contact', $contactId)->delete();
        
        if ($deleted > 0) {
            Log::info('Emails deletados', [
                'contact_id' => $contactId,
                'count' => $deleted
            ]);
        }
        
        return $deleted > 0;
    }
} 