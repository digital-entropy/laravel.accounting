<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Account full code = {type_code}-{code}.{group_code}
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->integer('code');
            $table->string('description')->nullable();
            $table->string('type_code');
            $table->string('type_description')->nullable();
            $table->string('group_code')->nullable();
            $table->string('group_description')->nullable();
            $table->boolean('is_cash')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('accounts');
    }
}
