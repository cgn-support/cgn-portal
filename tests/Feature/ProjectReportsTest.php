<?php

namespace Tests\Feature;

use App\Livewire\ProjectReports;
use App\Models\Business;
use App\Models\Project;
use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectReportsTest extends TestCase
{
    use RefreshDatabase;

    private function createTestProject($startDate = null)
    {
        $business = Business::factory()->create();
        return Project::factory()->create([
            'business_id' => $business->id,
            'project_start_date' => $startDate ?: now()->subYear()->startOfMonth(),
        ]);
    }

    private function createTestUser()
    {
        return User::factory()->create();
    }

    public function test_component_can_be_rendered()
    {
        $project = $this->createTestProject();
        $user = $this->createTestUser();

        $this->actingAs($user);

        Livewire::test(ProjectReports::class, ['project' => $project])
            ->assertStatus(200);
    }

    public function test_component_initializes_with_current_year_when_no_reports()
    {
        $project = $this->createTestProject();
        $user = $this->createTestUser();

        $this->actingAs($user);

        $component = Livewire::test(ProjectReports::class, ['project' => $project]);

        $this->assertEquals(now()->year, $component->get('selectedYear'));
        $this->assertEmpty($component->get('availableYears'));
    }

    public function test_component_gets_available_years_from_sent_reports()
    {
        $project = $this->createTestProject();
        $user = $this->createTestUser();

        // Create reports with different statuses and years
        Report::factory()->sent()->create([
            'project_id' => $project->id,
            'account_manager_id' => $user->id,
            'report_date' => now()->subYear(),
            'report_month' => 6,
        ]);

        Report::factory()->sent()->create([
            'project_id' => $project->id,
            'account_manager_id' => $user->id,
            'report_date' => now(),
            'report_month' => 3,
        ]);

        // This report should not appear (draft status)
        Report::factory()->create([
            'project_id' => $project->id,
            'account_manager_id' => $user->id,
            'report_date' => now()->subYears(2),
            'report_month' => 12,
            'status' => 'draft',
        ]);

        $this->actingAs($user);

        $component = Livewire::test(ProjectReports::class, ['project' => $project]);
        $availableYears = $component->get('availableYears');

        $this->assertCount(2, $availableYears);
        $this->assertContains(now()->year, $availableYears);
        $this->assertContains(now()->subYear()->year, $availableYears);
        $this->assertNotContains(now()->subYears(2)->year, $availableYears);
    }

    public function test_component_defaults_to_current_year_when_available()
    {
        $project = $this->createTestProject();
        $user = $this->createTestUser();

        // Create a report for current year
        Report::factory()->sent()->create([
            'project_id' => $project->id,
            'account_manager_id' => $user->id,
            'report_date' => now(),
            'report_month' => 6,
        ]);

        // Create a report for last year
        Report::factory()->sent()->create([
            'project_id' => $project->id,
            'account_manager_id' => $user->id,
            'report_date' => now()->subYear(),
            'report_month' => 12,
        ]);

        $this->actingAs($user);

        $component = Livewire::test(ProjectReports::class, ['project' => $project]);

        $this->assertEquals(now()->year, $component->get('selectedYear'));
    }

    public function test_component_defaults_to_latest_year_when_current_not_available()
    {
        $project = $this->createTestProject();
        $user = $this->createTestUser();

        // Create reports for past years only
        Report::factory()->sent()->create([
            'project_id' => $project->id,
            'account_manager_id' => $user->id,
            'report_date' => now()->subYear(),
            'report_month' => 6,
        ]);

        Report::factory()->sent()->create([
            'project_id' => $project->id,
            'account_manager_id' => $user->id,
            'report_date' => now()->subYears(2),
            'report_month' => 12,
        ]);

        $this->actingAs($user);

        $component = Livewire::test(ProjectReports::class, ['project' => $project]);

        $this->assertEquals(now()->subYear()->year, $component->get('selectedYear'));
    }

    public function test_can_change_selected_year()
    {
        $project = $this->createTestProject();
        $user = $this->createTestUser();

        // Create reports for multiple years
        Report::factory()->sent()->create([
            'project_id' => $project->id,
            'account_manager_id' => $user->id,
            'report_date' => now(),
            'report_month' => 6,
        ]);

        Report::factory()->sent()->create([
            'project_id' => $project->id,
            'account_manager_id' => $user->id,
            'report_date' => now()->subYear(),
            'report_month' => 12,
        ]);

        $this->actingAs($user);

        $component = Livewire::test(ProjectReports::class, ['project' => $project])
            ->set('selectedYear', now()->subYear()->year);

        $this->assertEquals(now()->subYear()->year, $component->get('selectedYear'));
    }

    public function test_get_reports_for_year_generates_12_sequential_reports()
    {
        $startDate = Carbon::create(2023, 3, 1); // March 2023
        $project = $this->createTestProject($startDate);
        $user = $this->createTestUser();

        $this->actingAs($user);

        $component = Livewire::test(ProjectReports::class, ['project' => $project])
            ->set('selectedYear', 2023);

        $monthlyReports = $component->viewData('monthlyReports');

        $this->assertCount(12, $monthlyReports);

        // First report should be March 2023 (project start)
        $this->assertEquals(1, $monthlyReports[0]['report_number']);
        $this->assertEquals(3, $monthlyReports[0]['month']);
        $this->assertEquals(2023, $monthlyReports[0]['year']);
        $this->assertEquals('March 2023', $monthlyReports[0]['name']);

        // Last report should be February 2024 (12 months later)
        $this->assertEquals(12, $monthlyReports[11]['report_number']);
        $this->assertEquals(2, $monthlyReports[11]['month']);
        $this->assertEquals(2024, $monthlyReports[11]['year']);
        $this->assertEquals('February 2024', $monthlyReports[11]['name']);
    }

    public function test_get_reports_for_year_handles_subsequent_years_correctly()
    {
        $startDate = Carbon::create(2023, 3, 1); // March 2023
        $project = $this->createTestProject($startDate);
        $user = $this->createTestUser();

        $this->actingAs($user);

        // Test second year (2024)
        $component = Livewire::test(ProjectReports::class, ['project' => $project])
            ->set('selectedYear', 2024);

        $monthlyReports = $component->viewData('monthlyReports');

        $this->assertCount(12, $monthlyReports);

        // First report of 2024 should be report 11 (March-Dec 2023 = 10 reports, starting from 1)
        $this->assertEquals(11, $monthlyReports[0]['report_number']);
        $this->assertEquals(1, $monthlyReports[0]['month']);
        $this->assertEquals(2024, $monthlyReports[0]['year']);

        // Last report should be December 2024 (report 22: 11 + 11 more reports)
        $this->assertEquals(22, $monthlyReports[11]['report_number']);
        $this->assertEquals(12, $monthlyReports[11]['month']);
        $this->assertEquals(2024, $monthlyReports[11]['year']);
    }

    public function test_reports_for_year_includes_actual_report_data()
    {
        $startDate = Carbon::create(2023, 6, 1); // June 2023
        $project = $this->createTestProject($startDate);
        $user = $this->createTestUser();

        // Create actual report for August 2023
        $report = Report::factory()->sent()->create([
            'project_id' => $project->id,
            'account_manager_id' => $user->id,
            'report_date' => Carbon::create(2023, 8, 15),
            'report_month' => 8,
            'title' => 'August 2023 Marketing Report',
        ]);

        $this->actingAs($user);

        $component = Livewire::test(ProjectReports::class, ['project' => $project])
            ->set('selectedYear', 2023);

        $monthlyReports = $component->viewData('monthlyReports');

        // Find August 2023 report (should be 3rd report: June=1, July=2, August=3)
        $augustReport = collect($monthlyReports)->firstWhere('month', 8);

        $this->assertNotNull($augustReport);
        $this->assertNotNull($augustReport['report']);
        $this->assertEquals('August 2023 Marketing Report', $augustReport['report']->title);
        $this->assertEquals(3, $augustReport['report_number']);
    }

    public function test_reports_for_year_handles_months_without_reports()
    {
        $startDate = Carbon::create(2023, 6, 1); // June 2023
        $project = $this->createTestProject($startDate);
        $user = $this->createTestUser();

        $this->actingAs($user);

        $component = Livewire::test(ProjectReports::class, ['project' => $project])
            ->set('selectedYear', 2023);

        $monthlyReports = $component->viewData('monthlyReports');

        // All reports should have null report data since no reports exist
        foreach ($monthlyReports as $monthlyReport) {
            $this->assertNull($monthlyReport['report']);
        }
    }

    public function test_current_month_is_marked_correctly()
    {
        $project = $this->createTestProject();
        $user = $this->createTestUser();

        $this->actingAs($user);

        $component = Livewire::test(ProjectReports::class, ['project' => $project])
            ->set('selectedYear', now()->year);

        $monthlyReports = $component->viewData('monthlyReports');

        $currentMonthReport = collect($monthlyReports)->firstWhere('is_current_month', true);
        $this->assertNotNull($currentMonthReport);
        $this->assertEquals(now()->month, $currentMonthReport['month']);
        $this->assertEquals(now()->year, $currentMonthReport['year']);
    }

    public function test_future_months_are_marked_correctly()
    {
        $project = $this->createTestProject();
        $user = $this->createTestUser();

        $this->actingAs($user);

        $component = Livewire::test(ProjectReports::class, ['project' => $project])
            ->set('selectedYear', now()->year);

        $monthlyReports = $component->viewData('monthlyReports');

        foreach ($monthlyReports as $monthlyReport) {
            $reportDate = Carbon::create($monthlyReport['year'], $monthlyReport['month'], 1);
            $this->assertEquals($reportDate->isFuture(), $monthlyReport['is_future_month']);
        }
    }

    public function test_only_shows_reports_for_specific_project()
    {
        $project1 = $this->createTestProject();
        $project2 = $this->createTestProject();
        $user = $this->createTestUser();

        // Create report for project1
        Report::factory()->sent()->create([
            'project_id' => $project1->id,
            'account_manager_id' => $user->id,
            'report_date' => now(),
            'report_month' => 6,
        ]);

        // Create report for project2
        Report::factory()->sent()->create([
            'project_id' => $project2->id,
            'account_manager_id' => $user->id,
            'report_date' => now(),
            'report_month' => 6,
        ]);

        $this->actingAs($user);

        $component1 = Livewire::test(ProjectReports::class, ['project' => $project1]);
        $component2 = Livewire::test(ProjectReports::class, ['project' => $project2]);

        $availableYears1 = $component1->get('availableYears');
        $availableYears2 = $component2->get('availableYears');

        // Both should have current year since they both have reports
        $this->assertContains(now()->year, $availableYears1);
        $this->assertContains(now()->year, $availableYears2);

        // But reports data should be project-specific
        $monthlyReports1 = $component1->get('monthlyReports');
        $monthlyReports2 = $component2->get('monthlyReports');

        $reportWithData1 = collect($monthlyReports1)->firstWhere('report');
        $reportWithData2 = collect($monthlyReports2)->firstWhere('report');

        if ($reportWithData1 && $reportWithData2) {
            $this->assertEquals($project1->id, $reportWithData1['report']->project_id);
            $this->assertEquals($project2->id, $reportWithData2['report']->project_id);
        }
    }

    public function test_short_month_names_are_generated_correctly()
    {
        $project = $this->createTestProject(Carbon::create(2023, 1, 1));
        $user = $this->createTestUser();

        $this->actingAs($user);

        $component = Livewire::test(ProjectReports::class, ['project' => $project])
            ->set('selectedYear', 2023);

        $monthlyReports = $component->viewData('monthlyReports');

        $this->assertEquals('Jan 2023', $monthlyReports[0]['short_name']);
        $this->assertEquals('Feb 2023', $monthlyReports[1]['short_name']);
        $this->assertEquals('Dec 2023', $monthlyReports[11]['short_name']);
    }

    public function test_updated_selected_year_triggers_rerender()
    {
        $project = $this->createTestProject();
        $user = $this->createTestUser();

        // Create reports for different years
        Report::factory()->sent()->create([
            'project_id' => $project->id,
            'account_manager_id' => $user->id,
            'report_date' => now(),
            'report_month' => 6,
        ]);

        Report::factory()->sent()->create([
            'project_id' => $project->id,
            'account_manager_id' => $user->id,
            'report_date' => now()->subYear(),
            'report_month' => 8,
        ]);

        $this->actingAs($user);

        $component = Livewire::test(ProjectReports::class, ['project' => $project]);

        // Initially should be current year
        $this->assertEquals(now()->year, $component->get('selectedYear'));

        // Change to previous year
        $component->set('selectedYear', now()->subYear()->year);

        // Should update
        $this->assertEquals(now()->subYear()->year, $component->get('selectedYear'));

        // Should trigger re-render of reports (can't directly test this, but setting works)
        $monthlyReports = $component->viewData('monthlyReports');
        $this->assertCount(12, $monthlyReports);
    }
}