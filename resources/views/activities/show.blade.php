<x-layout>
    <div class="container">
    <h1>{{ $activity->name }}</h1>

    <p><strong>Category:</strong> {{ $activity->category }}</p>
    <p><strong>Description:</strong> {{ $activity->description }}</p>
    <p><strong>Content:</strong> {{ $activity->content }}</p>

    @if ($activity->photo)
        <img src="{{ asset('storage/' . $activity->photo) }}" alt="{{ $activity->name }}" class="image">
    @endif

    <a href="{{ route('activities.index') }}" class="btn btn-primary ">Back to Activities</a>

    <!-- Admin controls: Check if the user is an admin -->
    @if (auth()->check() && auth()->user()->role == 1)  <!-- Check if the role is 1 (admin) -->
        <a href="{{ route('activities.edit', $activity->id) }}" class="btn btn-warning">Edit</a>

        <form action="{{ route('activities.destroy', $activity->id) }}" method="POST" style="display: inline-block;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
    @endif
</div>
</x-layout>
