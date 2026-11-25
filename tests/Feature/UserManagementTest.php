<?php

use App\Enums\Role;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can access user management pages', function () {
    $admin = User::factory()->create([
        'role' => Role::Admin,
    ]);

    $managedUser = User::factory()->create();

    $this->actingAs($admin)
        ->get(route('users.index'))
        ->assertOk();

    $this->actingAs($admin)
        ->get(route('users.create'))
        ->assertOk();

    $this->actingAs($admin)
        ->get(route('users.show', $managedUser))
        ->assertOk();

    $this->actingAs($admin)
        ->get(route('users.edit', $managedUser))
        ->assertOk();
});

test('staff and students cannot access user management', function () {
    foreach ([Role::Staff, Role::Student] as $role) {
        $user = User::factory()->create([
            'role' => $role,
        ]);

        $this->actingAs($user)
            ->get(route('users.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('users.create'))
            ->assertForbidden();
    }
});

test('admin can create a student and assign a room', function () {
    $admin = User::factory()->create([
        'role' => Role::Admin,
    ]);

    $room = Room::factory()->create([
        'capacity' => 2,
    ]);

    $this->actingAs($admin)
        ->post(route('users.store'), [
            'name' => 'New Student',
            'email' => 'newstudent@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => Role::Student->value,
            'room_id' => $room->id,
        ])
        ->assertRedirect(route('users.index'));

    $this->assertDatabaseHas('users', [
        'name' => 'New Student',
        'email' => 'newstudent@example.com',
        'role' => Role::Student->value,
        'room_id' => $room->id,
    ]);
});

test('admin cannot assign a student to a room that has reached capacity', function () {
    $admin = User::factory()->create([
        'role' => Role::Admin,
    ]);

    $room = Room::factory()->create([
        'capacity' => 1,
    ]);

    User::factory()->create([
        'role' => Role::Student,
        'room_id' => $room->id,
    ]);

    $this->actingAs($admin)
        ->post(route('users.store'), [
            'name' => 'Another Student',
            'email' => 'anotherstudent@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => Role::Student->value,
            'room_id' => $room->id,
        ])
        ->assertSessionHasErrors('room_id');

    $this->assertDatabaseMissing('users', [
        'email' => 'anotherstudent@example.com',
    ]);
});

test('admin can update a user and remove their room assignment when changing their role', function () {
    $admin = User::factory()->create([
        'role' => Role::Admin,
    ]);

    $room = Room::factory()->create();

    $student = User::factory()->create([
        'role' => Role::Student,
        'room_id' => $room->id,
    ]);

    $this->actingAs($admin)
        ->put(route('users.update', $student), [
            'name' => 'Updated Student',
            'email' => $student->email,
            'role' => Role::Staff->value,
            'room_id' => '',
            'password' => '',
            'password_confirmation' => '',
        ])
        ->assertRedirect(route('users.index'));

    $this->assertDatabaseHas('users', [
        'id' => $student->id,
        'name' => 'Updated Student',
        'role' => Role::Staff->value,
        'room_id' => null,
    ]);
});

test('admin cannot delete their own account but can delete another user', function () {
    $admin = User::factory()->create([
        'role' => Role::Admin,
    ]);

    $anotherUser = User::factory()->create();

    $this->actingAs($admin)
        ->delete(route('users.destroy', $admin))
        ->assertForbidden();

    $this->assertDatabaseHas('users', [
        'id' => $admin->id,
    ]);

    $this->actingAs($admin)
        ->delete(route('users.destroy', $anotherUser))
        ->assertRedirect(route('users.index'));

    $this->assertDatabaseMissing('users', [
        'id' => $anotherUser->id,
    ]);
});

test('staff and students cannot create, update, or delete users', function () {
    $managedUser = User::factory()->create();

    foreach ([Role::Staff, Role::Student] as $role) {
        $user = User::factory()->create([
            'role' => $role,
        ]);

        $this->actingAs($user)
            ->post(route('users.store'), [
                'name' => 'Unauthorized User',
                'email' => 'unauthorized@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => Role::Student->value,
                'room_id' => '',
            ])
            ->assertForbidden();

        $this->actingAs($user)
            ->put(route('users.update', $managedUser), [
                'name' => 'Updated Name',
                'email' => $managedUser->email,
                'role' => Role::Student->value,
                'room_id' => '',
            ])
            ->assertForbidden();

        $this->actingAs($user)
            ->delete(route('users.destroy', $managedUser))
            ->assertForbidden();
    }

    $this->assertDatabaseMissing('users', [
        'email' => 'unauthorized@example.com',
    ]);

    $this->assertDatabaseHas('users', [
        'id' => $managedUser->id,
    ]);
});

test('admin cannot move a student into a room that has reached capacity', function () {
    $admin = User::factory()->create([
        'role' => Role::Admin,
    ]);

    $fullRoom = Room::factory()->create([
        'capacity' => 1,
    ]);

    $availableRoom = Room::factory()->create([
        'capacity' => 2,
    ]);

    User::factory()->create([
        'role' => Role::Student,
        'room_id' => $fullRoom->id,
    ]);

    $student = User::factory()->create([
        'role' => Role::Student,
        'room_id' => $availableRoom->id,
    ]);

    $this->actingAs($admin)
        ->put(route('users.update', $student), [
            'name' => $student->name,
            'email' => $student->email,
            'role' => Role::Student->value,
            'room_id' => $fullRoom->id,
            'password' => '',
            'password_confirmation' => '',
        ])
        ->assertSessionHasErrors('room_id');

    $this->assertDatabaseHas('users', [
        'id' => $student->id,
        'room_id' => $availableRoom->id,
    ]);
});
