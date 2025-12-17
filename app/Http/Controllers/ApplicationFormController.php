<?php

namespace App\Http\Controllers;
use App\Models\ApplicationForm;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;
use Illuminate\Validation\Rule;
use App\Models\User;
use Illuminate\Http\Request;

class ApplicationFormController extends Controller
{
    public function index(Request $request)
    {
        $query = ApplicationForm::query();

        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        // SEARCH FUNCTIONALITY
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

    public function myApplications(Request $request, $userId)
    {

        $query = ApplicationForm::where('user_id', $userId);
    
        // Filter by status if provided
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        $applications = $query->latest()->get();
        return response()->json($applications);
    }

    public function stats()
    {
        return response()->json([
            'pending' => ApplicationForm::where('status', 'pending')->count(),
            'verified' => ApplicationForm::where('status', 'verified')->count(),
            'under_review' => ApplicationForm::where('status', 'under review')->count(),
            'approved' => ApplicationForm::where('status', 'approved')->count(),
            'rejected' => ApplicationForm::where('status', 'rejected')->count(),
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'birthdate' => 'required|date|before:today|before_or_equal:' . now()->subYears(21)->format('Y-m-d'),
            'email' => [
                'required',
                'email',
                Rule::unique('application_forms')->where(function ($query) {
                    $query->where('status', '!=', 'rejected'); 
                }),
            ],
            'phone' => ['required', 'regex:/^(09|\+639)\d{9}$/', 'unique:application_forms,phone'],
            'income_source' => 'required|string|in:employment,self employed,freelance,business,allowance,unemployed,others',
            'occupation' => 'required|string|max:255',
            'salary_range_from' => 'required|integer|lt:salary_range_to',
            'salary_range_to' => 'required|integer',
            'address' => 'required|string|max:500'
        ]);

        $loanform = ApplicationForm::create([
            'user_id' => $validated['user_id'],
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'],
            'last_name' => $validated['last_name'],
            'gender' => $validated['gender'],
            'birthdate' => $validated['birthdate'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'status' => 'pending',
            'income_source' => $validated['income_source'],
            'occupation' => $validated['occupation'],
            'salary_range_from' => $validated['salary_range_from'],
            'salary_range_to' => $validated['salary_range_to'],
            'address' => $validated['address']
        ]);


        return response()->json(['message' => 'Application Submitted Successfully'], 201);
    }

    public function show($id)
    {
        $application = ApplicationForm::findOrFail($id);
        return response()->json($application);
    }
    public function update(Request $request, $id)
    {
        $application = ApplicationForm::findOrFail($id);
        $user = User::find($application->user_id);
        $role = $request->user()->role ?? $request->role;

        if ($role === 'verifier') {
            $validated = $request->validate([
                'status' => 'required|in:pending,under review,verified,rejected',
                'remarks' => 'nullable|string|max:500',
            ]);
        } elseif ($role === 'admin') {
            if ($application->status !== 'verified') {
            return response()->json([
                'message' => 'Admin cannot edit this application unless it is verified.'
            ], 403);
            }

            // Admin update
            $validated = $request->validate([
                'status' => 'required|in:pending,approved,rejected',
                'remarks' => 'nullable|string|max:500',
            ]);
            
        } else {
            //Client update (full form)
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'last_name' => 'required|string|max:255',
                'gender' => 'required|in:male,female',
                'birthdate' => 'required|date|before:today|before_or_equal:' . now()->subYears(21)->format('Y-m-d'),
                'email' => [
                    'required',
                    'email',
                    Rule::unique('application_forms', 'email')->ignore($application->id),
                    Rule::unique('users', 'email')->ignore($user->id),
                ],
                'phone' => [
                'required',
                'regex:/^(09|\+639)\d{9}$/',
                    Rule::unique('application_forms', 'phone')->ignore($application->id),
                ],
                'occupation' => 'required|string|max:255',
                'income_source' => 'required|string|in:employment,self employed,freelance,business,allowance,unemployed,others',
                'salary_range_from' => 'required|integer|lt:salary_range_to',
                'salary_range_to' => 'required|integer',
                'address' => 'required|string|max:500',
                'remarks' => 'nullable|string|max:500'
            ]);


        }

        $application->update($validated);

        return response()->json([
            'message' => 'Application Updated Successfully',
            'data' => $application
        ]);
    }

    public function destroy($id)
    {
        $application = ApplicationForm::findOrFail($id);

        $user = User::find($application->user_id);
        if ($user) {
            $user->status = 'inactive';
            $user->save();
        }

        $application->delete();

        return response()->json(['message' => 'Application Deleted Successfully']);
    }
}
