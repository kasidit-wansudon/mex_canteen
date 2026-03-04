<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMealCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meal_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
            $table->foreignId('collector_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('collector_staff_code', 40)->nullable();
            $table->string('collector_name', 190)->nullable();
            $table->unsignedInteger('collected_meal_count')->default(1);
            $table->enum('collection_method', ['qr', 'manual'])->default('qr');
            $table->timestamp('collected_at');
            $table->text('remark')->nullable();
            $table->timestamps();

            $table->unique('reservation_id');
            $table->index(['collected_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meal_collections');
    }
}
