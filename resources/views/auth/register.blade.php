<x-layout>
    <x-slot:title>
        Register
    </x-slot:title>
<form action="{{ route('register') }}" method="POST">
    @csrf    
    <div class="mb-3">
        <label for="name" class="form-label">Username</label>
        <input type="text" name="name" class="form-control" required
            value="{{ old('name') }}">
        @error('name') 
            <small class="text-danger">{{ $message }}</small> 
        @enderror
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required
            value="{{ old('email') }}">
        @error('email') 
            <small class="text-danger">{{ $message }}</small> 
        @enderror
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
        @error('password') 
            <small class="text-danger">{{ $message }}</small> 
        @enderror
    </div>
    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <input type="password" name="password_confirmation" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">Register</button>
    <a href="{{ route('login') }}" class="btn btn-link">
        Already have an account?
    </a>
</form>
</x-layout>