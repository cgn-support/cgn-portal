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
        Schema::create('businesses', function (Blueprint $table) {
            $table->id(); // Primary key

            // Foreign key to link to the client company
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');

            // Business Information (public-facing)
            $table->string('name'); // e.g., "The Downtown Diner"
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('country')->nullable()->default('USA');
            $table->string('phone_number')->nullable();
            $table->string('website_url')->nullable();
            $table->string('google_maps_url')->nullable();
            $table->string('gbp_listing_id')->nullable(); // Google Business Profile Listing ID
            $table->string('industry')->nullable();

            $table->string('slack_channel_id')->nullable(); // For notifications or direct linking

            // Internal Notes for this specific business
            $table->text('notes')->nullable();

            $table->timestamps(); // created_at and updated_at
            $table->softDeletes(); // If you want to soft delete businesses
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
