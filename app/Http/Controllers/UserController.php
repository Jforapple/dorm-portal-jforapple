<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', User::class);

        $users = User::with(['room.dorm'])
            ->orderBy('name')
            ->paginate(20);

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        Gate::authorize('create', User::class);

        $rooms = Room::with('dorm')
            ->orderBy('dorm_id')
            ->orderBy('number')
            ->get();

        return view('users.create', compact('rooms'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', User::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::enum(Role::class)],
            'room_id' => [
                'nullable',
                'integer',
                'exists:rooms,id',
                'prohibited_unless:role,student',
            ],
        ]);

        $validated['room_id'] = $validated['role'] === Role::Student->value
            ? ($validated['room_id'] ?? null)
            : null;

        $this->ensureRoomHasCapacity($validated['room_id'] ?? null);

        User::create($validated);

        return redirect()
            ->route('users.index')
            ->with('status', 'User created successfully.');
    }

    public function show(User $user): View
    {
        Gate::authorize('view', $user);

        $user->load('room.dorm');

        return view('users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        Gate::authorize('update', $user);

        $rooms = Room::with('dorm')
            ->orderBy('dorm_id')
            ->orderBy('number')
            ->get();

        return view('users.edit', compact('user', 'rooms'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('update', $user);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::enum(Role::class)],
            'room_id' => [
                'nullable',
                'integer',
                'exists:rooms,id',
                'prohibited_unless:role,student',
            ],
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $validated['room_id'] = $validated['role'] === Role::Student->value
            ? ($validated['room_id'] ?? null)
            : null;

        $this->ensureRoomHasCapacity(
            $validated['room_id'],
            $user
        );

        $user->update($validated);

        return redirect()
            ->route('users.index')
            ->with('status', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('delete', $user);

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('status', 'User deleted successfully.');
    }

    private function ensureRoomHasCapacity(
        ?int $roomId,
        ?User $user = null
    ): void {
        if ($roomId === null) {
            return;
        }

        $room = Room::findOrFail($roomId);

        $assignedStudents = $room->users()
            ->when(
                $user,
                fn ($query) => $query->where('id', '!=', $user->id)
            )
            ->count();

        if ($assignedStudents >= $room->capacity) {
            throw ValidationException::withMessages([
                'room_id' => 'This room has reached its maximum capacity.',
            ]);
        }
    }
}
