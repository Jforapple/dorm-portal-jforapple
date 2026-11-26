<?php

use App\Enums\Role;
use App\Models\Room;
use App\Models\RoomCheck;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create([
        'role' => Role::Staff,
    ]));
});

function existingRoomCheck(): RoomCheck
{
    return RoomCheck::factory()->create();
}

it('shows an edit form with the current inspection values', function () {
    $roomCheck = existingRoomCheck();

    $this->get(route('roomcheck.edit', $roomCheck->id))
        ->assertOk()
        ->assertSee('value="'.$roomCheck->room_id.'"', false)
        ->assertSee('value="'.$roomCheck->student_id.'"', false)
        ->assertSee($roomCheck->student->name)
        ->assertSee($roomCheck->inspection_date->format('Y-m-d'))
        ->assertSee((string) $roomCheck->score)
        ->assertSee(route('roomcheck.update', $roomCheck->id), false);
});

it('updates the selected room inspection and its details', function () {
    $roomCheck = existingRoomCheck();
    $otherRoomCheck = existingRoomCheck();

    $newRoom = Room::factory()->create();

    $newStudent = User::factory()->create([
        'role' => Role::Student,
        'room_id' => $newRoom->id,
    ]);

    $data = [
        'room_id' => $newRoom->id,
        'student_id' => $newStudent->id,
        'inspection_date' => '2026-11-26',
        'checklist' => [
            'bed' => 'pass',
            'floor' => 'pass',
            'desk' => 'pass',
            'trash' => 'fail',
            'bathroom' => 'pass',
        ],
        'score' => 90,
        'notes' => 'The room is generally clean.',
    ];

    $this->put(route('roomcheck.update', $roomCheck->id), $data)
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('roomcheck.index'));

    $roomCheck->refresh();

    expect($roomCheck->room_id)->toBe($newRoom->id)
        ->and($roomCheck->student_id)->toBe($newStudent->id)
        ->and($roomCheck->inspection_date->format('Y-m-d'))->toBe('2026-11-26')
        ->and($roomCheck->checklist)->toBe($data['checklist'])
        ->and($roomCheck->score)->toBe(90)
        ->and($roomCheck->notes)->toBe('The room is generally clean.')
        ->and($roomCheck->inspector_id)->toBe(auth()->id());

    $this->assertDatabaseHas('room_checks', [
        'id' => $otherRoomCheck->id,
        'room_id' => $otherRoomCheck->room_id,
        'student_id' => $otherRoomCheck->student_id,
    ]);
});

it('rejects invalid updates and preserves the existing record', function () {
    $roomCheck = existingRoomCheck();

    $this->from(route('roomcheck.edit', $roomCheck->id))
        ->put(route('roomcheck.update', $roomCheck->id), [
            'room_id' => 'invalid',
            'student_id' => '',
        ])
        ->assertRedirect(route('roomcheck.edit', $roomCheck->id))
        ->assertSessionHasErrors([
            'room_id',
            'student_id',
            'inspection_date',
            'checklist',
            'score',
        ]);

    $this->assertDatabaseHas('room_checks', [
        'id' => $roomCheck->id,
        'room_id' => $roomCheck->room_id,
        'student_id' => $roomCheck->student_id,
    ]);
});

it('returns 404 for missing room inspections', function () {
    $this->get(route('roomcheck.edit', 999))->assertNotFound();

    $this->put(route('roomcheck.update', 999), [])->assertNotFound();
});
