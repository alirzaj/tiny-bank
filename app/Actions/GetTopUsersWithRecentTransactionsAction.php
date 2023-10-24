<?php

namespace App\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GetTopUsersWithRecentTransactionsAction
{
    public function __invoke(): Collection
    {
        return DB::table('TopUsers')
            ->withExpression(
                'TransactionCounts',
                DB::table('users')
                    ->select('users.id AS user_id')
                    ->selectRaw('COUNT(transactions.id) OVER (PARTITION BY users.id) AS transaction_count')
                    ->join('accounts', 'users.id', '=', 'accounts.user_id')
                    ->join('cards', 'accounts.id', '=', 'cards.account_id')
                    ->join('transactions', 'cards.id', '=', 'transactions.sender_card_id')
                    ->where('transactions.created_at', '>=', now()->subMinutes(10))
            )
            ->withExpression(
                'TopUsers',
                DB::table('TransactionCounts')
                    ->select('user_id', 'transaction_count')
                    ->distinct()
                    ->orderByDesc('transaction_count')
                    ->limit(3)
            )
            ->withExpression(
                'LastTransactions',
                DB::table('users')
                    ->select(
                        'users.id AS user_id',
                        'transactions.id AS transaction_id',
                        'transactions.amount',
                        'transactions.status',
                        'transactions.created_at',
                    )
                    ->selectRaw('ROW_NUMBER() OVER (PARTITION BY users.id ORDER BY transactions.created_at, transactions.id DESC) AS number')
                    ->join('accounts', 'users.id', '=', 'accounts.user_id')
                    ->join('cards', 'accounts.id', '=', 'cards.account_id')
                    ->join('transactions', 'cards.id', '=', 'transactions.sender_card_id')
                    ->whereIn('users.id', DB::table('TopUsers')->select('user_id'))
                    ->orderBy('users.id')
                    ->orderByDesc('transactions.created_at')
            )
            ->leftJoin('LastTransactions', 'TopUsers.user_id', '=', 'LastTransactions.user_id')
            ->where('LastTransactions.number', '<=', 10)
            ->orderByDesc('TopUsers.transaction_count')
            ->orderBy('LastTransactions.number')
            ->get()
            ->groupBy('user_id')
            ->map(fn(Collection $userTransactions, int $userId) => [
                'id' => $userId,
                'transactions_count' => $userTransactions[0]->transaction_count,
                'transactions' => $userTransactions
            ]);
    }
}
