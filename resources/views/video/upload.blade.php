<x-layout>
    <h1>Upload Video</h1>

    <form action="{{ route('video.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="video">Choose Video</label>
            <input type="file" name="video" id="video" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Upload Video</button>
    </form>
</x-layout>