<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function storeDev(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|max:255|email|unique:users,email',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
        ]);

        $year = date('Y');
        $firstName = str_replace(' ', '', ucwords(strtolower($validated['first_name'])));
        $generatedPassword = $year . $firstName . 'Developer';

        $user = User::create([
            'email' => $validated['email'],
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'],
            'last_name' => $validated['last_name'],
            'password' => Hash::make($generatedPassword),
            'role' => 'developer',
            'status' => 'active'
        ]);

        return response()->json([
            'message' => 'Developer created successfully',
            'generated_password' => $generatedPassword,
            'user' => $user
        ]);
    }
    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'email' => 'required|string|max:255|email|unique:users,email',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'password' => 'required|string|min:6|same:retype_password',
        ]);

        // Create user
        $user = User::create([
            'email' => $validated['email'],
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'],
            'last_name' => $validated['last_name'],
            'password' => Hash::make($validated['password']),
            'role' => 'client',
            'status' => 'inactive'
        ]);

        // Automatically log in the user
        Auth::login($user);
        $request->session()->regenerate();
        
        // Return success + user data
        return response()->json([
            'message' => 'Registration successful! You are now logged in.',
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'middle_name' => $user->middle_name,
                'last_name' => $user->last_name,
                'status' => $user->status
            ]
        ], 201);
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

    public function login(Request $request) 
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Check if account is active
            // if ($user->status !== 'active') {
            //     Auth::logout(); // Log them out immediately
            //     return response()->json([
            //         'error' => 'Your account is pending approval. Please wait for admin verification.'
            //     ], 403); // 403 Forbidden
            // }
            
            $request->session()->regenerate();

            $hasApplication = false;
            $applicationStatus = null;

            if ($user->role === 'client') {
                // Get the latest application
                $latestApplication = $user->registrations()->latest()->first();
                
                if ($latestApplication) {
                    $hasApplication = true;
                    $applicationStatus = $latestApplication->status;
                }
            }
            
            return response()->json([
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'first_name' => $user->first_name,
                    'middle_name' => $user->middle_name,
                    'last_name' => $user->last_name,
                    'role' => $user->role,
                    'status' => $user->status,
                ],
                'has_application' => $hasApplication,
                'application_status' => $applicationStatus, // ← Add this
            ]);
        }

        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email|exists:users,email',
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6|same:retype_password',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!Hash::check($validated['old_password'], $user->password)){
            return response()->json(['error' => 'Old password is incorrect'], 401);
        }

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return response()->json(['message' => 'Password reset successfully'], 200);
    }

    public function userCount()
    {
        return response()->json([
            'client' => User::where('role', 'client')->count(),
            'active_client' => User::where('role', 'client')->where('status', 'active')->count(),
            'pending_client' => User::where('role', 'client')->where('status', 'inactive')->count(),
        ]);
    }
    
    // NEW: Get all users for admin to approve
    public function index(Request $request)
    {
        $query = User::query();
        
        // Filter by status if provided
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        if($request->has('role') && $request->role != 'all') {
            $query->where('role', $request->role);
        }
        
        if ($request->has('search') && $request->search) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('id', 'like', "%{$search}%");
        });
    }
        
        return $query->latest()->paginate(10);
    }
    
    
    // NEW: Approve/Reject user
    public function updateStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:active,inactive,deactivated',
        ]);
        
        $user->status = $validated['status'];
        $user->save();
        
        return response()->json([
            'message' => 'User status updated successfully',
            'user' => $user
        ]);
    }

    public function updateProfile(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
        ]);
        
        $user->update($validated);
        
        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User Deleted Successfully']);
    }

    public function getCurrentUser(Request $request)
{
    $user = Auth::user();
    
    if (!$user) {
        return response()->json(['error' => 'Not authenticated'], 401);
    }
    
    return response()->json([
        'id' => $user->id,
        'email' => $user->email,
        'first_name' => $user->first_name,
        'middle_name' => $user->middle_name,
        'last_name' => $user->last_name,
        'role' => $user->role,
        'status' => $user->status,
    ]);
}
}