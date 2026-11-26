<?php

use App\Enums\Role;
use App\Models\Room;
use App\Models\RoomCheck;
use App\Models\User;
use App\Policies\RoomCheckPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->policy = new RoomCheckPolicy;

    $this->roomCheck = RoomCheck::factory()->create();
});

test('admin can view and delete room inspections but cannot create or edit them', function () {
    $user = User::factory()->create([
        'role' => Role::Admin,
    ]);

    expect($this->policy->viewAny($user))->toBeTrue()
        ->and($this->policy->view($user, $this->roomCheck))->toBeTrue()
        ->and($this->policy->create($user))->toBeFalse()
        ->and($this->policy->update($user, $this->roomCheck))->toBeFalse()
        ->and($this->policy->delete($user, $this->roomCheck))->toBeTrue();
});

test('staff can view and manage room inspections', function () {
    $user = User::factory()->create([
        'role' => Role::Staff,
    ]);

    expect($this->policy->viewAny($user))->toBeTrue()
        ->and($this->policy->view($user, $this->roomCheck))->toBeTrue()
        ->and($this->policy->create($user))->toBeTrue()
        ->and($this->policy->update($user, $this->roomCheck))->toBeTrue()
        ->and($this->policy->delete($user, $this->roomCheck))->toBeTrue();
});

test('student can view only inspections belonging to their own room', function () {
    $user = User::factory()->create([
        'role' => Role::Student,
        'room_id' => $this->roomCheck->room_id,
    ]);

    expect($this->policy->viewAny($user))->toBeTrue()
        ->and($this->policy->view($user, $this->roomCheck))->toBeTrue()
        ->and($this->policy->create($user))->toBeFalse()
        ->and($this->policy->update($user, $this->roomCheck))->toBeFalse()
        ->and($this->policy->delete($user, $this->roomCheck))->toBeFalse();

    $otherRoom = Room::factory()->create();

    $otherRoomCheck = RoomCheck::factory()->create([
        'room_id' => $otherRoom->id,
    ]);

    expect($this->policy->view($user, $otherRoomCheck))->toBeFalse();
});

test('student can view the room inspection list and details without management actions', function () {
    $user = User::factory()->create([
        'role' => Role::Student,
        'room_id' => $this->roomCheck->room_id,
    ]);

    $this->actingAs($user)
        ->get(route('roomcheck.index'))
        ->assertOk()
        ->assertDontSee('Add inspection')
        ->assertDontSee('Edit')
        ->assertDontSee('Delete');

    $this->actingAs($user)
        ->get(route('roomcheck.show', $this->roomCheck))
        ->assertOk()
        ->assertDontSee('Edit inspection');
});

test('student cannot access room inspection management actions', function () {
    $user = User::factory()->create([
        'role' => Role::Student,
        'room_id' => $this->roomCheck->room_id,
    ]);

    $this->actingAs($user)
        ->get(route('roomcheck.create'))
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('roomcheck.edit', $this->roomCheck))
        ->assertForbidden();

    $this->actingAs($user)
        ->post(route('roomcheck.store'), [])
        ->assertForbidden();

    $this->actingAs($user)
        ->put(route('roomcheck.update', $this->roomCheck), [])
        ->assertForbidden();

    $this->actingAs($user)
        ->delete(route('roomcheck.destroy', $this->roomCheck))
        ->assertForbidden();
});

test('student receives forbidden when opening another rooms inspection', function () {
    $otherRoom = Room::factory()->create();

    $otherRoomCheck = RoomCheck::factory()->create([
        'room_id' => $otherRoom->id,
    ]);

    $student = User::factory()->create([
        'role' => Role::Student,
        'room_id' => $this->roomCheck->room_id,
    ]);

    $this->actingAs($student)
        ->get(route('roomcheck.show', $otherRoomCheck))
        ->assertForbidden();
});

test('student sees only inspections belonging to their own room in the inspection list', function () {
    $ownRoomStudent = User::factory()->create([
        'role' => Role::Student,
        'room_id' => $this->roomCheck->room_id,
    ]);

    $otherRoom = Room::factory()->create();

    $otherRoomCheck = RoomCheck::factory()->create([
        'room_id' => $otherRoom->id,
    ]);

    $student = User::factory()->create([
        'role' => Role::Student,
        'room_id' => $this->roomCheck->room_id,
    ]);

    $this->actingAs($student)
        ->get(route('roomcheck.index'))
        ->assertOk()
        ->assertSee($this->roomCheck->student->name)
        ->assertDontSee($otherRoomCheck->student->name);
});
