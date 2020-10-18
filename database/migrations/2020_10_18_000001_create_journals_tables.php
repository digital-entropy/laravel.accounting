<?php

use DigitalEntropy\Accounting\Entities\Journal;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJournalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->nullableMorphs('tenant');
            $table->string('code', 4);
            $table->string('description', 20)->nullable();
            $table->string('type', 20);
            $table->boolean('is_cash')->default(false);
            $table->timestamps();
        });

        Schema::create('journals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('amount')->default(0);
            $table->string('memo', 150);
            $table->string('ref', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('journals_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigIncrements('journal_id');
            $table->bigIncrements('account_id');
            $table->unsignedBigInteger('amount')->default(0);
            $table->enum('type', [Journal::TYPE_CREDIT, Journal::TYPE_DEBIT]);
            $table->string('memo', 150)->nullable();
            $table->string('ref', 100)->nullable();
            $table->timestamps();

            $table->foreign('journal_id')->references('id')->on('journals');
            $table->foreign('account_id')->references('id')->on('accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('journals_entries');
        Schema::drop('journals');
        Schema::drop('accounts');
    }
}
