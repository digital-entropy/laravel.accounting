<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financial_statements', function (Blueprint $table) {
            $table->string('code')->primary();
            $table->string('name', 100);
            $table->timestamps();
        });

        Schema::create('financial_statements_accounts', function (Blueprint $table) {
            $table->string('account_type_code', 2);
            $table->string('financial_statement_code');
            $table->timestamps();

            $table->primary(['account_type_code', 'financial_statement_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('financial_statements_accounts');
        Schema::drop('financial_statements');
    }
}
