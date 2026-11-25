<?php

use App\Enums\AttendanceStatus;
use App\Enums\Role;
use App\Models\Attendance;
use App\Models\Dorm;
use App\Models\Event;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->staff = User::factory()->create([
        'role' => Role::Staff,
    ]);

    $this->actingAs($this->staff);

    $this->dorm = Dorm::factory()->create([
        'name' => 'Elijah Hall',
    ]);

    $this->otherDorm = Dorm::factory()->create([
        'name' => 'Solomon Hall',
    ]);

    $this->room = Room::factory()->create([
        'dorm_id' => $this->dorm->id,
    ]);

    $this->otherRoom = Room::factory()->create([
        'dorm_id' => $this->otherDorm->id,
    ]);

    $this->student = User::factory()->create([
        'role' => Role::Student,
        'room_id' => $this->room->id,
    ]);

    $this->secondStudent = User::factory()->create([
        'role' => Role::Student,
        'room_id' => $this->room->id,
    ]);

    $this->otherStudent = User::factory()->create([
        'role' => Role::Student,
        'room_id' => $this->otherRoom->id,
    ]);

    $this->event = Event::factory()->create([
        'dorm_id' => $this->dorm->id,
    ]);
});

test('staff can open the attendance form for an event', function () {
    $this->get(route('events.attendance', $this->event))
        ->assertOk()
        ->assertSee('Mark Worship Attendance')
        ->assertSee($this->student->name)
        ->assertSee($this->secondStudent->name)
        ->assertDontSee($this->otherStudent->name);
});

test('staff can mark attendance for students in the event dorm', function () {
    $this->post(route('events.attendance.store', $this->event), [
        'attendances' => [
            [
                'user_id' => $this->student->id,
                'status' => AttendanceStatus::Present->value,
            ],
            [
                'user_id' => $this->secondStudent->id,
                'status' => AttendanceStatus::Late->value,
            ],
        ],
    ])->assertRedirect(route('events.show', $this->event))
        ->assertSessionHas('status', 'Worship attendance saved.');

    $this->assertDatabaseHas('attendances', [
        'event_id' => $this->event->id,
        'user_id' => $this->student->id,
        'status' => AttendanceStatus::Present->value,
    ]);

    $this->assertDatabaseHas('attendances', [
        'event_id' => $this->event->id,
        'user_id' => $this->secondStudent->id,
        'status' => AttendanceStatus::Late->value,
    ]);
});

test('marking attendance again updates existing records without creating duplicates', function () {
    $attendanceData = [
        'attendances' => [
            [
                'user_id' => $this->student->id,
                'status' => AttendanceStatus::Present->value,
            ],
            [
                'user_id' => $this->secondStudent->id,
                'status' => AttendanceStatus::Late->value,
            ],
        ],
    ];

    $this->post(route('events.attendance.store', $this->event), $attendanceData)
        ->assertRedirect(route('events.show', $this->event));

    $attendanceData['attendances'][0]['status'] = AttendanceStatus::Late->value;

    $this->post(route('events.attendance.store', $this->event), $attendanceData)
        ->assertRedirect(route('events.show', $this->event));

    $this->assertDatabaseCount('attendances', 2);

    $this->assertDatabaseHas('attendances', [
        'event_id' => $this->event->id,
        'user_id' => $this->student->id,
        'status' => AttendanceStatus::Late->value,
    ]);

    $this->assertDatabaseHas('attendances', [
        'event_id' => $this->event->id,
        'user_id' => $this->secondStudent->id,
        'status' => AttendanceStatus::Late->value,
    ]);
});

test('staff cannot mark attendance for a student from another dorm', function () {
    $this->from(route('events.attendance', $this->event))
        ->post(route('events.attendance.store', $this->event), [
            'attendances' => [
                [
                    'user_id' => $this->otherStudent->id,
                    'status' => AttendanceStatus::Present->value,
                ],
            ],
        ])
        ->assertRedirect(route('events.attendance', $this->event))
        ->assertSessionHasErrors('attendances');

    $this->assertDatabaseCount('attendances', 0);
});

