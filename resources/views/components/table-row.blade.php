<tr>
    <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $member->name }}</td>
    <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $member->department }}</td>
    <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $member->email }}</td>
    <td class="px-5 py-3 whitespace-nowrap">
        <span class="status-indicator {{ $member->status === 'Active' ? 'status-active' : 'status-resigned' }}"></span>
        <span class="text-xs {{ $member->status === 'Active' ? 'text-[#00553d]' : 'text-[#90143c]' }}">{{ $member->status }}</span>
    </td>
    <td class="px-5 py-3 whitespace-nowrap text-xs">
        <button class="text-[#00553d] hover:text-[#003d2b] mr-2" data-id="{{ $member->id }}" data-name="{{ $member->name }}" onclick="viewHistoryLogs(this)" aria-label="View logs for {{ $member->name }}">View</button>
        <button class="text-[#90143c] hover:text-[#6b102d] mr-2" data-id="{{ $member->id }}" data-name="{{ $member->name }}" data-department="{{ $member->department }}" data-email="{{ $member->email }}" data-status="{{ $member->status }}" onclick="editStaff(this)" aria-label="Edit staff {{ $member->name }}">Edit</button>
        <form action="{{ route('staff.destroy', $member->id) }}" method="POST" class="inline delete-staff-form" data-name="{{ $member->name }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-[#90143c] hover:text-[#6b102d]" aria-label="Delete staff {{ $member->name }}">Delete</button>
        </form>
    </td>
</tr>