<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incomes', function (Blueprint $table) {
            
            $table->id();

            $table->unsignedBigInteger('customer_id');
            $table->string('description')->nullable(false);
            $table->double('amount', 11, 2)->nullable(false);
            $table->dateTime('income_date')->nullable(false);
            $table->string('tax_year', 4);
            $table->string('income_file_path');

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incomes');
    }
}
