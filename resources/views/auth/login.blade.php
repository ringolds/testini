<x-layout>
    <x-slot:title>
        Login
    </x-slot:title>
<form action="{{ route('login') }}" method="POST">
    @csrf   
    
    @if ($errors->any())
        <div class="alert alert-danger">
            Invalid email or password. Please try again.
        </div>
    @endif
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required
            value="{{ old('email') }}">

    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>

    </div>
    <button type="submit" class="btn btn-success">Login</button>
    <a href="{{ route('register') }}" class="btn btn-link">
        Don't have an account?
    </a>
</form>
</x-layout>