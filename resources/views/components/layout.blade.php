<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>{{ $title }}</title> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"> 
    <style>
        body { overflow-x: hidden; }
        #wrapper { display: flex; width: 100%; align-items: stretch; }
        #sidebar { min-width: 250px; max-width: 250px; min-height: 100vh; transition: all 0.3s; }
        #content { width: 100%; }
        .navbar-top { border-bottom: 1px solid #dee2e6; }
    </style>
</head> 
<body> 
        <div id="wrapper">
        <x-sidebar/>
        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light bg-light navbar-top p-3">
                <div class="container-fluid">
                    <span class="navbar-text">
                        {{ $title ?? 'Welcome' }}
                    </span>
                    
                    <div class="ms-auto">
                        @auth
                            <div class="dropdown">
                                <span class="me-3">Hi, {{ auth()->user()->name }}</span>
                                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Logout</button>
                                </form>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary">Login</a>
                            <a href="{{ route('register') }}" class="btn btn-sm btn-primary">Sign Up</a>
                        @endauth
                    </div>
                </div>
            </nav>

            <main class="p-4">
                {{ $slot }}
            </main>
        </div>
    </div>
</body> 
</html> 

