<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('token', 40)->unique();
            $table->string('qr_path')->nullable();
            $table->enum('status', ['ISSUED', 'CHECKED_IN', 'REJECTED'])->default('ISSUED');
            $table->datetime('checked_in_at')->nullable();
            $table->foreignId('checked_in_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('rejected_at')->nullable();
            $table->foreignId('rejected_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('reject_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['event_id', 'status']);
            $table->index('token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
