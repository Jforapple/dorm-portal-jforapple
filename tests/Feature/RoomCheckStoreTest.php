<?php

use App\Enums\Role;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Feature tests for creating room inspections.
 * RefreshDatabase gives each test an empty in-memory SQLite database.
 */
uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create([
        'role' => Role::Staff,
    ]));
});

it('stores a valid room check and redirects to the list', function () {
    $room = Room::factory()->create();

    $student = User::factory()->create([
        'role' => Role::Student,
        'room_id' => $room->id,
    ]);

    $data = [
        'room_id' => $room->id,
        'student_id' => $student->id,
    ];

    $this->post(route('roomcheck.store'), $data)
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('roomcheck.index'));

    $this->assertDatabaseHas('room_checks', $data);
});

it('rejects invalid room check data without saving', function () {
    $this->from(route('roomcheck.create'))
        ->post(route('roomcheck.store'), [
            'room_id' => 'invalid',
            'student_id' => '1.5',
        ])
        ->assertRedirect(route('roomcheck.create'))
        ->assertSessionHasErrors(['room_id', 'student_id']);

    $this->assertDatabaseCount('room_checks', 0);
});
