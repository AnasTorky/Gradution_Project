<x-layout>
    <div class="container">
        <h2>Add Activity</h2>
        <form method="POST" action="{{ route('activities.store') }}" enctype="multipart/form-data">
            @csrf
    
            <div>
                <label>Name</label>
                <input type="text" name="name" required>
            </div>
    
            <div>
                <label>Description</label>
                <textarea name="description" required></textarea>
            </div>
    
            <div>
                <label>Content</label>
                <input type="text" name="content" required>
            </div>
    
            <div>
                <label>Category</label>
                <input type="text" name="category" required>
            </div>
    
            <div>
                <label>Photo</label>
                <input type="file" name="photo">
            </div>
    
            <button type="submit">Create</button>
        </form>
    </div>
</x-layout>
