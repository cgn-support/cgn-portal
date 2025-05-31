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
        Schema::create('leads', function (Blueprint $table) {
            $table->id(); // Primary key

            // Fields from Zoho Forms Payload
            $table->string('phone')->nullable();
            $table->string('project_id'); // References projects.id which is a UUID
            $table->string('utm_medium')->nullable();
            $table->text('referrer_name')->nullable(); // Using text in case the URL is very long
            $table->string('session_id')->nullable()->index(); // Added index as session_id might be used for lookups/grouping
            $table->string('last_name')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('initial_referrer')->nullable();
            $table->timestamp('submitted_at')->nullable(); // To store 'time_submitted'
            $table->string('first_name')->nullable();
            $table->string('email')->nullable()->index(); // Added index as email is often queried
            $table->string('utm_source')->nullable();
            $table->text('payload_data')->nullable(); // To store the full JSON payload for auditing or future use

            // Additional fields for lead management (as discussed)
            $table->enum('status', ['new', 'contacted', 'qualified', 'lost', 'estimate', 'converted', 'closed'])->default('new');
            $table->decimal('value', 10, 2)->nullable(); // Example: 10 total digits, 2 decimal places
            $table->boolean('is_valid')->default(false);
            $table->text('notes')->nullable(); // For internal notes or messages from the form if available

            // Standard timestamps
            $table->timestamps(); // `created_at` and `updated_at`

            // Foreign key constraint (assuming your projects table is named 'projects')
            // Ensure your 'projects' table is created before this migration runs,
            // or defer foreign key creation.
            //            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            // Consider onDelete('set null') if you want to keep leads even if a project is deleted,
            // but 'cascade' is common if leads are intrinsically tied to a project.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
