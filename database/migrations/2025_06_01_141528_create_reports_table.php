<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('account_manager_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title');
            $table->date('report_date');
            $table->longText('content')->nullable();
            $table->json('metrics_data')->nullable();
            $table->string('file_path')->nullable();
            $table->enum('status', ['draft', 'sent', 'reviewed'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
