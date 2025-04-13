<x-layout>
    <div class="container">
        <h1>Admin Dashboard</h1>
        <p>Welcome, {{ auth()->user()->name }}</p>

        <div class="admin-links mt-4">
            <a href="{{ route('users') }}" class="btn btn-primary mr-3">Manage Users</a>
        </div>
    </div>
</x-layout>
