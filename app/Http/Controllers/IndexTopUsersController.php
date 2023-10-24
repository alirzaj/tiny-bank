<?php

namespace App\Http\Controllers;

use App\Actions\GetTopUsersWithRecentTransactionsAction;
use App\Http\Resources\TopUserResource;

class IndexTopUsersController extends Controller
{
    public function __invoke(GetTopUsersWithRecentTransactionsAction $getTopUsersWithRecentTransactionsAction)
    {
        return TopUserResource::collection($getTopUsersWithRecentTransactionsAction());
    }
}
