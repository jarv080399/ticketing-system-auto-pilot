<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketCreatedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TicketControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create(['role' => 'user']);
        $this->category = Category::factory()->create();
    }

    /** @test */
    public function user_can_list_their_tickets()
    {
        Ticket::factory()->count(3)->create(['requester_id' => $this->user->id]);
        Ticket::factory()->count(2)->create(); // Other tickets

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/tickets/my-tickets');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function user_can_create_a_ticket()
    {
        Notification::fake();
        Storage::fake('public');

        $payload = [
            'title' => 'Test Ticket Title',
            'description' => 'Test ticket description content.',
            'category_id' => $this->category->id,
            'priority' => 'high',
            'attachments' => [
                UploadedFile::fake()->image('screenshot.png'),
            ]
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/tickets', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'Test Ticket Title')
            ->assertJsonPath('data.status', 'new');

        $this->assertDatabaseHas('tickets', [
            'title' => 'Test Ticket Title',
            'requester_id' => $this->user->id,
        ]);

        $ticket = Ticket::first();
        $this->assertCount(1, $ticket->attachments);
        
        Notification::assertSentTo($this->user, TicketCreatedNotification::class);
    }

    /** @test */
    public function user_can_view_their_own_ticket()
    {
        $ticket = Ticket::factory()->create(['requester_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/tickets/{$ticket->ticket_number}");

        $response->assertStatus(200)
            ->assertJsonPath('data.ticket_number', $ticket->ticket_number);
    }

    /** @test */
    public function user_cannot_view_others_ticket()
    {
        $otherUser = User::factory()->create(['role' => 'user']);
        $ticket = Ticket::factory()->create(['requester_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/tickets/{$ticket->ticket_number}");

        $response->assertStatus(403);
    }

    /** @test */
    public function user_can_check_for_duplicates()
    {
        Ticket::factory()->create([
            'requester_id' => $this->user->id,
            'title' => 'Duplicate Title'
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/tickets/check-duplicate', ['title' => 'Duplicate Title']);

        $response->assertStatus(200)
            ->assertJsonPath('is_duplicate', true);
    }
}
