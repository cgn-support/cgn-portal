<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('primary_contact_name')->nullable();
            $table->string('primary_contact_email')->nullable()->unique();
            $table->string('primary_contact_phone')->nullable();
            $table->string('primary_contact_title')->nullable();
            $table->string('preferred_comms_method')->nullable()->default('slack');
            $table->date('signing_date')->nullable();
            $table->string('hubspot_company_record')->nullable();
            $table->enum('status', ['active', 'inactive', 'archived', 'on_hold'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