test('staff must submit attendance for every student in the event dorm', function () {
    $this->from(route('events.attendance', $this->event))
        ->post(route('events.attendance.store', $this->event), [
            'attendances' => [
                [
                    'user_id' => $this->student->id,
                    'status' => AttendanceStatus::Present->value,
                ],
            ],
        ])
        ->assertRedirect(route('events.attendance', $this->event))
        ->assertSessionHasErrors('attendances');

    $this->assertDatabaseCount('attendances', 0);
});

test('students cannot open or submit the staff attendance form', function () {
    $student = $this->student;

    $this->actingAs($student)
        ->get(route('events.attendance', $this->event))
        ->assertForbidden();

    $this->actingAs($student)
        ->post(route('events.attendance.store', $this->event), [
            'attendances' => [
                [
                    'user_id' => $student->id,
                    'status' => AttendanceStatus::Present->value,
                ],
            ],
        ])
        ->assertForbidden();
});

test('students can view their own attendance history and percentage', function () {
    $events = Event::factory()->count(4)->create([
        'dorm_id' => $this->dorm->id,
    ]);

    Attendance::factory()->create([
        'event_id' => $events[0]->id,
        'user_id' => $this->student->id,
        'status' => AttendanceStatus::Present,
    ]);

    Attendance::factory()->create([
        'event_id' => $events[1]->id,
        'user_id' => $this->student->id,
        'status' => AttendanceStatus::Late,
    ]);

    Attendance::factory()->create([
        'event_id' => $events[2]->id,
        'user_id' => $this->student->id,
        'status' => AttendanceStatus::Absent,
    ]);

    Attendance::factory()->create([
        'event_id' => $events[3]->id,
        'user_id' => $this->student->id,
        'status' => AttendanceStatus::Excused,
    ]);

    $this->actingAs($this->student)
        ->get(route('attendance.index'))
        ->assertOk()
        ->assertSee('66.67%')
        ->assertSee($events[0]->title)
        ->assertSee($events[1]->title)
        ->assertSee($events[2]->title)
        ->assertSee($events[3]->title);
});

test('students can only see their own attendance history', function () {
    $event = $this->event;

    Attendance::factory()->create([
        'event_id' => $event->id,
        'user_id' => $this->student->id,
        'status' => AttendanceStatus::Present,
    ]);

    Attendance::factory()->create([
        'event_id' => $event->id,
        'user_id' => $this->secondStudent->id,
        'status' => AttendanceStatus::Absent,
    ]);

    $this->actingAs($this->student)
        ->get(route('attendance.index'))
        ->assertOk()
        ->assertSee($event->title)
        ->assertSee('Present')
        ->assertDontSee('Absent');
});

test('the database prevents duplicate attendance records for the same event and student', function () {
    Attendance::factory()->create([
        'event_id' => $this->event->id,
        'user_id' => $this->student->id,
        'status' => AttendanceStatus::Present,
    ]);

    expect(fn () => Attendance::factory()->create([
        'event_id' => $this->event->id,
        'user_id' => $this->student->id,
        'status' => AttendanceStatus::Late,
    ]))->toThrow(QueryException::class);
});

test('excused attendance is excluded from the attendance percentage', function () {
    $events = Event::factory()->count(2)->create([
        'dorm_id' => $this->dorm->id,
    ]);

    Attendance::factory()->create([
        'event_id' => $events[0]->id,
        'user_id' => $this->student->id,
        'status' => AttendanceStatus::Excused,
    ]);

    Attendance::factory()->create([
        'event_id' => $events[1]->id,
        'user_id' => $this->student->id,
        'status' => AttendanceStatus::Present,
    ]);

    $this->actingAs($this->student)
        ->get(route('attendance.index'))
        ->assertOk()
        ->assertSee('100.00%');
});
