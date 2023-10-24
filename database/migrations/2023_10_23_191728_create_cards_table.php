<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId('account_id')
                ->references('id')
                ->on('accounts')
                ->cascadeOnDelete();
            $table->string('number', 16)->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cards');
    }
};
