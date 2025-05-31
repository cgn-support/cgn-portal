<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->index();
            $table->foreignId('business_id')->index();
            $table->foreignId('plan_id')->nullable()->index();
            $table->string('monday_pulse_id')->nullable();
            $table->string('monday_board_id')->nullable();
            $table->string('portfolio_project_rag')->nullable();
            $table->json('portfolio_project_doc')->nullable();
            $table->string('portfolio_project_scope')->nullable();
            $table->string('client_logo')->nullable();
            $table->string('google_sheet_id')->nullable();
            $table->string('slack_channel')->nullable();
            $table->string('bright_local_url')->nullable();
            $table->date('project_start_date')->nullable();
            $table->string('project_url')->nullable();
            $table->string('google_drive_folder')->nullable();
            $table->string('my_maps_share_link')->nullable();
            $table->string('wp_umbrella_project_id')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
