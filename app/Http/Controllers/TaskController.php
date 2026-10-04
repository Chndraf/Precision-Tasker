<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $query = $user->tasks()->with('course')->where('status_task', 'Pending');

        // Filtering & Sorting
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('sort')) {
            if ($request->sort === 'deadline') {
                // PostgreSQL compatible: nulls last
                $query->orderByRaw('deadline ASC NULLS LAST');
            } elseif ($request->sort === 'priority') {
                // PostgreSQL compatible: use CASE instead of FIELD
                $query->orderByRaw("CASE priority WHEN 'High' THEN 1 WHEN 'Medium' THEN 2 WHEN 'Low' THEN 3 END");
            }
        } else {
            $query->latest();
        }

        $tasks = $query->get();
        $courses = $user->courses;

        return view('tasks.index', compact('tasks', 'courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_id' => 'nullable|exists:courses,id',
            'priority' => 'required|in:Low,Medium,High',
            'deadline' => 'nullable|date',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->tasks()->create($validated);

        return redirect()->back();
    }

    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_id' => 'nullable|exists:courses,id',
            'priority' => 'required|in:Low,Medium,High',
            'deadline' => 'nullable|date',
        ]);

        $task->update($validated);

        return redirect()->back();
    }

    public function complete(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $task->update([
            'status_task' => 'Completed',
            'completed_at' => now(),
        ]);

        return redirect()->back();
    }

    public function destroy(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $task->delete();
        return redirect()->back();
    }
}
