<?php

use App\Enums\Role;
use App\Models\Event;
use App\Models\User;
use App\Policies\EventPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->policy = new EventPolicy;

    $this->event = Event::create([
        'title' => 'Dorm Worship',
        'dorm' => 'Elijah Hall',
        'date' => '2026-11-19',
        'session' => 'morning',
    ]);
});

test('admin can view and manage events', function () {
    $user = User::factory()->create([
        'role' => Role::Admin,
    ]);

    expect($this->policy->viewAny($user))->toBeTrue()
        ->and($this->policy->view($user, $this->event))->toBeTrue()
        ->and($this->policy->create($user))->toBeTrue()
        ->and($this->policy->update($user, $this->event))->toBeTrue()
        ->and($this->policy->delete($user, $this->event))->toBeTrue();
});

test('staff can view and manage events', function () {
    $user = User::factory()->create([
        'role' => Role::Staff,
    ]);

    expect($this->policy->viewAny($user))->toBeTrue()
        ->and($this->policy->view($user, $this->event))->toBeTrue()
        ->and($this->policy->create($user))->toBeTrue()
        ->and($this->policy->update($user, $this->event))->toBeTrue()
        ->and($this->policy->delete($user, $this->event))->toBeTrue();
});

test('student can view events but cannot manage them', function () {
    $user = User::factory()->create([
        'role' => Role::Student,
    ]);

    expect($this->policy->viewAny($user))->toBeTrue()
        ->and($this->policy->view($user, $this->event))->toBeTrue()
        ->and($this->policy->create($user))->toBeFalse()
        ->and($this->policy->update($user, $this->event))->toBeFalse()
        ->and($this->policy->delete($user, $this->event))->toBeFalse();
});

test('student can view the event list and details without management actions', function () {
    $user = User::factory()->create([
        'role' => Role::Student,
    ]);

    $this->actingAs($user)
        ->get(route('events.index'))
        ->assertOk()
        ->assertDontSee('Create worship event')
        ->assertDontSee('Edit')
        ->assertDontSee('Delete');

    $this->actingAs($user)
        ->get(route('events.show', $this->event))
        ->assertOk();
});

test('student cannot access event management actions', function () {
    $user = User::factory()->create([
        'role' => Role::Student,
    ]);

    $this->actingAs($user)
        ->get(route('events.create'))
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('events.edit', $this->event))
        ->assertForbidden();

    $this->actingAs($user)
        ->post(route('events.store'), [
            'title' => 'Another Worship Event',
            'dorm' => 'Esther Hall',
            'date' => '2026-11-20',
            'session' => 'morning',
        ])
        ->assertForbidden();

    $this->actingAs($user)
        ->put(route('events.update', $this->event), [
            'title' => 'Updated Worship Event',
            'dorm' => 'Esther Hall',
            'date' => '2026-11-20',
            'session' => 'morning',
        ])
        ->assertForbidden();

    $this->actingAs($user)
        ->delete(route('events.destroy', $this->event))
        ->assertForbidden();
});
