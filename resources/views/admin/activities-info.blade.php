<x-layout>
    <div class="container">
        <h1>Activities Management</h1>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>name</th>
                        <th>description</th>
                        <th>content</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activities as $activity)
                    <tr>
                        <td>{{ $activity->name }}</td>
                        <td>{{ $activity->description }}</td>
                        <td>{{ $activity->content }}</td>
                        <td>
                            <a href="{{ route('activities.create') }}" class="btn btn-sm btn-primary px-2">+ Add</a>
                            <a href="{{ route('activities.edit', $activity->id) }}" class="btn btn-sm btn-success">Edit</a>
                            <form action="{{ route('activities.destroy', $activity->id) }}" method="POST" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this activity?')">Delete</button>
                            </form>
                        </td>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layout>
