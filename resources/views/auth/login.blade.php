<x-layout>
    <x-slot:title>
        {{__('auth.login')}}
    </x-slot:title>
<form action="{{ route('login') }}" method="POST">
    @csrf   
    
    @if ($errors->any())
        <div class="alert alert-danger">
            {{__('auth.failed')}}
        </div>
    @endif
    <div class="mb-3">
        <label for="email" class="form-label">{{__('auth.email')}}</label>
        <input type="email" name="email" class="form-control" required
            value="{{ old('email') }}">

    </div>
    <div class="mb-3">
        <label for="password" class="form-label">{{__('auth.password')}}</label>
        <input type="password" name="password" class="form-control" required>

    </div>
    <button type="submit" class="btn btn-success">{{__('auth.login')}}</button>
    <div class="mt-3">
        <a href="{{ route('google.login') }}" class="btn btn-outline-dark w-100">
            <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" width="20" class="me-2">
            {{__('auth.google')}}
        </a>
    </div>
    <a href="{{ route('register') }}" class="btn btn-link">
       {{__('auth.noAccount')}}
    </a>
</form>
</x-layout>