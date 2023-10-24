<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Card;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class IndexTopUsersTest extends TestCase
{
    public function test_fetch_top_users_with_most_transactions()
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

        $getRecentTransactions = function (Card $card) {
            return Transaction::query()
                ->select('id', 'status', 'amount')
                ->whereBelongsTo($card, 'sender')
                ->latest()
                ->latest('id')
                ->take(10)
                ->get();
        };

        $this
            ->getJson(route('users.top.index'))
            ->assertOk()
            ->assertJson(fn(AssertableJson $response) => $response
                ->has('data', 3)
                ->has('data', fn(AssertableJson $data) => $data
                    ->where('0.id', $users[2]['id'])
                    ->where('0.transactions_count', 20)
                    ->where('0.transactions', $getRecentTransactions($cards[2]))

                    ->where('1.id', $users[0]['id'])
                    ->where('1.transactions_count', 12)
                    ->where('1.transactions', $getRecentTransactions($cards[0]))

                    ->where('2.id', $users[3]['id'])
                    ->where('2.transactions_count', 8)
                    ->where('2.transactions', $getRecentTransactions($cards[3]))
                )
            );
    }
}
