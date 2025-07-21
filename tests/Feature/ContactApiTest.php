<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Contact;
use App\Models\Address;
use App\Models\Phone;
use App\Models\Email;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class ContactApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $user;
    private $admin;
    private $token;
    private $adminToken;

    protected function setUp(): void
    {
        parent::setUp();
        config(['auth.defaults.guard' => 'api']);
        config(['auth.guards.api.driver' => 'jwt']);
        
        // Criar usuário comum
        $this->user = User::create([
            'username' => 'testuser',
            'password' => bcrypt('password'),
            'role' => 'USUAL'
        ]);

        // Criar usuário admin
        $this->admin = User::create([
            'username' => 'admin',
            'password' => bcrypt('password'),
            'role' => 'ADMIN'
        ]);

        // Fazer login e obter tokens
        $this->token = $this->getToken($this->user);
        $this->adminToken = $this->getToken($this->admin);
    }

    private function getToken(User $user): string
    {
        $response = $this->postJson('/api/login', [
            'username' => $user->username,
            'password' => 'password'
        ]);

        return $response->json('access_token');
    }

    /** @test */
    public function user_can_list_their_contacts()
    {
        // Criar contatos para o usuário
        $contact = $this->user->contacts()->create([
            'name' => 'João Silva',
            'description' => 'Cliente importante'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->getJson('/api/contacts');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'message',
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'description',
                            'user_id',
                            'address',
                            'phones',
                            'emails',
                            'created_at',
                            'updated_at'
                        ]
                    ],
                    'meta' => [
                        'total',
                        'user_id'
                    ]
                ]);
    }

    /** @test */
    public function user_can_create_contact()
    {
        $this->actingAs($this->user, 'api');
        $data = [
            'name' => 'Maria Santos',
            'description' => 'Cliente importante',
            'address' => [
                'zip_code' => '12345-678',
                'address_number' => '123',
                'street_address' => 'Rua das Flores'
            ],
            'phones' => [
                ['phone' => '(11) 99999-9999']
            ],
            'emails' => [
                ['email' => 'maria@email.com']
            ]
        ];
        $response = $this->postJson('/api/contacts', $data);
        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Contato criado com sucesso',
            ]);
        $this->assertDatabaseHas('contacts', [
            'name' => 'Maria Santos',
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function admin_can_update_contact()
    {
        $contact = $this->user->contacts()->create([
            'name' => 'João Silva',
            'description' => 'Cliente importante'
        ]);

        $updateData = [
            'name' => 'João Silva Atualizado',
            'description' => 'Cliente muito importante',
            'address' => [
                'zip_code' => '54321-876',
                'address_number' => '456',
                'street_address' => 'Rua das Palmeiras'
            ],
            'phones' => [
                ['phone' => '(11) 88888-8888']
            ],
            'emails' => [
                ['email' => 'joao.atualizado@email.com']
            ]
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->putJson("/api/contacts/{$contact->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'message' => 'Contato atualizado com sucesso'
                ]);

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'name' => 'João Silva Atualizado'
        ]);
    }

    /** @test */
    public function usual_user_cannot_update_contact()
    {
        $this->actingAs($this->user, 'api');
        $contact = $this->user->contacts()->create([
            'name' => 'João Silva',
            'description' => 'Cliente importante'
        ]);

        $updateData = [
            'name' => 'João Silva Atualizado',
            'description' => 'Cliente muito importante',
            'address' => [
                'zip_code' => '54321-876',
                'address_number' => '456',
                'street_address' => 'Rua das Palmeiras'
            ],
            'phones' => [
                ['phone' => '(11) 88888-8888']
            ],
            'emails' => [
                ['email' => 'joao.atualizado@email.com']
            ]
        ];

        $response = $this->putJson("/api/contacts/{$contact->id}", $updateData);

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_delete_contact()
    {
        $this->actingAs($this->admin, 'api');
        $contact = $this->admin->contacts()->create([
            'name' => 'João Silva',
            'description' => 'Cliente importante'
        ]);
        $response = $this->deleteJson("/api/contacts/{$contact->id}");
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Contato deletado com sucesso'
            ]);
        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
        ]);
        $deletedContact = \App\Models\Contact::withTrashed()->find($contact->id);
        $this->assertNotNull($deletedContact->deleted_at);
    }

    /** @test */
    public function usual_user_cannot_delete_contact()
    {
        $this->actingAs($this->user, 'api');
        $contact = $this->user->contacts()->create([
            'name' => 'João Silva',
            'description' => 'Cliente importante'
        ]);

        $response = $this->deleteJson("/api/contacts/{$contact->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_contacts()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer invalidtoken'])->getJson('/api/contacts');
        $response->assertStatus(401);
    }

    /** @test */
    public function contact_validation_works()
    {
        $invalidData = [
            'name' => '', // Nome vazio
            'description' => 'Teste',
            'address' => [
                'zip_code' => 'invalid-cep', // CEP inválido
                'address_number' => '123'
            ],
            'phones' => [
                ['phone' => 'invalid-phone'] // Telefone inválido
            ],
            'emails' => [
                ['email' => 'invalid-email'] // Email inválido
            ]
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->postJson('/api/contacts', $invalidData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'address.zip_code', 'phones.0.phone', 'emails.0.email']);
    }
} 