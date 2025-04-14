<x-layout>
    <form method="POST" action="{{ route('child.update', $child->id) }}">
        @csrf
        @method('PATCH')
        <div>
            <label>Name</label>
            <input type="text" name="name" required value="{{ $child->name }}">
        </div>
        @error('name')
        <p class="alert alert-danger mt-2">{{ $message }}</p>
    @enderror
        <div>
            <label>Age</label>
            <input type="age" name="age" required value="{{ $child->age }}">
        </div>
        @error('age')
        <p class="alert alert-danger mt-2">{{ $message }}</p>
    @enderror
        <button type="submit">Submit</button>
    </form>
</x-layout>

