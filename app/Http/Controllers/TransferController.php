<?php

namespace App\Http\Controllers;

use App\Actions\TransferCreditAction;
use App\Http\Requests\TransferRequest;
use App\Models\Card;

class TransferController extends Controller
{
    public function __invoke(TransferRequest $request, TransferCreditAction $transferCreditAction)
    {
        $sender = Card::query()
            ->where('number', $request->input('sender_card_number'))
            ->firstOrFail();

        $receiver = Card::query()
            ->where('number', $request->input('receiver_card_number'))
            ->firstOrFail();

        $transferCreditAction($sender, $receiver, $request->integer('amount'));

        return response()->json();
    }
}
