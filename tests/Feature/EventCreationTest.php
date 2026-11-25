<?php

use App\Enums\Role;
use App\Models\Dorm;
use App\Models\Event;
use App\Models\User;
use App\Support\ThailandHolidays;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create([
        'role' => Role::Staff,
    ]));

    $this->dorms = collect([
        'Eve Hall',
        'Ruth Hall',
        'Esther Hall',
        'Elijah Hall',
        'Solomon Hall',
    ])->mapWithKeys(fn (string $name) => [
        $name => Dorm::firstOrCreate(['name' => $name]),
    ]);
});

/**
 * Feature tests for creating worship events and every schedule rule in
 * StoreEventRequest. Use this file as the model for your own tests:
 * datasets (->with()) cover many inputs with one test.
 *
 * Run: php artisan test --compact tests/Feature/EventCreationTest.php
 */
it('prevents worship on every listed holiday', function (string $date) {
    $session = CarbonImmutable::parse($date)->isMonday() ? 'evening' : 'morning';

    $this->from(route('events.create'))->post(route('events.store'), [
        'title' => 'Worship',
        'dorm_id' => $this->dorms['Eve Hall']->id,
        'date' => $date,
        'session' => $session,
    ])->assertRedirect(route('events.create'))->assertSessionHasErrors([
        'date' => 'No worship on holidays: '.ThailandHolidays::DATES[$date].'.',
    ]);

    $this->assertDatabaseCount('events', 0);
})->with(array_keys(ThailandHolidays::DATES));

it('does not classify seasons or ordinary days as holidays', function () {
    expect(ThailandHolidays::isHoliday('2026-10-20'))->toBeFalse()
        ->and(ThailandHolidays::isHoliday('2026-12-22'))->toBeFalse()
        ->and(ThailandHolidays::isHoliday('2027-06-21'))->toBeFalse();
});

it('rejects another event for the same dorm and date', function (array $changes) {
    $event = [
        'title' => 'Worship',
        'dorm_id' => $this->dorms['Eve Hall']->id,
        'date' => '2026-10-20',
        'session' => 'morning',
    ];

    Event::create($event);

    $this->from(route('events.create'))->post(route('events.store'), [
        ...$event, ...$changes,
    ])->assertRedirect(route('events.create'))->assertSessionHasErrors('date');

    $this->assertDatabaseCount('events', 1);
})->with([
    'same details' => [[]],
    'different title' => [['title' => 'Prayer']],
]);

it('allows different dorms to worship on the same date and session', function () {
    $event = [
        'title' => 'Worship',
        'dorm_id' => $this->dorms['Eve Hall']->id,
        'date' => '2026-10-20',
        'session' => 'morning',
    ];

    Event::create($event);

    foreach (['Ruth Hall', 'Esther Hall'] as $dorm) {
        $dormId = $this->dorms[$dorm]->id;

        $this->post(route('events.store'), [
            ...$event,
            'dorm_id' => $dormId,
        ])
            ->assertRedirect(route('events.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('events', [
            ...$event,
            'dorm_id' => $dormId,
            'date' => '2026-10-20 00:00:00',
        ]);
    }

    $this->assertDatabaseCount('events', 3);
});

it('rejects the same date even when the existing session differs', function () {
    Event::create([
        'title' => 'Worship',
        'dorm_id' => $this->dorms['Eve Hall']->id,
        'date' => '2026-10-20',
        'session' => 'evening',
    ]);

    $this->post(route('events.store'), [
        'title' => 'Worship',
        'dorm_id' => $this->dorms['Eve Hall']->id,
        'date' => '2026-10-20',
        'session' => 'morning',
    ])->assertSessionHasErrors('date');

    $this->assertDatabaseCount('events', 1);
});

it('allows an event on a different date', function () {
    Event::create([
        'title' => 'Worship',
        'dorm_id' => $this->dorms['Eve Hall']->id,
        'date' => '2026-10-21',
        'session' => 'morning',
    ]);

    $this->post(route('events.store'), [
        'title' => 'Worship',
        'dorm_id' => $this->dorms['Eve Hall']->id,
        'date' => '2026-10-20',
        'session' => 'morning',
    ])->assertRedirect(route('events.index'))->assertSessionHasNoErrors();

    $this->assertDatabaseCount('events', 2);
});

