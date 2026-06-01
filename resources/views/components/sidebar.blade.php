<nav id="sidebar" class="bg-dark text-white p-3">
        <h3>Testiņi</h3>
        <hr>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ route('home') }}">Home</a>
            </li>

            @auth
                <li class="nav-item">
                    <a class="nav-link text-white" href="/bank">My banks</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="/test">My tests</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="/question/create">Create questions</a>
                </li>

                @if(auth()->user()->is_admin)
                    <hr>
                    <small class="text-uppercase text-secondary">Admin Tools</small>
                    <li class="nav-item">
                        <a class="nav-link text-warning" href="/bank">Manage banks</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-warning" href="/map">Manage maps</a>
                    </li>
                @endif
            @else
                <li class="nav-item">
                    <a class="nav-link text-white-50" href="{{ route('login') }}">Login to start</a>
                </li>
            @endauth
        </ul>
    </nav>