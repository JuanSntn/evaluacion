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
        Schema::create('managed_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->text('name');
            $table->text('rfc');
            $table->char('rfc_hash', 64);
            $table->text('address')->nullable();
            $table->text('phone')->nullable();
            $table->text('website')->nullable();
            $table->timestamps();

            $table->unique(['created_by', 'rfc_hash']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('managed_users');
    }
};
