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
        Schema::create('clients', function (Blueprint $table) {
            $table->id(); // Primary key

            // Client Company Information
            $table->string('name'); // Legal or official name of the client company
            $table->string('primary_contact_name')->nullable();
            $table->string('primary_contact_email')->nullable()->unique();
            $table->string('primary_contact_phone')->nullable();
            $table->string('primary_contact_title')->nullable();
            $table->string('preferred_comms_method')->nullable()->default('slack');
            $table->string('signing_date');
            $table->string('hubspot_company_record')->nullable()->comment('URL to the company record in HubSpot');

            // Client Status
            $table->enum('status', ['active', 'inactive', 'archived', 'on_hold'])->default('active');

            // Internal Notes
            $table->text('notes')->nullable(); // For internal agency notes about the client contract/relationship

            $table->timestamps(); // created_at and updated_at
            $table->softDeletes(); // If you want to soft delete clients
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
