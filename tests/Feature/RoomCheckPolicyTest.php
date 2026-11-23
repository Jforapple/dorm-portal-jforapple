
<?php

use App\Enums\Role;
use App\Models\RoomCheck;
use App\Models\User;
use App\Policies\RoomCheckPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->policy = new RoomCheckPolicy;

    $this->roomCheck = RoomCheck::create([
        'room_id' => 101,
        'student_id' => 1,
        'student_name' => 'Test Student',
    ]);
});

test('admin can view and manage room inspections', function () {
    $user = User::factory()->create([
        'role' => Role::Admin,
    ]);

    expect($this->policy->viewAny($user))->toBeTrue()
        ->and($this->policy->view($user, $this->roomCheck))->toBeTrue()
        ->and($this->policy->create($user))->toBeTrue()
        ->and($this->policy->update($user, $this->roomCheck))->toBeTrue()
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

test('student can view room inspections but cannot manage them', function () {
    $user = User::factory()->create([
        'role' => Role::Student,
    ]);

    expect($this->policy->viewAny($user))->toBeTrue()
        ->and($this->policy->view($user, $this->roomCheck))->toBeTrue()
        ->and($this->policy->create($user))->toBeFalse()
        ->and($this->policy->update($user, $this->roomCheck))->toBeFalse()
        ->and($this->policy->delete($user, $this->roomCheck))->toBeFalse();
});

test('student can view the room inspection list and details without management actions', function () {
    $user = User::factory()->create([
        'role' => Role::Student,
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
    ]);

    $this->actingAs($user)
        ->get(route('roomcheck.create'))
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('roomcheck.edit', $this->roomCheck))
        ->assertForbidden();

    $this->actingAs($user)
        ->post(route('roomcheck.store'), [
            'room_id' => 102,
            'student_id' => 2,
            'student_name' => 'Another Student',
        ])
        ->assertForbidden();

    $this->actingAs($user)
        ->put(route('roomcheck.update', $this->roomCheck), [
            'room_id' => 102,
            'student_id' => 2,
            'student_name' => 'Updated Student',
        ])
        ->assertForbidden();

    $this->actingAs($user)
        ->delete(route('roomcheck.destroy', $this->roomCheck))
        ->assertForbidden();
});
