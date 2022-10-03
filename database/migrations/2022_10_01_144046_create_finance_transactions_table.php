<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinanceTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('finance_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("user_id")->unsigned();
            $table->string("name");
            $table->integer("finance_account_id")->unsigned()->nullable();
            $table->double("amount");
            $table->text("description");
            $table->softDeletes();
            $table->timestamps();

            
            $table->foreign("finance_account_id")
                ->references("id")
                ->on("finance_accounts")
                ->onDelete("restrict");

                
            $table->foreign("user_id")
                ->references("id")
                ->on("users")
                ->onDelete("restrict");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('finance_transactions');
    }
}
