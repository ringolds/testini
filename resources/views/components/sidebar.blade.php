<nav id="sidebar" class="bg-dark text-white p-3">
        <h3>Platform</h3>
        <hr>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ route('welcome') }}">Home</a>
            </li>

            @auth
                <li class="nav-item">
                    <a class="nav-link text-white" href="#">My Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="#">Take a Test</a>
                </li>

                @if(auth()->user()->is_admin)
                    <hr>
                    <small class="text-uppercase text-secondary">Admin Tools</small>
                    <li class="nav-item">
                        <a class="nav-link text-warning" href="#">Manage Banks</a>
                    </li>
                @endif
            @else
                <li class="nav-item">
                    <a class="nav-link text-white-50" href="{{ route('login') }}">Login to start</a>
                </li>
            @endauth
        </ul>
    </nav>