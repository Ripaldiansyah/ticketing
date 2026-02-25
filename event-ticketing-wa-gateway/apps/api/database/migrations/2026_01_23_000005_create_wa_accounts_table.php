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
        Schema::create('wa_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Customer Service 1"
            $table->string('session_id')->unique(); // Unique ID sent to gateway
            $table->string('number')->nullable(); // Set after connected
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('wa_account_id')->nullable()->constrained('wa_accounts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['wa_account_id']);
            $table->dropColumn('wa_account_id');
        });

        Schema::dropIfExists('wa_accounts');
    }
};
