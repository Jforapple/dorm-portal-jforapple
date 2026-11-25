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

function validRoomCheckData(Room $room, User $student): array
{
    return [
        'room_id' => $room->id,
        'student_id' => $student->id,
        'inspection_date' => '2026-11-26',
        'checklist' => [
            'bed' => 'pass',
            'floor' => 'pass',
            'desk' => 'fail',
            'trash' => 'pass',
            'bathroom' => 'fail',
        ],
        'score' => 80,
        'notes' => 'Some cleaning is required.',
    ];
}

it('stores a valid room inspection with its details and staff inspector', function () {
    $room = Room::factory()->create();

    $student = User::factory()->create([
        'role' => Role::Student,
        'room_id' => $room->id,
    ]);

    $data = validRoomCheckData($room, $student);

    $this->post(route('roomcheck.store'), $data)
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('roomcheck.index'));

    $roomCheck = RoomCheck::firstOrFail();

    expect($roomCheck->room_id)->toBe($room->id)
        ->and($roomCheck->student_id)->toBe($student->id)
        ->and($roomCheck->inspection_date->format('Y-m-d'))->toBe('2026-11-26')
        ->and($roomCheck->checklist)->toBe($data['checklist'])
        ->and($roomCheck->score)->toBe(80)
        ->and($roomCheck->notes)->toBe('Some cleaning is required.')
        ->and($roomCheck->inspector_id)->toBe(auth()->id());

    expect($roomCheck->inspector->role)->toBe(Role::Staff);
});

it('rejects a score outside the allowed range', function () {
    $room = Room::factory()->create();

    $student = User::factory()->create([
        'role' => Role::Student,
        'room_id' => $room->id,
    ]);

    $data = validRoomCheckData($room, $student);
    $data['score'] = 101;

    $this->from(route('roomcheck.create'))
        ->post(route('roomcheck.store'), $data)
        ->assertRedirect(route('roomcheck.create'))
        ->assertSessionHasErrors(['score']);

    $this->assertDatabaseCount('room_checks', 0);
});

it('rejects an invalid checklist result', function () {
    $room = Room::factory()->create();

    $student = User::factory()->create([
        'role' => Role::Student,
        'room_id' => $room->id,
    ]);

    $data = validRoomCheckData($room, $student);
    $data['checklist']['bed'] = 'excellent';

    $this->from(route('roomcheck.create'))
        ->post(route('roomcheck.store'), $data)
        ->assertRedirect(route('roomcheck.create'))
        ->assertSessionHasErrors(['checklist.bed']);

    $this->assertDatabaseCount('room_checks', 0);
});

it('rejects invalid room inspection data without saving', function () {
    $this->from(route('roomcheck.create'))
        ->post(route('roomcheck.store'), [
            'room_id' => 'invalid',
            'student_id' => '1.5',
        ])
        ->assertRedirect(route('roomcheck.create'))
        ->assertSessionHasErrors([
            'room_id',
            'student_id',
            'inspection_date',
            'checklist',
            'score',
        ]);

    $this->assertDatabaseCount('room_checks', 0);
});
