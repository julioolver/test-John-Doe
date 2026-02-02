<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;

class WalletSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            Wallet::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'balance' => 100000, // R$ 1.000,00 em centavos
                ]
            );
        }
    }
}
