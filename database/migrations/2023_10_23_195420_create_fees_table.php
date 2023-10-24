<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('fees', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId('transaction_id')
                ->references('id')
                ->on('transactions');
            $table->unsignedBigInteger('amount');
            $table->timestamp('created_at')->nullable()->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fees');
    }
};
