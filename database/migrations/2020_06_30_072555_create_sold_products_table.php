<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSoldProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sold_products', function (Blueprint $table) {
            $table->id();
            $table->double('quantity')->default(0);
            $table->double('list_price')->default(0);
            $table->double('discount')->default(0);
            $table->foreignId('product_id')->nullable()->constrained();
            $table->foreignId('transaction_id')->nullable()->constrained();
            $table->unique(['product_id', 'transaction_id']);
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
        Schema::dropIfExists('sold_products');
    }
}
