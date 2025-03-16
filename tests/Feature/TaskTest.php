<?php
//
//namespace Tests\Feature;
//
//use App\Models\User;
//use App\Models\Task;
//use Illuminate\Foundation\Testing\RefreshDatabase;
//use Tests\TestCase;
//
//class TaskTest extends TestCase
//{
//    use RefreshDatabase;
//
//    private $user;
//    private $token;
//
//    protected function setUp(): void
//    {
//        parent::setUp();
//        $this->user = User::factory()->create();
//        $this->token = $this->user->createToken('test-token')->plainTextToken;
//    }
//
//    public function test_user_can_create_task()
//    {
//        $response = $this->postJson('/api/tasks', [
//            'title' => 'New Task',
//            'description' => 'Task Description',
//            'status' => 'pending'
//        ], [
//            'Authorization' => 'Bearer ' . $this->token
//        ]);
//
//        $response->assertStatus(201)
//            ->assertJsonStructure(['task', 'success', 'message']);
//    }
//
//    public function test_user_can_view_tasks()
//    {
//        Task::factory()->count(3)->create(['user_id' => $this->user->id]);
//
//        $response = $this->getJson('/api/tasks', [
//            'Authorization' => 'Bearer ' . $this->token
//        ]);
//
//        $response->assertStatus(200)
//            ->assertJsonCount(3);
//    }
//
//    public function test_user_can_update_task()
//    {
//        $task = Task::factory()->create(['user_id' => $this->user->id]);
//
//        $response = $this->putJson("/api/tasks/{$task->id}", [
//            'title' => 'Updated Task',
//            'description' => 'Updated Description',
//            'status' => 'in progress'
//        ], [
//            'Authorization' => 'Bearer ' . $this->token
//        ]);
//
//        $response->assertStatus(200)
//            ->assertJson(['title' => 'Updated Task', 'status' => 'in progress']);
//    }
//
//    public function test_user_can_delete_task()
//    {
//        $user = User::factory()->create();
//        $token = $user->createToken('API Token')->plainTextToken;
//
//        $task = Task::factory()->create(['user_id' => $user->id]);
//
//        $response = $this->withHeaders([
//            'Authorization' => 'Bearer ' . $token,
//        ])->deleteJson("/api/tasks/{$task->id}");
//
//        $response->assertStatus(200)
//            ->assertJson([
//                'success' => true,
//                'message' => 'task deleted successfully',
//            ]);
//
//        $this->assertDatabaseMissing('tasks', [
//            'id' => $task->id,
//        ]);
//    }
//}

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function it_creates_a_task_successfully()
    {
        $user = User::factory()->create();
        $token = $user->createToken('API Token')->plainTextToken;

        $taskData = [
            'title' => 'Test Task',
            'description' => 'This is a test task',
            'status' => 'pending',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'task create successfully',
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function it_fails_to_create_a_task_with_invalid_data()
    {
        $user = User::factory()->create();
        $token = $user->createToken('API Token')->plainTextToken;

        $taskData = [
            'title' => '',
            'description' => 'This is a test task',
            'status' => 'invalid_status',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/tasks', $taskData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'status']);
    }

    /** @test */
    public function it_retrieves_all_tasks()
    {
        $user = User::factory()->create();
        $token = $user->createToken('API Token')->plainTextToken;

        Task::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'all tasks',
            ])
            ->assertJsonCount(3, 'tasks');
    }

    /** @test */
    public function it_retrieves_a_specific_task()
    {
        $user = User::factory()->create();
        $token = $user->createToken('API Token')->plainTextToken;

        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'task',
                'task' => [
                    'id' => $task->id,
                    'title' => $task->title,
                ],
            ]);
    }

    /** @test */
    public function it_fails_to_retrieve_a_nonexistent_task()
    {
        $user = User::factory()->create();
        $token = $user->createToken('API Token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/tasks/999');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_updates_a_task_successfully()
    {
        $user = User::factory()->create();
        $token = $user->createToken('API Token')->plainTextToken;

        $task = Task::factory()->create(['user_id' => $user->id]);

        $updatedData = [
            'title' => 'Updated Task Title',
            'status' => 'completed',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/tasks/{$task->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'task update successfully',
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task Title',
            'status' => 'completed',
        ]);
    }

    /** @test */
    public function it_fails_to_update_a_nonexistent_task()
    {
        $user = User::factory()->create();
        $token = $user->createToken('API Token')->plainTextToken;

        $updatedData = [
            'title' => 'Updated Task Title',
            'status' => 'completed',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/tasks/999', $updatedData); // مهمة غير موجودة

        $response->assertStatus(422);
    }

    /** @test */
    public function it_fails_to_update_another_users_task()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $token = $user1->createToken('API Token')->plainTextToken;

        $task = Task::factory()->create(['user_id' => $user2->id]);

        $updatedData = [
            'title' => 'Updated Task Title',
            'status' => 'completed',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/tasks/{$task->id}", $updatedData);

        $response->assertStatus(422);
    }

    /** @test */
    public function it_deletes_a_task_successfully()
    {
        $user = User::factory()->create();
        $token = $user->createToken('API Token')->plainTextToken;

        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'task deleted successfully',
            ]);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    public function it_fails_to_delete_a_nonexistent_task()
    {
        $user = User::factory()->create();
        $token = $user->createToken('API Token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/tasks/999');

        $response->assertStatus(422);
    }

}
