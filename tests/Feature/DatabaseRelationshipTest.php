<?php

use App\Enums\Role;
use App\Models\Dorm;
use App\Models\Event;
use App\Models\Room;
use App\Models\RoomCheck;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('links a dorm to its rooms', function () {
    $dorm = Dorm::factory()->create();

    $room = Room::factory()->create([
        'dorm_id' => $dorm->id,
    ]);

    expect($dorm->rooms)
        ->toHaveCount(1)
        ->and($dorm->rooms->first()->id)
        ->toBe($room->id);
});

it('links a room to its dorm and users', function () {
    $room = Room::factory()->create();

    $student = User::factory()->create([
        'role' => Role::Student,
        'room_id' => $room->id,
    ]);

    expect($room->dorm->id)
        ->toBe($room->dorm_id)
        ->and($room->users->pluck('id')->all())
        ->toContain($student->id);
});

it('links a student to their room', function () {
    $room = Room::factory()->create();

    $student = User::factory()->create([
        'role' => Role::Student,
        'room_id' => $room->id,
    ]);

    expect($student->room->id)->toBe($room->id);
});

it('links a room inspection to its room and student', function () {
    $room = Room::factory()->create();

    $student = User::factory()->create([
        'role' => Role::Student,
        'room_id' => $room->id,
    ]);

    $roomCheck = RoomCheck::factory()->create([
        'room_id' => $room->id,
        'student_id' => $student->id,
    ]);

    expect($roomCheck->room->id)
        ->toBe($room->id)
        ->and($roomCheck->student->id)
        ->toBe($student->id);
});

it('links a worship event to its dorm', function () {
    $dorm = Dorm::factory()->create();

    $event = Event::create([
        'title' => 'Test Worship',
        'dorm_id' => $dorm->id,
        'date' => '2026-11-30',
        'session' => 'evening',
    ]);

    expect($event->dorm->id)->toBe($dorm->id);
});

it('stores the room capacity and counts assigned students', function () {
    $room = Room::factory()->create([
        'capacity' => 2,
    ]);

    User::factory()->count(2)->create([
        'role' => Role::Student,
        'room_id' => $room->id,
    ]);

    expect($room->capacity)
        ->toBe(2)
        ->and($room->users()->count())
        ->toBe(2)
        ->and($room->capacity - $room->users()->count())
        ->toBe(0);
});
