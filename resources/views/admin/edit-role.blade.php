<x-layout>
<div class="container">
    <h2>Edit Role for {{ $user->name }}</h2>
    <form action="{{ route('user.updateRole', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
    
        <select name="role" class="form-select">
            <option value="0" {{ $user->role == 0 ? 'selected' : '' }}>User</option>
            <option value="1" {{ $user->role == 1 ? 'selected' : '' }}>Admin</option>
        </select>
    
        <button type="submit" class="btn btn-primary mt-2">Update Role</button>
    </form>
    
    
</div>
</x-layout>
