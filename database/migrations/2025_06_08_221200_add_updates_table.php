<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('updates', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->longText('content');
            $table->enum('type', ['general', 'milestone', 'issue', 'announcement'])->default('general');
            $table->boolean('is_public')->default(true);
            $table->datetime('scheduled_for')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('updates');
    }
};
