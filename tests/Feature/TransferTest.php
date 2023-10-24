<?php

namespace Tests\Feature;

use App\Enums\TransactionStatus;
use App\Models\Account;
use App\Models\Card;
use App\Models\Fee;
use App\Models\Transaction;
use App\Notifications\CreditDepositedNotification;
use App\Notifications\CreditWithdrewNotification;
use App\Services\Sms\Facades\SMS;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TransferTest extends TestCase
{
    /** @test */
    public function users_can_transfer_credit_between_two_cards()
    {
        SMS::fake();

        $sender = Card::factory()->for(Account::factory()->set('balance', 1_000_000))->create();
        $receiver = Card::factory()->for(Account::factory()->set('balance', 0))->create();

        $this
            ->postJson(route('cards.transfer'), [
                'sender_card_number' => $sender->number,
                'receiver_card_number' => $receiver->number,
                'amount' => 200_000,
            ])
            ->assertOk();

        $this->assertDatabaseHas(Transaction::class, [
            'sender_card_id' => $sender->id,
            'receiver_card_id' => $receiver->id,
            'amount' => 200_000,
            'status' => TransactionStatus::COMPLETED->name
        ]);

        $this->assertDatabaseHas(Account::class, [
            'id' => $sender->account->id,
            'balance' => 1_000_000 - (200_000 + config('fee.amount'))
        ]);

        $this->assertDatabaseHas(Account::class, [
            'id' => $receiver->account->id,
            'balance' => 200_000
        ]);

        $this->assertDatabaseHas(Fee::class, [
            'amount' => config('fee.amount'),
            'transaction_id' => Transaction::query()
                ->where('status', TransactionStatus::COMPLETED->name)
                ->where('amount', 200_000)
                ->whereBelongsTo($sender, 'sender')
                ->whereBelongsTo($receiver, 'receiver')
                ->value('id')
        ]);
    }

    /** @test */
    public function when_users_transfer_credit_between_two_cards_they_will_be_notified()
    {
        Notification::fake();

        $sender = Card::factory()->for(Account::factory()->set('balance', 1_000_000))->create();
        $receiver = Card::factory()->for(Account::factory()->set('balance', 0))->create();

        $this
            ->postJson(route('cards.transfer'), [
                'sender_card_number' => $sender->number,
                'receiver_card_number' => $receiver->number,
                'amount' => 200_000,
            ])
            ->assertOk();

        $transactionId = Transaction::query()
            ->where('status', TransactionStatus::COMPLETED->name)
            ->where('amount', 200_000)
            ->whereBelongsTo($sender, 'sender')
            ->whereBelongsTo($receiver, 'receiver')
            ->valueOrFail('id');

        Notification::assertCount(2);

        Notification::assertSentTo(
            $sender->account->user,
            fn(CreditWithdrewNotification $notification) => $notification->transaction->id === $transactionId
        );

        Notification::assertSentTo(
            $receiver->account->user,
            fn(CreditDepositedNotification $notification) => $notification->transaction->id === $transactionId
        );
    }

    /** @test */
    public function when_users_transfer_credit_between_two_cards_proper_text_messages_are_sent()
    {
       SMS::fake();

        $sender = Card::factory()->for(Account::factory()->set('balance', 1_000_000))->create();
        $receiver = Card::factory()->for(Account::factory()->set('balance', 0))->create();

        $this
            ->postJson(route('cards.transfer'), [
                'sender_card_number' => $sender->number,
                'receiver_card_number' => $receiver->number,
                'amount' => 200_000,
            ])
            ->assertOk();

        SMS::assertSentTo($sender->account->user->phone, '');
        SMS::assertSentTo($receiver->account->user->phone,'' );
    }

    /** @test */
    public function amount_can_not_be_greater_than_500_million_rials()
    {
        [$sender, $receiver] = Card::factory()->count(2)->create();

        $this
            ->postJson(route('cards.transfer'), [
                'sender_card_number' => $sender->number,
                'receiver_card_number' => $receiver->number,
                'amount' => 500_000_001,
            ])
            ->assertInvalid(['amount' => __('transfer.amount.max')]);
    }

    /** @test */
    public function amount_can_not_be_less_than_10_thousand_rials()
    {
        [$sender, $receiver] = Card::factory()->count(2)->create();

        $this
            ->postJson(route('cards.transfer'), [
                'sender_card_number' => $sender->number,
                'receiver_card_number' => $receiver->number,
                'amount' => 9_999,
            ])
            ->assertInvalid(['amount' => __('transfer.amount.min')]);
    }

    /** @test */
    public function users_can_not_transform_money_from_a_card_that_does_not_exist()
    {
        $receiver = Card::factory()->create();

        $this
            ->postJson(route('cards.transfer'), [
                'sender_card_number' => fake()->unique()->creditCardNumber(separator: ''),
                'receiver_card_number' => $receiver->number,
                'amount' => 50000,
            ])
            ->assertInvalid('sender_card_number');
    }

    /** @test */
    public function users_can_not_transform_money_to_a_card_that_does_not_exist()
    {
        $sender = Card::factory()->create();

        $this
            ->postJson(route('cards.transfer'), [
                'sender_card_number' => $sender->number,
                'receiver_card_number' => fake()->unique()->creditCardNumber(separator: ''),
                'amount' => 50000,
            ])
            ->assertInvalid('receiver_card_number');
    }

    /** @test */
    public function sender_and_receiver_cards_cannot_be_the_same()
    {
        $card = Card::factory()->create();

        $this
            ->postJson(route('cards.transfer'), [
                'sender_card_number' => $card->number,
                'receiver_card_number' => $card->number,
                'amount' => 50000,
            ])
            ->assertInvalid(['receiver_card_number' => __('transfer.card.same')]);
    }

    /** @test */
    public function sender_card_must_be_a_valid_iranian_card_number()
    {
        $receiver = Card::factory()->create();

        $this
            ->postJson(route('cards.transfer'), [
                'sender_card_number' => '1234-1234-1234-1234',
                'receiver_card_number' => $receiver->number,
                'amount' => 50000,
            ])
            ->assertInvalid(['sender_card_number' => __('transfer.card.invalid_format')]);
    }

    /** @test */
    public function sender_card_number_can_bepersian_or_arabic_characters()
    {
//TODO
    }

    /** @test */
    public function users_can_not_transfer_credit_when_they_dont_have_enough_credit()
    {
        $sender = Card::factory()->for(Account::factory()->set('balance', 10_000))->create();
        $receiver = Card::factory()->create();

        $this
            ->postJson(route('cards.transfer'), [
                'sender_card_number' => $sender->number,
                'receiver_card_number' => $receiver->number,
                'amount' => 10_000, //sender cannot afford the transfer fee
            ])
            ->assertInvalid(['sender_card_number' => __('transfer.insufficient_balance')]);
    }
}