it('shows the event form and an empty event list', function () {
    $this->get(route('events.create'))->assertOk()->assertSee('Create Worship Event');
    $this->get(route('events.index'))->assertOk()->assertSee('No worship events yet.');
});

it('creates worship events for the allowed sessions', function (string $date, string $session) {
    $dormId = $this->dorms['Elijah Hall']->id;

    $this->post(route('events.store'), [
        'title' => 'Dorm Worship',
        'dorm_id' => $dormId,
        'date' => $date,
        'session' => $session,
    ])->assertRedirect(route('events.index'))->assertSessionHasNoErrors();

    $this->assertDatabaseHas('events', [
        'dorm_id' => $dormId,
        'session' => $session,
    ]);

    expect(Event::sole()->date->format('Y-m-d'))->toBe($date);

    $this->get(route('events.index'))
        ->assertOk()
        ->assertSee('Dorm Worship')
        ->assertSee('Elijah Hall');
})->with([
    ['2026-10-19', 'evening'],
    ['2026-10-20', 'morning'],
    ['2026-10-21', 'morning'],
    ['2026-10-22', 'morning'],
    ['2026-10-30', 'morning'],
]);

it('rejects worship outside the schedule', function (string $date, string $session, string $field) {
    $this->from(route('events.create'))->post(route('events.store'), [
        'title' => 'Worship', 'dorm_id' => $this->dorms['Solomon Hall']->id, 'date' => $date, 'session' => $session,
    ])->assertRedirect(route('events.create'))->assertSessionHasErrors($field);

    $this->assertDatabaseCount('events', 0);
})->with([
    ['2026-10-19', 'morning', 'session'],
    ['2026-10-20', 'evening', 'session'],
    ['2026-10-21', 'evening', 'session'],
    ['2026-10-22', 'evening', 'session'],
    ['2026-10-30', 'evening', 'session'],
    ['2026-10-24', 'morning', 'date'],
    ['2026-10-24', 'evening', 'date'],
    ['2026-10-25', 'morning', 'date'],
    ['2026-10-25', 'evening', 'date'],
    ['invalid', 'morning', 'date'],
    ['2026-10-20', 'afternoon', 'session'],
]);

it('requires event details', function () {
    $this->post(route('events.store'), [])
        ->assertSessionHasErrors(['title', 'dorm_id', 'date', 'session']);
    $this->assertDatabaseCount('events', 0);
});

it('rejects unknown dorms', function () {
    $this->post(route('events.store'), [
        'title' => 'Worship',
        'dorm_id' => 999999,
        'date' => '2026-10-20',
        'session' => 'morning',
    ])->assertSessionHasErrors('dorm_id');

    $this->assertDatabaseCount('events', 0);
});

it('shows an existing worship event', function () {
    $event = Event::create([
        'title' => 'Test Worship',
        'dorm_id' => $this->dorms['Elijah Hall']->id,
        'date' => '2026-10-20',
        'session' => 'morning',
    ]);

    $this->get(route('events.show', $event))
        ->assertOk()
        ->assertSee('Test Worship')
        ->assertSee('Elijah Hall')
        ->assertSee('October 20, 2026')
        ->assertSee('Tuesday')
        ->assertSee('Morning');
});

it('updates an existing worship event', function () {
    $event = Event::create([
        'title' => 'Original Worship',
        'dorm_id' => $this->dorms['Elijah Hall']->id,
        'date' => '2026-10-20',
        'session' => 'morning',
    ]);

    $this->put(route('events.update', $event), [
        'title' => 'Updated Worship',
        'dorm_id' => $this->dorms['Elijah Hall']->id,
        'date' => '2026-10-21',
        'session' => 'morning',
    ])
        ->assertRedirect(route('events.index'))
        ->assertSessionHas('status', 'Worship event updated.');

    $this->assertDatabaseHas('events', [
        'id' => $event->id,
        'title' => 'Updated Worship',
        'dorm_id' => $this->dorms['Elijah Hall']->id,
        'date' => '2026-10-21 00:00:00',
        'session' => 'morning',
    ]);
});

