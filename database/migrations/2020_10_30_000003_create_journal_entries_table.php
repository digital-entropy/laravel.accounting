<?php

use DigitalEntropy\Accounting\Entities\Account;
use DigitalEntropy\Accounting\Entities\Journal;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJournalEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Journal::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Account::class)->constrained()->restrictOnDelete();
            $table->unsignedBigInteger('amount')->default(0);
            $table->enum('type', [Journal::TYPE_CREDIT, Journal::TYPE_DEBIT]);
            $table->string('memo')->nullable();
            $table->string('ref')->nullable();
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
        Schema::drop('journal_entries');
    }
}
