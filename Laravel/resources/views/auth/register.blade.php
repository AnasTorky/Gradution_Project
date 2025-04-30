<x-layout>
    <form method="POST" action="{{ route('register.store') }}">
        @csrf
        <div>
            <label>Name</label>
            <input type="text" name="name" required>
        </div>
        @error('name')
            <p class="alert alert-danger mt-2">{{ $message }}</p>
        @enderror
        <div>
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        @error('email')
            <p class="alert alert-danger mt-2">{{ $message }}</p>
        @enderror
        <div>
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        @error('password')
        <p class="alert alert-danger mt-2">{{ $message }}</p>
    @enderror
        
        <div>
            <label>Confirm Password</label>
            <input type="password" name="password_confirmation" required>
        </div>
        @error('password_confirmation')
        <p class="alert alert-danger mt-2">{{ $message }}</p>
    @enderror
        <button type="submit">Register</button>
    </form>
</x-layout>
