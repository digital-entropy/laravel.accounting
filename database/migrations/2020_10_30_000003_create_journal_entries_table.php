<?php

use DigitalEntropy\Accounting\Contracts\Journal\Entry;
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
            $table->foreignIdFor(config('accounting.models.journal'))->constrained()->cascadeOnDelete();
            $table->foreignIdFor(config('accounting.models.account'))->constrained()->restrictOnDelete();
            $table->unsignedBigInteger('amount')->default(0);
            $table->morphs('author');
            $table->enum('type', [Entry::TYPE_CREDIT, Entry::TYPE_DEBIT]);
            $table->string('memo')->nullable();
            $table->string('ref')->nullable();
            $table->dateTime('date');
            $table->softDeletes();
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
