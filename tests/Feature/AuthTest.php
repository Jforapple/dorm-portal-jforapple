
<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('a user can log in with correct credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
    ]);

    $this->post(route('login.store'), [
        'email' => 'test@example.com',
        'password' => 'password',
    ])
        ->assertRedirect(route('home'));

    $this->assertAuthenticatedAs($user);
});

test('a user cannot log in with incorrect credentials', function () {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
    ]);

    $this->post(route('login.store'), [
        'email' => 'test@example.com',
        'password' => 'wrong-password',
    ])
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('an authenticated user can log out', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});

test('guests are redirected when accessing protected modules', function () {
    $this->get(route('roomcheck.index'))
        ->assertRedirect(route('login'));

    $this->get(route('events.index'))
        ->assertRedirect(route('login'));
});
