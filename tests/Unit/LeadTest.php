<?php

namespace Tests\Unit;

use App\Models\Lead;
use App\Models\Project;
use App\Models\Business;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadTest extends TestCase
{
    use RefreshDatabase;

    private function createTestProject()
    {
        $business = Business::factory()->create();
        return Project::factory()->create(['business_id' => $business->id]);
    }

    public function test_lead_can_be_created()
    {
        $project = $this->createTestProject();

        $lead = Lead::create([
            'project_id' => $project->id,
            'payload' => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'message' => 'I need help with my business',
            ],
        ]);

        $this->assertInstanceOf(Lead::class, $lead);
        $this->assertEquals($project->id, $lead->project_id);
        $this->assertEquals('new', $lead->status); // Default status
        $this->assertFalse($lead->is_valid); // Default is_valid
    }

    public function test_lead_has_default_attributes()
    {
        $project = $this->createTestProject();

        $lead = Lead::create([
            'project_id' => $project->id,
            'payload' => ['test' => 'data'],
        ]);

        $this->assertEquals('new', $lead->status);
        $this->assertFalse($lead->is_valid);
    }

    public function test_lead_belongs_to_project()
    {
        $project = $this->createTestProject();
        $lead = Lead::factory()->create(['project_id' => $project->id]);

        $this->assertInstanceOf(Project::class, $lead->project);
        $this->assertEquals($project->id, $lead->project->id);
    }

    public function test_lead_casts_attributes_correctly()
    {
        $project = $this->createTestProject();

        $lead = Lead::create([
            'project_id' => $project->id,
            'payload' => ['test' => 'data'],
            'is_valid' => 1,
            'value' => '1500.50',
            'submitted_at' => '2023-06-15 10:30:00',
        ]);

        $this->assertIsArray($lead->payload);
        $this->assertIsBool($lead->is_valid);
        $this->assertTrue($lead->is_valid);
        $this->assertInstanceOf(Carbon::class, $lead->submitted_at);
        $this->assertEquals('1500.50', $lead->value);
    }

    public function test_create_from_webhook_with_valid_project()
    {
        $project = $this->createTestProject();

        $payload = [
            'project_id' => $project->id,
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'message' => 'Contact me please',
            'utm_source' => 'google',
            'utm_medium' => 'cpc',
            'utm_campaign' => 'summer-sale',
            'time_submitted' => '2:30 PM',
        ];

        $lead = Lead::createFromWebhook($payload);

        $this->assertEquals($project->id, $lead->project_id);
        $this->assertEquals($payload, $lead->payload);
        $this->assertEquals('google', $lead->utm_source);
        $this->assertEquals('cpc', $lead->utm_medium);
        $this->assertEquals('summer-sale', $lead->utm_campaign);
        $this->assertNotNull($lead->submitted_at);
    }

    public function test_create_from_webhook_throws_exception_for_invalid_project()
    {
        $payload = [
            'project_id' => 'invalid-project-id',
            'name' => 'Test User',
            'email' => 'test@example.com',
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Valid project_id is required in webhook payload');

        Lead::createFromWebhook($payload);
    }

    public function test_create_from_webhook_throws_exception_for_missing_project()
    {
        $payload = [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Valid project_id is required in webhook payload');

        Lead::createFromWebhook($payload);
    }

    public function test_create_from_webhook_handles_time_submitted()
    {
        $project = $this->createTestProject();

        $payload = [
            'project_id' => $project->id,
            'time_submitted' => '10.30 AM',
        ];

        $lead = Lead::createFromWebhook($payload);

        $this->assertInstanceOf(Carbon::class, $lead->submitted_at);
        $this->assertEquals('10:30', $lead->submitted_at->format('H:i'));
    }

    public function test_create_from_webhook_handles_invalid_time_format()
    {
        $project = $this->createTestProject();

        $payload = [
            'project_id' => $project->id,
            'time_submitted' => 'invalid-time',
        ];

        $before = now()->subSecond();
        $lead = Lead::createFromWebhook($payload);
        $after = now()->addSecond();

        $this->assertTrue($lead->submitted_at->between($before, $after));
    }

    public function test_get_email_attribute_from_various_payload_keys()
    {
        $project = $this->createTestProject();

        // Test with 'email' key
        $lead1 = Lead::factory()->create([
            'project_id' => $project->id,
            'payload' => ['email' => 'test1@example.com'],
        ]);
        $this->assertEquals('test1@example.com', $lead1->email);

        // Test with 'Email' key (capitalized)
        $lead2 = Lead::factory()->create([
            'project_id' => $project->id,
            'payload' => ['Email' => 'test2@example.com'],
        ]);
        $this->assertEquals('test2@example.com', $lead2->email);

        // Test with 'Field_1' key
        $lead3 = Lead::factory()->create([
            'project_id' => $project->id,
            'payload' => ['Field_1' => 'test3@example.com'],
        ]);
        $this->assertEquals('test3@example.com', $lead3->email);

        // Test with no email
        $lead4 = Lead::factory()->create([
            'project_id' => $project->id,
            'payload' => ['name' => 'No Email User'],
        ]);
        $this->assertNull($lead4->email);
    }

    public function test_get_name_attribute_from_payload()
    {
        $project = $this->createTestProject();

        // Test with 'name' key
        $lead1 = Lead::factory()->create([
            'project_id' => $project->id,
            'payload' => ['name' => 'John Doe'],
        ]);
        $this->assertEquals('John Doe', $lead1->name);

        // Test with first_name and last_name
        $lead2 = Lead::factory()->create([
            'project_id' => $project->id,
            'payload' => ['first_name' => 'Jane', 'last_name' => 'Smith'],
        ]);
        $this->assertEquals('Jane Smith', $lead2->name);

        // Test with only first_name
        $lead3 = Lead::factory()->create([
            'project_id' => $project->id,
            'payload' => ['first_name' => 'Bob'],
        ]);
        $this->assertEquals('Bob', $lead3->name);

        // Test with only last_name
        $lead4 = Lead::factory()->create([
            'project_id' => $project->id,
            'payload' => ['last_name' => 'Johnson'],
        ]);
        $this->assertEquals('Johnson', $lead4->name);

        // Test with no name data
        $lead5 = Lead::factory()->create([
            'project_id' => $project->id,
            'payload' => ['email' => 'noname@example.com'],
        ]);
        $this->assertNull($lead5->name);
    }

    public function test_get_phone_attribute_from_payload()
    {
        $project = $this->createTestProject();

        // Test with 'phone' key
        $lead1 = Lead::factory()->create([
            'project_id' => $project->id,
            'payload' => ['phone' => '555-1234'],
        ]);
        $this->assertEquals('555-1234', $lead1->phone);

        // Test with 'Phone' key (capitalized)
        $lead2 = Lead::factory()->create([
            'project_id' => $project->id,
            'payload' => ['Phone' => '555-5678'],
        ]);
        $this->assertEquals('555-5678', $lead2->phone);

        // Test with no phone
        $lead3 = Lead::factory()->create([
            'project_id' => $project->id,
            'payload' => ['name' => 'No Phone User'],
        ]);
        $this->assertNull($lead3->phone);
    }

    public function test_get_message_attribute_from_various_payload_keys()
    {
        $project = $this->createTestProject();

        // Test with 'message' key
        $lead1 = Lead::factory()->create([
            'project_id' => $project->id,
            'payload' => ['message' => 'This is a message'],
        ]);
        $this->assertEquals('This is a message', $lead1->message);

        // Test with 'Message' key
        $lead2 = Lead::factory()->create([
            'project_id' => $project->id,
            'payload' => ['Message' => 'This is a Message'],
        ]);
        $this->assertEquals('This is a Message', $lead2->message);

        // Test with 'comments' key
        $lead3 = Lead::factory()->create([
            'project_id' => $project->id,
            'payload' => ['comments' => 'These are comments'],
        ]);
        $this->assertEquals('These are comments', $lead3->message);

        // Test with 'inquiry' key
        $lead4 = Lead::factory()->create([
            'project_id' => $project->id,
            'payload' => ['inquiry' => 'This is an inquiry'],
        ]);
        $this->assertEquals('This is an inquiry', $lead4->message);

        // Test with no message
        $lead5 = Lead::factory()->create([
            'project_id' => $project->id,
            'payload' => ['name' => 'No Message User'],
        ]);
        $this->assertNull($lead5->message);
    }

    public function test_get_custom_field_attribute()
    {
        $project = $this->createTestProject();

        $lead = Lead::factory()->create([
            'project_id' => $project->id,
            'payload' => [
                'custom_field_1' => 'Custom Value 1',
                'special_data' => 'Special Info',
            ],
        ]);

        $this->assertEquals('Custom Value 1', $lead->getCustomFieldAttribute('custom_field_1'));
        $this->assertEquals('Special Info', $lead->getCustomFieldAttribute('special_data'));
        $this->assertNull($lead->getCustomFieldAttribute('non_existent_field'));
    }

    public function test_new_leads_scope()
    {
        $project = $this->createTestProject();

        Lead::factory()->create(['project_id' => $project->id, 'status' => 'new']);
        Lead::factory()->create(['project_id' => $project->id, 'status' => 'new']);
        Lead::factory()->create(['project_id' => $project->id, 'status' => 'valid']);
        Lead::factory()->create(['project_id' => $project->id, 'status' => 'closed']);

        $newLeads = Lead::newLeads()->get();

        $this->assertCount(2, $newLeads);
        $this->assertTrue($newLeads->every(fn($lead) => $lead->status === 'new'));
    }

    public function test_valid_scope()
    {
        $project = $this->createTestProject();

        Lead::factory()->create(['project_id' => $project->id, 'is_valid' => true]);
        Lead::factory()->create(['project_id' => $project->id, 'is_valid' => true]);
        Lead::factory()->create(['project_id' => $project->id, 'is_valid' => false]);

        $validLeads = Lead::valid()->get();

        $this->assertCount(2, $validLeads);
        $this->assertTrue($validLeads->every(fn($lead) => $lead->is_valid === true));
    }

    public function test_invalid_scope()
    {
        $project = $this->createTestProject();

        Lead::factory()->create(['project_id' => $project->id, 'is_valid' => false]);
        Lead::factory()->create(['project_id' => $project->id, 'is_valid' => false]);
        Lead::factory()->create(['project_id' => $project->id, 'is_valid' => true]);

        $invalidLeads = Lead::invalid()->get();

        $this->assertCount(2, $invalidLeads);
        $this->assertTrue($invalidLeads->every(fn($lead) => $lead->is_valid === false));
    }

    public function test_closed_scope()
    {
        $project = $this->createTestProject();

        Lead::factory()->create(['project_id' => $project->id, 'status' => 'closed']);
        Lead::factory()->create(['project_id' => $project->id, 'status' => 'closed']);
        Lead::factory()->create(['project_id' => $project->id, 'status' => 'new']);
        Lead::factory()->create(['project_id' => $project->id, 'status' => 'valid']);

        $closedLeads = Lead::closed()->get();

        $this->assertCount(2, $closedLeads);
        $this->assertTrue($closedLeads->every(fn($lead) => $lead->status === 'closed'));
    }

    public function test_mark_as_valid()
    {
        $project = $this->createTestProject();

        $lead = Lead::factory()->create([
            'project_id' => $project->id,
            'status' => 'new',
            'is_valid' => false,
        ]);

        $result = $lead->markAsValid();

        $this->assertTrue($result);
        $this->assertTrue($lead->fresh()->is_valid);
        $this->assertEquals('valid', $lead->fresh()->status);
    }

    public function test_mark_as_invalid()
    {
        $project = $this->createTestProject();

        $lead = Lead::factory()->create([
            'project_id' => $project->id,
            'status' => 'new',
            'is_valid' => false,
        ]);

        $result = $lead->markAsInvalid();

        $this->assertTrue($result);
        $this->assertFalse($lead->fresh()->is_valid);
        $this->assertEquals('invalid', $lead->fresh()->status);
    }

    public function test_mark_as_closed_without_value()
    {
        $project = $this->createTestProject();

        $lead = Lead::factory()->create([
            'project_id' => $project->id,
            'status' => 'valid',
            'value' => null,
        ]);

        $result = $lead->markAsClosed();

        $this->assertTrue($result);
        $this->assertEquals('closed', $lead->fresh()->status);
        $this->assertNull($lead->fresh()->value);
    }

    public function test_mark_as_closed_with_value()
    {
        $project = $this->createTestProject();

        $lead = Lead::factory()->create([
            'project_id' => $project->id,
            'status' => 'valid',
            'value' => null,
        ]);

        $result = $lead->markAsClosed(2500.00);

        $this->assertTrue($result);
        $this->assertEquals('closed', $lead->fresh()->status);
        $this->assertEquals('2500.00', $lead->fresh()->value);
    }

    public function test_mark_as_closed_updates_existing_value()
    {
        $project = $this->createTestProject();

        $lead = Lead::factory()->create([
            'project_id' => $project->id,
            'status' => 'valid',
            'value' => 1000.00,
        ]);

        $result = $lead->markAsClosed(3500.50);

        $this->assertTrue($result);
        $this->assertEquals('closed', $lead->fresh()->status);
        $this->assertEquals('3500.50', $lead->fresh()->value);
    }

    public function test_lead_fillable_attributes()
    {
        $project = $this->createTestProject();

        $attributes = [
            'project_id' => $project->id,
            'payload' => ['test' => 'data'],
            'status' => 'valid',
            'value' => 1500.00,
            'is_valid' => true,
            'notes' => 'Test notes',
            'submitted_at' => now(),
            'ip_address' => '192.168.1.1',
            'referrer_name' => 'example.com',
            'utm_source' => 'google',
            'utm_medium' => 'cpc',
            'utm_campaign' => 'test-campaign',
        ];

        $lead = Lead::create($attributes);

        foreach ($attributes as $key => $value) {
            if ($key === 'submitted_at') {
                $this->assertInstanceOf(Carbon::class, $lead->$key);
            } elseif ($key === 'value') {
                $this->assertEquals('1500.00', $lead->$key);
            } else {
                $this->assertEquals($value, $lead->$key);
            }
        }
    }
}