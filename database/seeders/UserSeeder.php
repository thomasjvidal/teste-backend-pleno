<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar usuário admin
        User::create([
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'ADMIN',
        ]);

        // Criar usuário comum
        User::create([
            'username' => 'user',
            'password' => Hash::make('password'),
            'role' => 'USUAL',
        ]);

        // Criar usuário de teste
        User::create([
            'username' => 'testuser',
            'password' => Hash::make('password'),
            'role' => 'USUAL',
        ]);

        $this->command->info('Usuários criados com sucesso!');
        $this->command->info('Admin: admin/password');
        $this->command->info('Usuário: user/password');
        $this->command->info('Teste: testuser/password');
    }
}
