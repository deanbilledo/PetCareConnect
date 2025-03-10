<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ShopEmployeeSetupController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'has-shop']);
    }

    public function index()
    {
        $shop = auth()->user()->shop;
        $employees = $shop->employees()->latest()->get();
        
        return view('shop.setup.employees', compact('employees'));
    }

    public function show(Employee $employee)
    {
        // Check if the employee belongs to the authenticated user's shop
        if ($employee->shop_id !== auth()->user()->shop->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'employee' => $employee
        ]);
    }

    public function store(Request $request)
    {
        $shop = auth()->user()->shop;
        
        // Debug information to identify the issue
        \Log::info('Employee store attempt', [
            'shop_id' => $shop->id,
            'email' => $request->email
        ]);
        
        // Check if this email already exists in this shop
        $existingEmployee = Employee::where('email', $request->email)
            ->where('shop_id', $shop->id)
            ->withTrashed() // Include soft-deleted records in the check
            ->first();
            
        if ($existingEmployee) {
            \Log::info('Existing employee found', [
                'id' => $existingEmployee->id,
                'deleted_at' => $existingEmployee->deleted_at
            ]);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                function ($attribute, $value, $fail) use ($shop) {
                    // Custom validation to check uniqueness within shop and handle soft-deleted records
                    $existingEmployee = Employee::where('email', $value)
                        ->where('shop_id', $shop->id)
                        ->withTrashed() // Include soft-deleted records
                        ->first();
                        
                    if ($existingEmployee) {
                        if ($existingEmployee->deleted_at) {
                            // If it's soft-deleted, restore it to allow reusing the record
                            $existingEmployee->restore();
                            return; // Allow validation to pass
                        }
                        
                        $fail("The email has already been taken in your shop.");
                    }
                }
            ],
            'phone' => 'required|string|max:20',
            'position' => 'required|string|max:255',
            'employment_type' => 'required|in:full-time,part-time',
            'profile_photo' => 'nullable|image|max:1024',
            'bio' => 'nullable|string'
        ]);

        // For debugging
        \Log::info('Validation passed', $validated);

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('employee-photos', 'public');
        }

        $employee = $shop->employees()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Employee added successfully',
            'employee' => $employee
        ]);
    }

    public function update(Request $request, Employee $employee)
    {
        // Check if the employee belongs to the authenticated user's shop
        if ($employee->shop_id !== auth()->user()->shop->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }
        
        $shop = auth()->user()->shop;
        
        // Debug information
        \Log::info('Employee update attempt', [
            'shop_id' => $shop->id,
            'employee_id' => $employee->id,
            'email' => $request->email
        ]);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                function ($attribute, $value, $fail) use ($shop, $employee) {
                    // Custom validation to check uniqueness within shop, ignoring current employee
                    $existingEmployee = Employee::where('email', $value)
                        ->where('shop_id', $shop->id)
                        ->where('id', '!=', $employee->id) // Ignore current employee
                        ->withTrashed() // Include soft-deleted records
                        ->first();
                        
                    if ($existingEmployee) {
                        if ($existingEmployee->deleted_at) {
                            // If it's soft-deleted, allow keeping the email by forcing the soft-deleted record to use another email
                            $existingEmployee->update(['email' => $existingEmployee->email . '.deleted.' . time()]);
                            return; // Allow validation to pass
                        }
                        
                        $fail("The email has already been taken by another employee in your shop.");
                    }
                }
            ],
            'phone' => 'required|string|max:20',
            'position' => 'required|string|max:255',
            'employment_type' => 'required|in:full-time,part-time',
            'profile_photo' => 'nullable|image|max:1024',
            'bio' => 'nullable|string'
        ]);

        // For debugging
        \Log::info('Update validation passed', $validated);

        if ($request->hasFile('profile_photo')) {
            if ($employee->profile_photo) {
                Storage::disk('public')->delete($employee->profile_photo);
            }
            $validated['profile_photo'] = $request->file('profile_photo')->store('employee-photos', 'public');
        }

        $employee->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Employee updated successfully',
            'employee' => $employee
        ]);
    }

    public function destroy(Employee $employee)
    {
        // Check if the employee belongs to the authenticated user's shop
        if ($employee->shop_id !== auth()->user()->shop->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        if ($employee->profile_photo) {
            Storage::disk('public')->delete($employee->profile_photo);
        }

        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Employee removed successfully'
        ]);
    }
} 