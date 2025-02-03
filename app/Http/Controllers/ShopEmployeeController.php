<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ShopEmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('has-shop');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shop = auth()->user()->shop;
        $employees = $shop->employees()->latest()->get();
        return view('shop.employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $shop = auth()->user()->shop;
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required|string|max:20',
            'position' => 'required|string|max:255',
            'employment_type' => 'required|in:full-time,part-time',
            'profile_photo' => 'nullable|image|max:1024',
            'bio' => 'nullable|string'
        ]);

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

    /**
     * Display the specified resource.
     */
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        // Check if the employee belongs to the authenticated user's shop
        if ($employee->shop_id !== auth()->user()->shop->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('employees')->ignore($employee->id)],
            'phone' => 'required|string|max:20',
            'position' => 'required|string|max:255',
            'employment_type' => 'required|in:full-time,part-time',
            'profile_photo' => 'nullable|image|max:1024',
            'bio' => 'nullable|string'
        ]);

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

    /**
     * Remove the specified resource from storage.
     */
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

    public function restore(Employee $employee)
    {
        // Check if the employee belongs to the authenticated user's shop
        if ($employee->shop_id !== auth()->user()->shop->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $employee->restore();

        return response()->json([
            'success' => true,
            'message' => 'Employee restored successfully'
        ]);
    }
}
