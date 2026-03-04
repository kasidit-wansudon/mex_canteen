<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMealPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meal_plans', function (Blueprint $table) {
            $table->id();
            $table->date('meal_date');
            $table->enum('meal_type', ['lunch', 'dinner']);
            $table->string('menu_item_1', 255);
            $table->string('menu_item_2', 255)->nullable();
            $table->string('menu_item_3', 255)->nullable();
            $table->timestamp('reservation_open_at')->nullable();
            $table->timestamp('reservation_close_at')->nullable();
            $table->timestamp('collection_start_at')->nullable();
            $table->timestamp('collection_end_at')->nullable();
            $table->enum('status', ['published', 'cancelled'])->default('published');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['meal_date', 'meal_type']);
            $table->index(['meal_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meal_plans');
    }
}
