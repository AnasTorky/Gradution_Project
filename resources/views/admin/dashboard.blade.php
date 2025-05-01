<x-layout>
    <div class="container">
        <h1>Admin Dashboard</h1>
        <p>Welcome, {{ auth()->user()->name }}</p>

        <div class="admin-links mt-4">
            <a href="{{ route('users') }}" class="btn btn-primary mr-3">Manage Users</a><br><br>
            <a href="{{ route('admin.categories') }}" class="btn btn-primary mr-3">Manage Categories</a><br><br>
            <a href="{{ route('admin.activities') }}" class="btn btn-primary mr-3">Manage Activities</a>
        </div>
    </div>
</x-layout>
