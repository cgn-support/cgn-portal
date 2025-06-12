<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('project_id')->constrained()->onDelete('cascade');
            $table->json('payload'); // Store entire Zoho form submission
            $table->enum('status', ['new', 'valid', 'invalid', 'closed'])->default('new');
            $table->decimal('value', 10, 2)->nullable();
            $table->boolean('is_valid')->default(false);
            $table->text('notes')->nullable();
            $table->datetime('submitted_at')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('referrer_name')->nullable(); // Changed back to referrer_name
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->timestamps();

            // Indexes for common queries
            $table->index(['project_id', 'status']);
            $table->index('submitted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
