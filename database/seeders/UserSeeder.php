<?php

namespace Database\Seeders;

use App\Domain\User\ValueObjects\DocumentType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Joao Silva',
                'email' => 'joao@example.com',
                'document' => '12345678901',
                'document_type' => DocumentType::CPF,
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Maria Souza',
                'email' => 'maria@example.com',
                'document' => '98765432100',
                'document_type' => DocumentType::CPF,
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Loja Central LTDA',
                'email' => 'loja@example.com',
                'document' => '12345678000199',
                'document_type' => DocumentType::CNPJ,
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
