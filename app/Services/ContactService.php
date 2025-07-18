<?php

namespace App\Services;

use App\Models\Contact;
use App\Repositories\ContactRepository;
use App\Services\AddressService;
use App\Services\PhoneService;
use App\Services\EmailService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ContactService
{
    private $contactRepository;
    private $addressService;
    private $phoneService;
    private $emailService;

    public function __construct(
        ContactRepository $contactRepository,
        AddressService $addressService,
        PhoneService $phoneService,
        EmailService $emailService
    ) {
        $this->contactRepository = $contactRepository;
        $this->addressService = $addressService;
        $this->phoneService = $phoneService;
        $this->emailService = $emailService;
    }

    /**
     * Criar um novo contato com endereço, telefones e emails
     */
    public function createContact(array $data, $user): Contact
    {
        DB::beginTransaction();
        
        try {
            // Criar o contato associado ao usuário
            $contact = $user->contacts()->create([
                'name' => $data['name'],
                'description' => $data['description'],
            ]);

            // Processar endereço com integração ViaCEP
            $this->addressService->createOrUpdateAddress($contact->id, $data['address']);

            // Processar telefones
            $this->phoneService->createPhones($contact->id, $data['phones']);

            // Processar emails
            $this->emailService->createEmails($contact->id, $data['emails']);

            DB::commit();

            Log::info('Contato criado com sucesso', ['contact_id' => $contact->id]);

            return $this->contactRepository->findByIdWithRelations($contact->id);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar contato', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Atualizar um contato existente
     */
    public function updateContact(Contact $contact, array $data): Contact
    {
        DB::beginTransaction();
        
        try {
            // Atualizar dados básicos do contato
            $this->contactRepository->update($contact, [
                'name' => $data['name'],
                'description' => $data['description'],
            ]);

            // Atualizar endereço
            $this->addressService->createOrUpdateAddress($contact->id, $data['address']);

            // Atualizar telefones (deletar e recriar)
            $this->phoneService->updatePhones($contact->id, $data['phones']);

            // Atualizar emails (deletar e recriar)
            $this->emailService->updateEmails($contact->id, $data['emails']);

            DB::commit();

            Log::info('Contato atualizado com sucesso', ['contact_id' => $contact->id]);

            return $this->contactRepository->findByIdWithRelations($contact->id);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar contato', [
                'contact_id' => $contact->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Deletar um contato e seus relacionamentos
     */
    public function deleteContact(Contact $contact): bool
    {
        DB::beginTransaction();
        
        try {
            // Deletar relacionamentos
            $this->addressService->deleteAddress($contact->id);
            $this->phoneService->deletePhones($contact->id);
            $this->emailService->deleteEmails($contact->id);

            // Deletar o contato
            $this->contactRepository->delete($contact);

            DB::commit();

            Log::info('Contato deletado com sucesso', ['contact_id' => $contact->id]);

            return true;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao deletar contato', [
                'contact_id' => $contact->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Buscar um contato específico
     */
    public function getContact(int $id): Contact
    {
        $contact = $this->contactRepository->findByIdOrFail($id);
        
        Log::info('Contato buscado', ['contact_id' => $id]);
        
        return $contact;
    }

    /**
     * Listar contatos de um usuário
     */
    public function getContactsByUser(int $userId)
    {
        $contacts = $this->contactRepository->getContactsByUser($userId);
        
        Log::info('Lista de contatos buscada', ['user_id' => $userId, 'count' => $contacts->count()]);
        
        return $contacts;
    }
}
