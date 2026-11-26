<?php

use App\Enums\Role;
use App\Models\CleaningDuty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('staff can create a cleaning duty for a student', function () {
    $staff = User::factory()->create([
        'role' => Role::Staff,
    ]);

    $student = User::factory()->create([
        'role' => Role::Student,
    ]);

    $this->actingAs($staff)
        ->post(route('cleaning-duties.store'), [
            'area' => 'Dormitory hallway',
            'user_id' => $student->id,
            'due_date' => today()->addDays(3)->toDateString(),
        ])
        ->assertRedirect(route('cleaning-duties.index'));

    $this->assertDatabaseHas('cleaning_duties', [
        'area' => 'Dormitory hallway',
        'user_id' => $student->id,
        'completed_at' => null,
    ]);
});

test('admin cannot create a cleaning duty', function () {
    $admin = User::factory()->create([
        'role' => Role::Admin,
    ]);

    $student = User::factory()->create([
        'role' => Role::Student,
    ]);

    $this->actingAs($admin)
        ->post(route('cleaning-duties.store'), [
            'area' => 'Common room',
            'user_id' => $student->id,
            'due_date' => today()->addDays(3)->toDateString(),
        ])
        ->assertForbidden();

    expect(CleaningDuty::count())->toBe(0);
});

test('student cannot create a cleaning duty', function () {
    $student = User::factory()->create([
        'role' => Role::Student,
    ]);

    $this->actingAs($student)
        ->post(route('cleaning-duties.store'), [])
        ->assertForbidden();

    expect(CleaningDuty::count())->toBe(0);
});

test('only student accounts can be assigned cleaning duties', function () {
    $staff = User::factory()->create([
        'role' => Role::Staff,
    ]);

    $anotherStaff = User::factory()->create([
        'role' => Role::Staff,
    ]);

    $this->actingAs($staff)
        ->post(route('cleaning-duties.store'), [
            'area' => 'Study area',
            'user_id' => $anotherStaff->id,
            'due_date' => today()->addDays(3)->toDateString(),
        ])
        ->assertSessionHasErrors('user_id');

    expect(CleaningDuty::count())->toBe(0);
});

test('student sees only their own cleaning duties', function () {
    $student = User::factory()->create([
        'role' => Role::Student,
    ]);

    $otherStudent = User::factory()->create([
        'role' => Role::Student,
    ]);

    $ownDuty = CleaningDuty::factory()->create([
        'user_id' => $student->id,
        'area' => 'My hallway',
    ]);

    CleaningDuty::factory()->create([
        'user_id' => $otherStudent->id,
        'area' => 'Other hallway',
    ]);

    $this->actingAs($student)
        ->get(route('cleaning-duties.index'))
        ->assertOk()
        ->assertSee('My hallway')
        ->assertDontSee('Other hallway');
});

test('student can complete their own cleaning duty', function () {
    $student = User::factory()->create([
        'role' => Role::Student,
    ]);

    $duty = CleaningDuty::factory()->create([
        'user_id' => $student->id,
        'completed_at' => null,
    ]);

    $this->actingAs($student)
        ->patch(route('cleaning-duties.complete', $duty))
        ->assertRedirect(route('cleaning-duties.index'));

    expect($duty->fresh()->completed_at)->not->toBeNull();
});

test('student cannot complete another students cleaning duty', function () {
    $student = User::factory()->create([
        'role' => Role::Student,
    ]);

    $otherStudent = User::factory()->create([
        'role' => Role::Student,
    ]);

    $duty = CleaningDuty::factory()->create([
        'user_id' => $otherStudent->id,
        'completed_at' => null,
    ]);

    $this->actingAs($student)
        ->patch(route('cleaning-duties.complete', $duty))
        ->assertForbidden();

    expect($duty->fresh()->completed_at)->toBeNull();
});

test('completed cleaning duty cannot be completed again', function () {
    $student = User::factory()->create([
        'role' => Role::Student,
    ]);

    $duty = CleaningDuty::factory()->create([
        'user_id' => $student->id,
        'completed_at' => now(),
    ]);

    $this->actingAs($student)
        ->patch(route('cleaning-duties.complete', $duty))
        ->assertForbidden();
});

test('overdue cleaning duty is identified correctly', function () {
    $student = User::factory()->create([
        'role' => Role::Student,
    ]);

    $overdueDuty = CleaningDuty::factory()->create([
        'user_id' => $student->id,
        'due_date' => today()->subDay(),
        'completed_at' => null,
    ]);

    $pendingDuty = CleaningDuty::factory()->create([
        'user_id' => $student->id,
        'due_date' => today()->addDay(),
        'completed_at' => null,
    ]);

    expect($overdueDuty->isOverdue())->toBeTrue()
        ->and($pendingDuty->isOverdue())->toBeFalse();
});

test('completed cleaning duty is not considered overdue', function () {
    $student = User::factory()->create([
        'role' => Role::Student,
    ]);

    $duty = CleaningDuty::factory()->create([
        'user_id' => $student->id,
        'due_date' => today()->subDay(),
        'completed_at' => now(),
    ]);

    expect($duty->isOverdue())->toBeFalse();
});

test('staff can view all cleaning duties', function () {
    $staff = User::factory()->create([
        'role' => Role::Staff,
    ]);

    $firstStudent = User::factory()->create([
        'role' => Role::Student,
    ]);

    $secondStudent = User::factory()->create([
        'role' => Role::Student,
    ]);

    CleaningDuty::factory()->create([
        'user_id' => $firstStudent->id,
        'area' => 'First hallway',
    ]);

    CleaningDuty::factory()->create([
        'user_id' => $secondStudent->id,
        'area' => 'Second hallway',
    ]);

    $this->actingAs($staff)
        ->get(route('cleaning-duties.index'))
        ->assertOk()
        ->assertSee('First hallway')
        ->assertSee('Second hallway');
});
