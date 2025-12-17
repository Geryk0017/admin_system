<?php

namespace App\Http\Controllers;

use App\Models\AddTask;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AddTaskController extends Controller
{
    public function index(Request $request)
    {
        $query = AddTask::with('developer', 'client')->latest();

        // Role-based filtering - CRITICAL FOR DEVELOPERS AND CLIENTS
        if ($request->has('role') && $request->has('user_id')) {
            $role = $request->input('role');
            $userId = $request->input('user_id');

            if ($role === 'developer') {
                $query->where('developer_id', $userId);
            } elseif ($role === 'client') {
                $query->where('client_id', $userId);
                // ->where('status', 'approved');
            }
            // Admin gets all tasks (no filter)
        }

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = strtolower($request->input('search'));
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(title) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('LOWER(description) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('LOWER(task_id) LIKE ?', ["%{$search}%"])
                ->orWhereHas('developer', function($q2) use ($search) {
                    $q2->whereRaw("LOWER(CONCAT(first_name, ' ', last_name)) LIKE ?", ["%{$search}%"]);
                })
                ->orWhereHas('client', function($q3) use ($search) {
                    $q3->whereRaw("LOWER(CONCAT(first_name, ' ', last_name)) LIKE ?", ["%{$search}%"]);
                });
            });
        }

        // Priority filter
        if ($request->has('priority') && $request->priority !== 'all') {
            $query->where('priority_level', strtolower($request->priority));
        }

        // Due date filter
        if ($request->has('due_date') && $request->due_date !== 'all') {
            $query->whereDate('due_date', $request->due_date);
        }

        return $query->paginate(10);
    }

    public function allTasksForChart(Request $request)
    {
        $role = $request->input('role');
        $userId = $request->input('user_id');

        $query = AddTask::with('developer', 'client')->latest();

        // Filter tasks based on role
        if ($role === 'developer') {
            $query->where('developer_id', $userId);
        }
        // Admin sees all tasks, no filter needed

        $tasks = $query->get(); // get ALL tasks, no pagination
        return response()->json($tasks);
    }



    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'developer_id' => 'required|exists:users,id',
            'client_id' => 'required|exists:users,id',
            'priority_level' => 'required|in:low,medium,high',
            'start_date' => 'required|date|date_format:Y-m-d',
            'due_date' => 'required|date|after_or_equal:start_date|date_format:Y-m-d',
            'notes' => 'nullable|string|max:500',
            'progress' => 'required|integer|min:0|max:100'
        ]);

        $task = AddTask::create($validated);
        
        // Load the relationship before returning
        $task->load('developer');

        return response()->json([
            'message' => 'Task Submitted Successfully',
            'task' => $task
        ], 201);
    }

    public function show($id)
    {
        $task = AddTask::with('developer')->findOrFail($id);
        return response()->json($task);
    }

    public function showByTaskId($task_id)
    {
        $task = AddTask::with('developer')->where('task_id', $task_id)->firstOrFail();
        return response()->json($task);
    }

    public function update(Request $request, $id)
    {
        $task = AddTask::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'developer_id' => 'required|exists:users,id',
            'client_id' => 'required|exists:users,id',
            'priority_level' => 'required|in:low,medium,high',
            'start_date' => 'required|date|date_format:Y-m-d',
            'due_date' => 'required|date|after_or_equal:start_date|date_format:Y-m-d',
            'notes' => 'nullable|string|max:500',
            'progress' => 'required|integer|min:0|max:100'
        ]);

        $task->update($validated);
        $task->load('developer');
        
        return response()->json([
            'message' => 'Task Updated Successfully',
            'data' => $task
        ]);
    }

    public function destroy($id)
    {
        $task = AddTask::findOrFail($id);
        $task->delete();
        
        return response()->json(['message' => 'Task Deleted Successfully']);
    }
    
    // NEW: Get all developers for the dropdown
    public function getDevelopers()
    {
        $developers = User::where('role', 'developer')
            ->select('id', 'first_name', 'middle_name', 'last_name', 'email')
            ->orderBy('last_name')
            ->get();
        
        return response()->json($developers);
    }
    public function getClientsWithRegistration()
    {
        $client = User::where('role', 'client')
        ->where('status', 'active')
        ->whereHas('registrations', function($query) {
            $query->where('status', 'approved');
        })
        ->select('id', 'first_name', 'middle_name', 'last_name', 'email')
        ->orderBy('last_name')
        ->get();

        return response()->json($client);
    }

    public function getClients()
    {
        $client = User::where('role', 'client')
        ->select('id', 'first_name', 'middle_name', 'last_name', 'email')
        ->orderBy('last_name')
        ->get();

        return response()->json($client);
    }
}