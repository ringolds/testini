<!DOCTYPE html> 
<html lang="en"> 
    <head> 
        <meta charset="UTF-8"> 
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 

        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title }}</title> 
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"> 
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            window.translations = {
                errors: @json(__('errors')),
                game: @json(__('game')),
                publishedBank: @json(__('banks.published')),
                publishedTest: @json(__('tests.published')),
            };
        </script>
        <style>
            body { overflow-x: hidden; }
            #wrapper { display: flex; width: 100%; align-items: stretch; }
            #sidebar { min-width: 250px; max-width: 250px; min-height: 100vh; transition: all 0.3s; }
            #content { flex-grow: 1; min-width: 0; }
            .navbar-top { border-bottom: 1px solid #dee2e6; }
        </style>
        @stack('styles')
    </head> 
    <body> 
        <div id="wrapper">
            <x-sidebar/>
            <div id="content">
                <nav class="navbar navbar-expand-lg navbar-light bg-light navbar-top p-3">
                    <div class="container-fluid">
                        <div class="ms-auto d-flex align-items-center gap-3">
                            <button
                                class="btn btn-outline-secondary d-lg-none"
                                type="button"
                                data-bs-toggle="offcanvas"
                                data-bs-target="#mobileSidebar"
                                aria-controls="mobileSidebar">
                                ☰
                            </button>
                            @auth
                                <div class="dropdown">
                                    <span class="me-3">{{ auth()->user()->name }}</span>
                                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">{{__('auth.logout')}}</button>
                                    </form>
                                </div>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary">{{__('auth.login')}}</a>
                                <a href="{{ route('register') }}" class="btn btn-sm btn-primary">{{__('auth.register')}}</a>
                            @endauth
                            <div class="dropdown me-3">
                                <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center gap-2" 
                                        type="button" 
                                        id="languageDropdown" 
                                        data-bs-toggle="dropdown" 
                                        aria-expanded="false">
                                    <span>{{ strtoupper(app()->getLocale()) }}</span>
                                </button>
                                
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                                    <li>
                                        <a class="dropdown-item d-flex justify-content-between align-items-center {{ app()->getLocale() === 'en' ? 'active' : '' }}" 
                                        href="{{ route('lang.switch', 'en') }}">
                                            English <span>EN</span>
                                        </a>
                                    </li>
                                    
                                    <li>
                                        <a class="dropdown-item d-flex justify-content-between align-items-center {{ app()->getLocale() === 'lv' ? 'active' : '' }}" 
                                        href="{{ route('lang.switch', 'lv') }}">
                                            Latviešu <span>LV</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>

                <main class="p-4">
                    {{ $slot }}
                </main>
            </div>
        </div>
        <div
            class="offcanvas offcanvas-start bg-dark text-white"
            tabindex="-1"
            id="mobileSidebar">

            <div class="offcanvas-header">
                <p class="text-white fs-1">Testiņi</p>

                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="offcanvas"
                    aria-label="Close">
                </button>
            </div>

            <div class="offcanvas-body">
                <x-sidebar_links></x-sidebar_links>
            </div>
        </div>
    </body> 
</html> 

