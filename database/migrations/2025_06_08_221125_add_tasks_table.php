<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('project_id')->constrained()->onDelete('cascade');
            $table->string('monday_task_id')->nullable()->unique();
            $table->string('title');
            $table->longText('description')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('due_date')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->json('monday_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
