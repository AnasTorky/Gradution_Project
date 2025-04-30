<x-layout>
    <div class="container">
        <h1>Edit Category</h1>

        <form action="{{ route('categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ $category->name }}" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" required>{{ $category->description }}</textarea>
            </div>

            <div class="form-group">
                <label for="content">Content</label>
                <textarea id="content" name="content" class="form-control" required>{{ $category->content }}</textarea>
            </div>

            <div class="form-group">
                <label for="photo">Photo</label>
                <input type="file" id="photo" name="photo" class="form-control">
                @if ($category->photo)
                    <img src="{{ asset('storage/' . $category->photo) }}" alt="category Photo" width="100">
                @endif
            </div>

            <button type="submit" class="btn btn-primary">Update Category</button>
        </form>
    </div>
</x-layout>
