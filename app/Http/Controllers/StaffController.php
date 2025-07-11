<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        $staff = Staff::when(request('search'), fn($query) => $query->where('name', 'like', '%'.request('search').'%')
            ->orWhere('department', 'like', '%'.request('search').'%')
            ->orWhere('email', 'like', '%'.request('search').'%'))
            ->get();
        return view('staff', compact('staff'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email',
        ]);

        Staff::create($validated);
        return redirect()->route('staff')->with('success', 'Staff added successfully.');
    }

    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email,'.$staff->id,
        ]);

        $staff->update($validated);
        return redirect()->route('staff')->with('success', 'Staff updated successfully.');
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();
        return redirect()->route('staff')->with('success', 'Staff deleted successfully.');
    }
}