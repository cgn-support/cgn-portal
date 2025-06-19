<?php

namespace Tests\Unit;

use App\Models\PresetTask;
use App\Models\ClientTask;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PresetTaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_preset_task_can_be_created()
    {
        $presetTask = PresetTask::create([
            'title' => 'Test Task',
            'description' => 'This is a test task description',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->assertInstanceOf(PresetTask::class, $presetTask);
        $this->assertEquals('Test Task', $presetTask->title);
        $this->assertEquals('This is a test task description', $presetTask->description);
        $this->assertEquals(1, $presetTask->sort_order);
        $this->assertTrue($presetTask->is_active);
    }

    public function test_preset_task_has_default_values()
    {
        $presetTask = PresetTask::create([
            'title' => 'Test Task',
            'description' => 'Test description',
        ]);

        $presetTask->refresh(); // Refresh to get database defaults
        
        $this->assertTrue($presetTask->is_active);
        $this->assertEquals(0, $presetTask->sort_order);
    }

    public function test_preset_task_can_be_inactive()
    {
        $presetTask = PresetTask::create([
            'title' => 'Inactive Task',
            'description' => 'This task is inactive',
            'is_active' => false,
        ]);

        $this->assertFalse($presetTask->is_active);
    }

    public function test_preset_task_has_client_tasks_relationship()
    {
        $presetTask = PresetTask::create([
            'title' => 'Test Task',
            'description' => 'Test description',
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $presetTask->clientTasks());
    }

    public function test_active_scope_returns_only_active_tasks()
    {
        PresetTask::create([
            'title' => 'Active Task',
            'description' => 'Active task',
            'is_active' => true,
        ]);

        PresetTask::create([
            'title' => 'Inactive Task',
            'description' => 'Inactive task',
            'is_active' => false,
        ]);

        $activeTasks = PresetTask::active()->get();

        $this->assertCount(1, $activeTasks);
        $this->assertEquals('Active Task', $activeTasks->first()->title);
    }

    public function test_ordered_scope_returns_tasks_in_sort_order()
    {
        PresetTask::create([
            'title' => 'Third Task',
            'description' => 'Third',
            'sort_order' => 3,
        ]);

        PresetTask::create([
            'title' => 'First Task',
            'description' => 'First',
            'sort_order' => 1,
        ]);

        PresetTask::create([
            'title' => 'Second Task',
            'description' => 'Second',
            'sort_order' => 2,
        ]);

        $orderedTasks = PresetTask::ordered()->get();

        $this->assertEquals('First Task', $orderedTasks[0]->title);
        $this->assertEquals('Second Task', $orderedTasks[1]->title);
        $this->assertEquals('Third Task', $orderedTasks[2]->title);
    }

    public function test_ordered_scope_falls_back_to_title_when_sort_order_same()
    {
        PresetTask::create([
            'title' => 'Z Task',
            'description' => 'Z Task',
            'sort_order' => 1,
        ]);

        PresetTask::create([
            'title' => 'A Task',
            'description' => 'A Task',
            'sort_order' => 1,
        ]);

        $orderedTasks = PresetTask::ordered()->get();

        $this->assertEquals('A Task', $orderedTasks[0]->title);
        $this->assertEquals('Z Task', $orderedTasks[1]->title);
    }

    public function test_preset_task_is_castable_to_boolean()
    {
        $presetTask = PresetTask::create([
            'title' => 'Test Task',
            'description' => 'Test description',
            'is_active' => 1,
        ]);

        $this->assertIsBool($presetTask->is_active);
        $this->assertTrue($presetTask->is_active);
    }

    public function test_preset_task_fillable_attributes()
    {
        $attributes = [
            'title' => 'Fillable Test',
            'description' => 'Testing fillable attributes',
            'is_active' => true,
            'sort_order' => 5,
        ];

        $presetTask = PresetTask::create($attributes);

        foreach ($attributes as $key => $value) {
            $this->assertEquals($value, $presetTask->$key);
        }
    }
}