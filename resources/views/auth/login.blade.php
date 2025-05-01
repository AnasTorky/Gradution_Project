<x-layout>
    <form method="POST" action="{{ route('login') }}">
        @csrf
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
        <button type="submit">Login</button>
    </form>
</x-layout>
