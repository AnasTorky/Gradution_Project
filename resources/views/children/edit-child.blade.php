<x-layout>
    <form method="POST" action="{{ route('child.update', $child->id) }}">
        @csrf
        @method('PATCH')
        <div>
            <label>Name</label>
            <input type="text" name="name" required value="{{ $child->name }}">
        </div>
        <div>
            <label>Age</label>
            <input type="age" name="age" required value="{{ $child->age }}">
        </div>
        <button type="submit">Submit</button>
    </form>
</x-layout>

