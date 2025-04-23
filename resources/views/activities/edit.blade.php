<x-layout>
    <div class="container">
        <h1>Edit Activity</h1>

        <form action="{{ route('activities.update', $activity->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ $activity->name }}" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" required>{{ $activity->description }}</textarea>
            </div>

            <div class="form-group">
                <label for="content">Content</label>
                <textarea id="content" name="content" class="form-control" required>{{ $activity->content }}</textarea>
            </div>

            <div class="form-group mb-3">
                <label for="category_id">Category</label>
                <select name="category_id" id="category_id" class="form-select" required>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ $category->id == $activity->category_id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="photo">Photo</label>
                <input type="file" id="photo" name="photo" class="form-control">
                @if ($activity->photo)
                    <img src="{{ asset('storage/' . $activity->photo) }}" alt="Activity Photo" width="100">
                @endif
            </div>

            <button type="submit" class="btn btn-primary">Update Activity</button>
        </form>
    </div>
</x-layout>
