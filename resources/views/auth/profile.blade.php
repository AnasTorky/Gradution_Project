<x-layout>
    <div class="col-md-8">
        <h2>User Profile: {{ $user->name }}</h2>
        <div class="card mb-4">
            <div class="card-header">
                User Information
            </div>
            <div class="card-body">
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <a class="nav-link" href="{{ route('child.create', ['user' => auth()->id()]) }}">Add Child</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Registered Children
            </div>
            <div class="card-body">
                @if($user->children->count() > 0)
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Age</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->children as $child)
                            <tr>
                                <td>{{ $child->name }}</td>
                                <td>{{ $child->age }}</td>
                                <td>
                                    <a href="{{ route('child.edit', $child->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                    <form action="{{ route('child.destroy', $child->id) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this child?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No registered children</p>
                @endif
            </div>
        </div>
    </div>
</x-layout>
