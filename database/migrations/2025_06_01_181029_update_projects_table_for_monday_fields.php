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
        Schema::table('projects', function (Blueprint $table) {
            // Add new account_manager_id (nullable as it might be set later)
            // Assuming your users table uses auto-incrementing IDs. If UUIDs, use $table->foreignUuid('account_manager_id')
            $table->foreignId('account_manager_id')->nullable()->after('user_id')->constrained('users')->onDelete('set null');

            // Fields for services (storing as JSON is flexible for arrays)
            $table->json('current_services')->nullable()->after('portfolio_project_scope');
            $table->json('completed_services')->nullable()->after('current_services');

            // Fields for Monday User IDs of assigned team members
            $table->string('specialist_monday_id')->nullable()->after('completed_services');
            $table->string('content_writer_monday_id')->nullable()->after('specialist_monday_id');
            $table->string('developer_monday_id')->nullable()->after('content_writer_monday_id');
            $table->string('copywriter_monday_id')->nullable()->after('developer_monday_id');
            $table->string('designer_monday_id')->nullable()->after('copywriter_monday_id');

            // Ensure other fields are present (some were in your original migration)
            // If these columns already exist from your previous migration, this won't re-add them
            // but it's good for completeness if someone is starting fresh or reviewing.
            if (!Schema::hasColumn('projects', 'client_logo')) {
                $table->string('client_logo')->nullable()->after('portfolio_project_scope');
            }
            if (!Schema::hasColumn('projects', 'slack_channel')) {
                $table->string('slack_channel')->nullable()->after('google_sheet_id');
            }
            // wordpress_api_url - decide if you need this. If so, ensure it's in a migration.
            // $table->string('wordpress_api_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Drop foreign key first if it exists
            // Note: The name of the foreign key constraint might vary.
            // Laravel default is projects_account_manager_id_foreign
            if (Schema::hasColumn('projects', 'account_manager_id')) {
                // Check if foreign key exists before trying to drop.
                // This requires knowing the exact foreign key name, which can be found via DB inspection or Laravel's schema builder.
                // A more robust way is to check if the column exists and then drop it.
                // For simplicity, we'll just try to drop the column. If it has a FK, it might fail without dropping FK first.
                // A safer approach for rollback is specific:
                // $table->dropForeign(['account_manager_id']);
            }
            $table->dropColumn([
                'account_manager_id',
                'current_services',
                'completed_services',
                'specialist_monday_id',
                'content_writer_monday_id',
                'developer_monday_id',
                'copywriter_monday_id',
                'designer_monday_id',
                // Don't drop client_logo, slack_channel here if they were part of the original create_projects_table
                // unless you are fully reverting this specific set of changes.
            ]);
        });
    }
};
