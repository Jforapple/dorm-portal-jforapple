<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('all authenticated roles can access their own profile', function () {
    foreach ([Role::Admin, Role::Staff, Role::Student] as $role) {
        $user = User::factory()->create([
            'role' => $role,
        ]);

        $this->actingAs($user)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSee($user->name)
            ->assertSee($user->email);
    }
});

test('user can update their own name without changing their password', function () {
    $user = User::factory()->create([
        'password' => 'old-password',
    ]);

    $this->actingAs($user)
        ->put(route('profile.update'), [
            'name' => 'Updated Profile Name',
            'password' => '',
            'password_confirmation' => '',
        ])
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHas('status', 'Profile updated successfully.');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Profile Name',
    ]);

    expect(Hash::check('old-password', $user->fresh()->password))->toBeTrue();
});

test('user can change their password with valid confirmation', function () {
    $user = User::factory()->create([
        'password' => 'old-password',
    ]);

    $this->actingAs($user)
        ->put(route('profile.update'), [
            'name' => $user->name,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertRedirect(route('profile.edit'));

    expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue();
});

test('user cannot change password without matching confirmation', function () {
    $user = User::factory()->create([
        'password' => 'old-password',
    ]);

    $this->actingAs($user)
        ->put(route('profile.update'), [
            'name' => $user->name,
            'password' => 'new-password',
            'password_confirmation' => 'different-password',
        ])
        ->assertSessionHasErrors('password');

    expect(Hash::check('old-password', $user->fresh()->password))->toBeTrue();
});

test('guest cannot access the profile page', function () {
    $this->get(route('profile.edit'))
        ->assertRedirect(route('login'));

    $this->put(route('profile.update'), [
        'name' => 'Updated Name',
    ])->assertRedirect(route('login'));
});
