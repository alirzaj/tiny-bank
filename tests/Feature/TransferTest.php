<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Card;
use Tests\TestCase;

class TransferTest extends TestCase
{
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
            ->assertInvalid();
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
            ->assertInvalid(['amount' => __('transfer.insufficient_balance')]);
    }
}