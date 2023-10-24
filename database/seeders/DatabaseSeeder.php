<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Account;
use App\Models\Card;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory(5)->create();
        $accounts = Account::factory(5)
            ->state(new Sequence(fn(Sequence $sequence) => ['user_id' => $users[$sequence->index]['id']]))
            ->create();
        $cards = Card::factory(5)
            ->state(new Sequence(fn(Sequence $sequence) => ['account_id' => $accounts[$sequence->index]['id']]))
            ->create();

        foreach ([12, 5, 20, 8, 6,] as $index => $transactionCount) {
            Transaction::factory()->count($transactionCount)->create([
                'sender_card_id' => $cards[$index]['id'],
                'receiver_card_id' => $cards[(($index + 1) % 5)]['id'],
                'created_at' => now()->subMinutes(rand(0, 9)),
            ]);
        }
    }
}
