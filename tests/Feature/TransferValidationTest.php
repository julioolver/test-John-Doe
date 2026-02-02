<?php

namespace Tests\Feature;

use App\Domain\User\ValueObjects\DocumentType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TransferValidationTest extends TestCase
{
    use RefreshDatabase;

    public function testTransferCannotBeToSameUser(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'document' => '12345678901',
            'document_type' => DocumentType::CPF,
        ]);

        $response = $this->postJson('/api/transfers', [
            'value' => 10.00,
            'payer' => $user->id,
            'payee' => $user->id,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['payee']);
    }
}
