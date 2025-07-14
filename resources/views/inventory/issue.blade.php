@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Issue Inventory</h1>
    
    <form action="{{ route('inventory.issue') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="equipment_id">Equipment</label>
            <select name="equipment_id" id="equipment_id" class="form-control" required>
                <option value="">Select Equipment</option>
                {{-- Add your equipment options here --}}
            </select>
        </div>
        
        <div class="form-group">
            <label for="staff_name">Staff Name</label>
            <input type="text" name="staff_name" id="staff_name" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="department">Department</label>
            <input type="text" name="department" id="department" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="issue_date">Issue Date</label>
            <input type="date" name="issue_date" id="issue_date" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Issue Equipment</button>
        <a href="{{ route('inventory') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection