<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Http\Requests\CompleteCleaningDutyRequest;
use App\Http\Requests\StoreCleaningDutyRequest;
use App\Models\CleaningDuty;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class CleaningDutyController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', CleaningDuty::class);

        $user = auth()->user();

        $duties = CleaningDuty::with('student')
            ->when(
                $user->role === Role::Student,
                fn ($query) => $query->where('user_id', $user->id)
            )
            ->orderBy('due_date')
            ->get();

        return view('cleaning.index', compact('duties'));
    }

    public function create()
    {
        Gate::authorize('create', CleaningDuty::class);

        $students = User::where('role', Role::Student)
            ->orderBy('name')
            ->get();

        return view('cleaning.create', compact('students'));
    }

    public function store(StoreCleaningDutyRequest $request)
    {
        CleaningDuty::create($request->validated());

        return redirect()
            ->route('cleaning-duties.index')
            ->with('status', 'Cleaning duty assigned successfully.');
    }

    public function complete(
        CompleteCleaningDutyRequest $request,
        CleaningDuty $cleaning_duty
    ) {
        $cleaning_duty->update([
            'completed_at' => now(),
        ]);

        return redirect()
            ->route('cleaning-duties.index')
            ->with('status', 'Cleaning duty marked as completed.');
    }
}