it('rejects updates that violate worship scheduling rules', function (string $date, string $session, string $field) {
    $event = Event::create([
        'title' => 'Original Worship',
        'dorm_id' => $this->dorms['Elijah Hall']->id,
        'date' => '2026-10-20',
        'session' => 'morning',
    ]);

    $this->put(route('events.update', $event), [
        'title' => 'Updated Worship',
        'dorm_id' => $this->dorms['Elijah Hall']->id,
        'date' => $date,
        'session' => $session,
    ])
        ->assertSessionHasErrors($field);

    $this->assertDatabaseHas('events', [
        'id' => $event->id,
        'title' => 'Original Worship',
        'date' => '2026-10-20 00:00:00',
        'session' => 'morning',
    ]);
})->with([
    'holiday' => ['2026-12-05', 'morning', 'date'],
    'Saturday' => ['2026-10-24', 'morning', 'date'],
    'Sunday' => ['2026-10-25', 'morning', 'date'],
    'Monday morning' => ['2026-10-19', 'morning', 'session'],
    'Tuesday evening' => ['2026-10-20', 'evening', 'session'],
]);

it('rejects updating an event to another event existing dorm and date', function () {
    Event::create([
        'title' => 'Existing Worship',
        'dorm_id' => $this->dorms['Elijah Hall']->id,
        'date' => '2026-10-20',
        'session' => 'morning',
    ]);

    $event = Event::create([
        'title' => 'Original Worship',
        'dorm_id' => $this->dorms['Ruth Hall']->id,
        'date' => '2026-10-21',
        'session' => 'morning',
    ]);

    $this->put(route('events.update', $event), [
        'title' => 'Updated Worship',
        'dorm_id' => $this->dorms['Elijah Hall']->id,
        'date' => '2026-10-20',
        'session' => 'morning',
    ])->assertSessionHasErrors('date');

    $this->assertDatabaseHas('events', [
        'id' => $event->id,
        'title' => 'Original Worship',
        'dorm_id' => $this->dorms['Ruth Hall']->id,
        'date' => '2026-10-21 00:00:00',
        'session' => 'morning',
    ]);

    $this->assertDatabaseCount('events', 2);
});

it('allows saving an event without changing its information', function () {
    $event = Event::create([
        'title' => 'Original Worship',
        'dorm_id' => $this->dorms['Elijah Hall']->id,
        'date' => '2026-10-20',
        'session' => 'morning',
    ]);

    $this->put(route('events.update', $event), [
        'title' => 'Original Worship',
        'dorm_id' => $this->dorms['Elijah Hall']->id,
        'date' => '2026-10-20',
        'session' => 'morning',
    ])
        ->assertRedirect(route('events.index'))
        ->assertSessionHasNoErrors()
        ->assertSessionHas('status', 'Worship event updated.');

    $this->assertDatabaseHas('events', [
        'id' => $event->id,
        'title' => 'Original Worship',
        'dorm_id' => $this->dorms['Elijah Hall']->id,
        'date' => '2026-10-20 00:00:00',
        'session' => 'morning',
    ]);

    $this->assertDatabaseCount('events', 1);
});

it('deletes an existing worship event', function () {
    $event = Event::create([
        'title' => 'Test Worship',
        'dorm_id' => $this->dorms['Elijah Hall']->id,
        'date' => '2026-10-20',
        'session' => 'morning',
    ]);

    $this->delete(route('events.destroy', $event))
        ->assertRedirect(route('events.index'))
        ->assertSessionHas('status', 'Worship event deleted.');

    $this->assertDatabaseMissing('events', [
        'id' => $event->id,
    ]);

    $this->assertDatabaseCount('events', 0);
});

it('returns 404 when an event does not exist', function () {
    $missingEventId = 999999;

    $this->get(route('events.show', $missingEventId))
        ->assertNotFound();

    $this->get(route('events.edit', $missingEventId))
        ->assertNotFound();

    $this->put(route('events.update', $missingEventId), [
        'title' => 'Updated Worship',
        'dorm_id' => $this->dorms['Elijah Hall']->id,
        'date' => '2026-10-20',
        'session' => 'morning',
    ])->assertNotFound();

    $this->delete(route('events.destroy', $missingEventId))
        ->assertNotFound();

    $this->assertDatabaseCount('events', 0);
});
