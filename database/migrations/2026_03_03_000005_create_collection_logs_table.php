<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollectionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collection_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->nullable()->constrained('reservations')->nullOnDelete();
            $table->foreignId('scanner_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('scanner_staff_code', 40)->nullable();
            $table->string('qr_code_token', 100)->nullable();
            $table->enum('scan_result', ['pass', 'failed']);
            $table->string('failure_reason', 120)->nullable();
            $table->unsignedInteger('meal_count')->default(0);
            $table->json('scan_payload')->nullable();
            $table->timestamp('scanned_at');
            $table->timestamps();

            $table->index(['scanned_at']);
            $table->index(['scan_result', 'failure_reason']);
            $table->index(['qr_code_token']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('collection_logs');
    }
}
