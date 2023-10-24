<?php

use App\Enums\TransactionStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId('sender_card_id')
                ->references('id')
                ->on('cards');
            $table
                ->foreignId('receiver_card_id')
                ->references('id')
                ->on('cards');
            $table->unsignedBigInteger('amount');
            $table->enum('status', TransactionStatus::toArray());
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
