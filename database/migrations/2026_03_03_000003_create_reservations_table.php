<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meal_plan_id')->constrained('meal_plans')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('visitor_id')->nullable()->constrained('visitors')->nullOnDelete();
            $table->date('reservation_date');
            $table->enum('meal_type', ['lunch', 'dinner']);
            $table->enum('reservation_type', [
                'self',
                'self_invitation',
                'self_pickup',
                'invitation_only',
                'pickup_only'
            ]);
            $table->unsignedInteger('visitor_count')->default(0);
            $table->string('pickup_for_staff_code', 40)->nullable();
            $table->foreignId('pickup_for_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('meal_count')->default(1);
            $table->string('qr_code_token', 100)->unique();
            $table->timestamp('qr_expiry_time');
            $table->enum('status', ['pending', 'confirmed', 'collected', 'cancelled', 'expired'])->default('confirmed');
            $table->timestamp('collected_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('remark')->nullable();
            $table->timestamps();

            $table->index(['reservation_date', 'meal_type']);
            $table->index(['status', 'reservation_date']);
            $table->index(['user_id', 'reservation_date']);
            $table->index(['visitor_id', 'reservation_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservations');
    }
}
