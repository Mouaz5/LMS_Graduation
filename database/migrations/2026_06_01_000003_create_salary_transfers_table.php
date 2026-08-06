<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('usd');
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
            $table->string('stripe_transfer_id')->nullable();
            $table->date('transfer_date');
            $table->string('description')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['teacher_user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_transfers');
    }
};
