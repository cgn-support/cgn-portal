<?php

namespace Tests\Feature;

use App\Livewire\ProjectLeads;
use App\Models\Business;
use App\Models\Lead;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectLeadsTest extends TestCase
{
    use RefreshDatabase;

    private function createTestProject()
    {
        $business = Business::factory()->create();
        return Project::factory()->create(['business_id' => $business->id]);
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

        Livewire::test(ProjectLeads::class, ['project' => $project])
            ->assertStatus(200);
    }

    public function test_component_displays_leads_for_project()
    {
        $project = $this->createTestProject();
        $user = $this->createTestUser();

        // Create leads for this project
        Lead::factory()->create([
            'project_id' => $project->id,
            'payload' => ['name' => 'John Doe', 'email' => 'john@example.com'],
        ]);

        Lead::factory()->create([
            'project_id' => $project->id,
            'payload' => ['name' => 'Jane Smith', 'email' => 'jane@example.com'],
        ]);

        // Create lead for different project
        $otherProject = $this->createTestProject();
        Lead::factory()->create([
            'project_id' => $otherProject->id,
            'payload' => ['name' => 'Other User', 'email' => 'other@example.com'],
        ]);

        $this->actingAs($user);

        Livewire::test(ProjectLeads::class, ['project' => $project])
            ->assertSee('John Doe')
            ->assertSee('jane@example.com')
            ->assertDontSee('Other User');
    }

    public function test_component_displays_metrics_correctly()
    {
        $project = $this->createTestProject();
        $user = $this->createTestUser();

        // Create leads with different statuses
        Lead::factory()->create([
            'project_id' => $project->id,
            'status' => 'new',
            'is_valid' => false,
        ]);

        Lead::factory()->create([
            'project_id' => $project->id,
            'status' => 'valid',
            'is_valid' => true,
        ]);

        Lead::factory()->create([
            'project_id' => $project->id,
            'status' => 'closed',
            'is_valid' => true,
            'value' => 1000.00,
        ]);

        Lead::factory()->create([
            'project_id' => $project->id,
            'status' => 'closed',
            'is_valid' => true,
            'value' => 500.00,
        ]);

        $this->actingAs($user);

        $component = Livewire::test(ProjectLeads::class, ['project' => $project]);

        $metrics = $component->get('metrics');

        $this->assertEquals(4, $metrics['total_leads']);
        $this->assertEquals(3, $metrics['valid_leads']); // 1 valid + 2 closed
        $this->assertEquals(2, $metrics['closed_leads']);
        $this->assertEquals(1500.00, $metrics['total_value']);
        $this->assertEquals(750.00, $metrics['avg_value']);
    }

    public function test_can_mark_lead_as_valid()
    {
        $project = $this->createTestProject();
        $user = $this->createTestUser();

        $lead = Lead::factory()->create([
            'project_id' => $project->id,
            'status' => 'new',
            'is_valid' => false,
        ]);

        $this->actingAs($user);

        Livewire::test(ProjectLeads::class, ['project' => $project])
            ->call('markAsValid', $lead->id)
            ->assertDispatched('lead-updated');

        $lead->refresh();
        $this->assertTrue($lead->is_valid);
        $this->assertEquals('valid', $lead->status);
    }

    public function test_can_mark_lead_as_invalid()
    {
        $project = $this->createTestProject();
        $user = $this->createTestUser();

        $lead = Lead::factory()->create([
            'project_id' => $project->id,
            'status' => 'new',
            'is_valid' => false,
        ]);

        $this->actingAs($user);

        Livewire::test(ProjectLeads::class, ['project' => $project])
            ->call('markAsInvalid', $lead->id)
            ->assertDispatched('lead-updated');

        $lead->refresh();
        $this->assertFalse($lead->is_valid);
        $this->assertEquals('invalid', $lead->status);
    }

    public function test_can_open_and_close_lead_modal()
    {
        $project = $this->createTestProject();
        $user = $this->createTestUser();

        $lead = Lead::factory()->create([
            'project_id' => $project->id,
        ]);

        $this->actingAs($user);

        $component = Livewire::test(ProjectLeads::class, ['project' => $project])
            ->call('openLeadModal', $lead->id)
            ->assertSet('showLeadModal', true)
            ->assertSet('selectedLead.id', $lead->id)
            ->call('closeLeadModal')
            ->assertSet('showLeadModal', false)
            ->assertSet('selectedLead', null);
    }

    public function test_can_mark_lead_as_closed_with_value()
    {
        $project = $this->createTestProject();
        $user = $this->createTestUser();

        $lead = Lead::factory()->create([
            'project_id' => $project->id,
            'status' => 'valid',
            'is_valid' => true,
        ]);

        $this->actingAs($user);

        Livewire::test(ProjectLeads::class, ['project' => $project])
            ->call('openLeadModal', $lead->id)
            ->set('leadValue', 2500.00)
            ->set('leadNotes', 'Great customer, quick close')
            ->call('markAsClosed')
            ->assertSet('showLeadModal', false)
            ->assertDispatched('lead-updated');

        $lead->refresh();
        $this->assertEquals('closed', $lead->status);
        $this->assertEquals(2500.00, $lead->value);
        $this->assertEquals('Great customer, quick close', $lead->notes);
    }

    public function test_search_filters_leads()
    {
        $project = $this->createTestProject();
        $user = $this->createTestUser();

        Lead::factory()->create([
            'project_id' => $project->id,
            'payload' => ['name' => 'John Doe', 'email' => 'john@example.com'],
        ]);

        Lead::factory()->create([
            'project_id' => $project->id,
            'payload' => ['name' => 'Jane Smith', 'email' => 'jane@example.com'],
        ]);

        $this->actingAs($user);

        Livewire::test(ProjectLeads::class, ['project' => $project])
            ->set('search', 'john@example.com')
            ->assertSee('John Doe')
            ->assertDontSee('Jane Smith');
    }

    public function test_status_filter_works()
    {
        $project = $this->createTestProject();
        $user = $this->createTestUser();

        $validLead = Lead::factory()->create([
            'project_id' => $project->id,
            'status' => 'valid',
            'payload' => ['name' => 'Valid Lead'],
        ]);

        $invalidLead = Lead::factory()->create([
            'project_id' => $project->id,
            'status' => 'invalid',
            'payload' => ['name' => 'Invalid Lead'],
        ]);

        $this->actingAs($user);

        $component = Livewire::test(ProjectLeads::class, ['project' => $project]);
        
        $component->set('statusFilter', 'valid');
        $component->assertSee('Valid Lead');
        
        $component->set('statusFilter', 'invalid');
        $component->assertSee('Invalid Lead');
    }

    public function test_cannot_manage_leads_from_different_project()
    {
        $project1 = $this->createTestProject();
        $project2 = $this->createTestProject();
        $user = $this->createTestUser();

        $lead = Lead::factory()->newLead()->create([
            'project_id' => $project2->id,
        ]);

        $this->actingAs($user);

        // Should not update lead from different project
        Livewire::test(ProjectLeads::class, ['project' => $project1])
            ->call('markAsValid', $lead->id);

        $lead->refresh();
        $this->assertEquals('new', $lead->status); // Should remain unchanged due to security check
    }

    public function test_pagination_works()
    {
        $project = $this->createTestProject();
        $user = $this->createTestUser();

        // Create more than 15 leads (default pagination limit)
        Lead::factory()->count(20)->create([
            'project_id' => $project->id,
        ]);

        $this->actingAs($user);

        $component = Livewire::test(ProjectLeads::class, ['project' => $project]);
        
        $leads = $component->get('leads');
        
        $this->assertEquals(15, $leads->perPage());
        $this->assertEquals(20, $leads->total());
    }

    public function test_lead_modal_shows_correct_information()
    {
        $project = $this->createTestProject();
        $user = $this->createTestUser();

        $lead = Lead::factory()->create([
            'project_id' => $project->id,
            'payload' => [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'phone' => '555-1234',
                'message' => 'I need help with my business',
            ],
            'utm_source' => 'google',
            'utm_medium' => 'cpc',
            'utm_campaign' => 'spring-promo',
        ]);

        $this->actingAs($user);

        Livewire::test(ProjectLeads::class, ['project' => $project])
            ->call('openLeadModal', $lead->id)
            ->assertSee('Test User')
            ->assertSee('test@example.com')
            ->assertSee('555-1234')
            ->assertSee('I need help with my business')
            ->assertSee('google')
            ->assertSee('cpc')
            ->assertSee('spring-promo');
    }
}