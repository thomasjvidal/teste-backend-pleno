<?php

namespace App\Services;

use App\Models\Address;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AddressService
{
    /**
     * Criar ou atualizar endereço com integração ViaCEP
     */
    public function createOrUpdateAddress(int $contactId, array $addressData): Address
    {
        // Buscar dados do ViaCEP se apenas CEP foi fornecido
        if (isset($addressData['zip_code']) && !isset($addressData['street_address'])) {
            $viaCepData = $this->getAddressFromViaCep($addressData['zip_code']);
            if ($viaCepData) {
                $addressData = array_merge($addressData, $viaCepData);
            }
        }

        $addressData['id_contact'] = $contactId;

        // Verificar se já existe endereço para este contato
        $address = Address::where('id_contact', $contactId)->first();

        if ($address) {
            $address->update($addressData);
            Log::info('Endereço atualizado', ['contact_id' => $contactId]);
        } else {
            $address = Address::create($addressData);
            Log::info('Endereço criado', ['contact_id' => $contactId]);
        }

        return $address;
    }

    /**
     * Buscar dados de endereço via ViaCEP
     */
    public function getAddressFromViaCep(string $cep): ?array
    {
        try {
            $cep = preg_replace('/[^0-9]/', '', $cep);
            
            $response = Http::timeout(10)->get("https://viacep.com.br/ws/{$cep}/json/");
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (!isset($data['erro'])) {
                    Log::info('Dados do ViaCEP obtidos com sucesso', ['cep' => $cep]);
                    
                    return [
                        'street_address' => $data['logradouro'] ?? null,
                        'neighborhood' => $data['bairro'] ?? null,
                        'city' => $data['localidade'] ?? null,
                        'state' => $data['uf'] ?? null,
                        'country' => 'Brasil',
                        'address_line' => $data['complemento'] ?? null,
                    ];
                }
            }
            
            Log::warning('CEP não encontrado no ViaCEP', ['cep' => $cep]);
            return null;
            
        } catch (\Exception $e) {
            Log::error('Erro ao buscar CEP no ViaCEP', [
                'cep' => $cep,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Deletar endereço
     */
    public function deleteAddress(int $contactId): bool
    {
        $deleted = Address::where('id_contact', $contactId)->delete();
        
        if ($deleted) {
            Log::info('Endereço deletado', ['contact_id' => $contactId]);
        }
        
        return $deleted > 0;
    }
} 