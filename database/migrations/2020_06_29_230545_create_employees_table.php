<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->unique()->constrained()->onDelete('cascade');
            $table->boolean('is_active')->default(false);
            $table->foreignId('manager_id')->nullable()->constrained('employees');
            $table->timestamps();
            $table->unique(['user_id', 'manager_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
}
