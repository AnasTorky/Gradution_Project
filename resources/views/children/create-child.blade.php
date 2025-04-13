<x-layout>
    <form method="POST" action="{{ route('child.store') }}">
        @csrf
        <div>
            <label>Name</label>
            <input type="text" name="name" required>
        </div>
        <div>
            <label>Age</label>
            <input type="age" name="age" required>
        </div>
        <button type="submit">Submit</button>
    </form>
</x-layout>

