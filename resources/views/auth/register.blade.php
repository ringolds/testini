<x-layout>
    <x-slot:title>
        {{__('auth.register')}}
    </x-slot:title>
<form action="{{ route('register') }}" method="POST">
    @csrf    
    <div class="mb-3">
        <label for="name" class="form-label">{{__('auth.username')}}</label>
        <input type="text" name="name" class="form-control" required
            value="{{ old('name') }}">
        @error('name') 
            <small class="text-danger">{{ $message }}</small> 
        @enderror
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">{{__('auth.email')}}</label>
        <input type="email" name="email" class="form-control" required
            value="{{ old('email') }}">
        @error('email') 
            <small class="text-danger">{{ $message }}</small> 
        @enderror
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">{{__('auth.password')}}</label>
        <input type="password" name="password" class="form-control" required>
        @error('password') 
            <small class="text-danger">{{ $message }}</small> 
        @enderror
    </div>
    <div class="mb-3">
        <label for="password_confirmation" class="form-label">{{__('auth.passwordConfirm')}}</label>
        <input type="password" name="password_confirmation" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">{{__('auth.register')}}</button>
    <div class="mt-3">
        <a href="{{ route('google.login') }}" class="btn btn-outline-dark w-100">
            <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" width="20" class="me-2">
            {{__('auth.google')}}
        </a>
    </div>
    <a href="{{ route('login') }}" class="btn btn-link">
        {{__('auth.hasAccount')}}
    </a>
</form>
</x-layout>