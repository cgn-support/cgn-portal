<?php

namespace Tests\Unit;

use App\Models\ClientTask;
use App\Models\PresetTask;
use App\Models\Project;
use App\Models\User;
use App\Models\Business;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTaskTest extends TestCase
{
    use RefreshDatabase;

    private function createTestProject()
    {
        $business = Business::factory()->create();
        return Project::factory()->create(['business_id' => $business->id]);
    }

    public function test_client_task_can_be_created()
    {
        $project = $this->createTestProject();
        $presetTask = PresetTask::factory()->create();
        $user = User::factory()->create();

        $clientTask = ClientTask::create([
            'project_id' => $project->id,
            'preset_task_id' => $presetTask->id,
            'assigned_by' => $user->id,
            'title' => 'Test Client Task',
            'description' => 'This is a test client task',
            'link' => 'https://example.com',
            'is_completed' => false,
            'due_date' => now()->addDays(7),
        ]);

        $this->assertInstanceOf(ClientTask::class, $clientTask);
        $this->assertEquals('Test Client Task', $clientTask->title);
        $this->assertEquals('This is a test client task', $clientTask->description);
        $this->assertEquals('https://example.com', $clientTask->link);
        $this->assertFalse($clientTask->is_completed);
        $this->assertNull($clientTask->completed_at);
    }

    public function test_client_task_has_relationships()
    {
        $project = $this->createTestProject();
        $presetTask = PresetTask::factory()->create();
        $user = User::factory()->create();

        $clientTask = ClientTask::create([
            'project_id' => $project->id,
            'preset_task_id' => $presetTask->id,
            'assigned_by' => $user->id,
            'title' => 'Test Task',
            'description' => 'Test description',
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $clientTask->project());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $clientTask->presetTask());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $clientTask->assignedBy());

        $this->assertEquals($project->id, $clientTask->project->id);
        $this->assertEquals($presetTask->id, $clientTask->presetTask->id);
        $this->assertEquals($user->id, $clientTask->assignedBy->id);
    }

    public function test_client_task_can_be_marked_as_completed()
    {
        $project = $this->createTestProject();
        $presetTask = PresetTask::factory()->create();
        $user = User::factory()->create();

        $clientTask = ClientTask::create([
            'project_id' => $project->id,
            'preset_task_id' => $presetTask->id,
            'assigned_by' => $user->id,
            'title' => 'Test Task',
            'description' => 'Test description',
            'is_completed' => false,
        ]);

        $this->assertFalse($clientTask->is_completed);
        $this->assertNull($clientTask->completed_at);

        $result = $clientTask->markAsCompleted('Task completed successfully');

        $this->assertTrue($result);
        $this->assertTrue($clientTask->fresh()->is_completed);
        $this->assertNotNull($clientTask->fresh()->completed_at);
        $this->assertEquals('Task completed successfully', $clientTask->fresh()->completion_notes);
    }

    public function test_client_task_can_be_marked_as_completed_without_notes()
    {
        $project = $this->createTestProject();
        $presetTask = PresetTask::factory()->create();
        $user = User::factory()->create();

        $clientTask = ClientTask::create([
            'project_id' => $project->id,
            'preset_task_id' => $presetTask->id,
            'assigned_by' => $user->id,
            'title' => 'Test Task',
            'description' => 'Test description',
        ]);

        $result = $clientTask->markAsCompleted();

        $this->assertTrue($result);
        $this->assertTrue($clientTask->fresh()->is_completed);
        $this->assertNotNull($clientTask->fresh()->completed_at);
        $this->assertNull($clientTask->fresh()->completion_notes);
    }

    public function test_client_task_scopes()
    {
        $project = $this->createTestProject();
        $presetTask = PresetTask::factory()->create();
        $user = User::factory()->create();

        // Create completed task
        $completedTask = ClientTask::create([
            'project_id' => $project->id,
            'preset_task_id' => $presetTask->id,
            'assigned_by' => $user->id,
            'title' => 'Completed Task',
            'description' => 'Completed task description',
            'is_completed' => true,
            'completed_at' => now(),
        ]);

        // Create pending task
        $pendingTask = ClientTask::create([
            'project_id' => $project->id,
            'preset_task_id' => $presetTask->id,
            'assigned_by' => $user->id,
            'title' => 'Pending Task',
            'description' => 'Pending task description',
        ]);

        // Create overdue task
        $overdueTask = ClientTask::create([
            'project_id' => $project->id,
            'preset_task_id' => $presetTask->id,
            'assigned_by' => $user->id,
            'title' => 'Overdue Task',
            'description' => 'Overdue task description',
            'due_date' => now()->subDays(1),
        ]);

        $this->assertCount(1, ClientTask::completed()->get());
        $this->assertCount(2, ClientTask::pending()->get());
        $this->assertCount(1, ClientTask::overdue()->get());
    }

    public function test_client_task_is_overdue_attribute()
    {
        $project = $this->createTestProject();
        $presetTask = PresetTask::factory()->create();
        $user = User::factory()->create();

        // Task with future due date
        $futureTask = ClientTask::create([
            'project_id' => $project->id,
            'preset_task_id' => $presetTask->id,
            'assigned_by' => $user->id,
            'title' => 'Future Task',
            'description' => 'Future task description',
            'due_date' => now()->addDays(1),
        ]);

        // Task with past due date
        $overdueTask = ClientTask::create([
            'project_id' => $project->id,
            'preset_task_id' => $presetTask->id,
            'assigned_by' => $user->id,
            'title' => 'Overdue Task',
            'description' => 'Overdue task description',
            'due_date' => now()->subDays(1),
        ]);

        // Completed task with past due date
        $completedTask = ClientTask::create([
            'project_id' => $project->id,
            'preset_task_id' => $presetTask->id,
            'assigned_by' => $user->id,
            'title' => 'Completed Task',
            'description' => 'Completed task description',
            'due_date' => now()->subDays(1),
            'is_completed' => true,
        ]);

        // Task without due date
        $noDueDateTask = ClientTask::create([
            'project_id' => $project->id,
            'preset_task_id' => $presetTask->id,
            'assigned_by' => $user->id,
            'title' => 'No Due Date Task',
            'description' => 'No due date task description',
        ]);

        $this->assertFalse($futureTask->is_overdue);
        $this->assertTrue($overdueTask->is_overdue);
        $this->assertFalse($completedTask->is_overdue);
        $this->assertFalse($noDueDateTask->is_overdue);
    }

    public function test_client_task_status_attribute()
    {
        $project = $this->createTestProject();
        $presetTask = PresetTask::factory()->create();
        $user = User::factory()->create();

        // Completed task
        $completedTask = ClientTask::create([
            'project_id' => $project->id,
            'preset_task_id' => $presetTask->id,
            'assigned_by' => $user->id,
            'title' => 'Completed Task',
            'description' => 'Completed task description',
            'is_completed' => true,
        ]);

        // Overdue task
        $overdueTask = ClientTask::create([
            'project_id' => $project->id,
            'preset_task_id' => $presetTask->id,
            'assigned_by' => $user->id,
            'title' => 'Overdue Task',
            'description' => 'Overdue task description',
            'due_date' => now()->subDays(1),
        ]);

        // Pending task
        $pendingTask = ClientTask::create([
            'project_id' => $project->id,
            'preset_task_id' => $presetTask->id,
            'assigned_by' => $user->id,
            'title' => 'Pending Task',
            'description' => 'Pending task description',
            'due_date' => now()->addDays(1),
        ]);

        $this->assertEquals('completed', $completedTask->status);
        $this->assertEquals('overdue', $overdueTask->status);
        $this->assertEquals('pending', $pendingTask->status);
    }

    public function test_client_task_date_casting()
    {
        $project = $this->createTestProject();
        $presetTask = PresetTask::factory()->create();
        $user = User::factory()->create();

        $dueDate = now()->addDays(7);
        $completedAt = now();

        $clientTask = ClientTask::create([
            'project_id' => $project->id,
            'preset_task_id' => $presetTask->id,
            'assigned_by' => $user->id,
            'title' => 'Test Task',
            'description' => 'Test description',
            'due_date' => $dueDate,
            'completed_at' => $completedAt,
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $clientTask->due_date);
        $this->assertInstanceOf(\Carbon\Carbon::class, $clientTask->completed_at);
        $this->assertEquals($dueDate->format('Y-m-d H:i:s'), $clientTask->due_date->format('Y-m-d H:i:s'));
        $this->assertEquals($completedAt->format('Y-m-d H:i:s'), $clientTask->completed_at->format('Y-m-d H:i:s'));
    }

    public function test_client_task_boolean_casting()
    {
        $project = $this->createTestProject();
        $presetTask = PresetTask::factory()->create();
        $user = User::factory()->create();

        $clientTask = ClientTask::create([
            'project_id' => $project->id,
            'preset_task_id' => $presetTask->id,
            'assigned_by' => $user->id,
            'title' => 'Test Task',
            'description' => 'Test description',
            'is_completed' => 1,
        ]);

        $this->assertIsBool($clientTask->is_completed);
        $this->assertTrue($clientTask->is_completed);
    }
}