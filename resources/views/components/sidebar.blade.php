<nav id="sidebar" class="bg-dark text-white p-3">
    <p class="text-white fs-1">Testiņi</p>
    <hr>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link text-white" href="{{ route('home') }}">{{__('sidebar.home')}}</a>
        </li>

        @auth
            <li class="nav-item">
                <a class="nav-link text-white" href="/bank">{{__('banks.myBanks')}}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="/test">{{__('tests.myTests')}}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="/question/create">{{__('questions.create')}}</a>
            </li>

            @if(auth()->user()->is_admin)
                <li>
                    <hr>
                </li>
                <li>
                    <p class="text-uppercase text-secondary">{{__('sidebar.adminTools')}}</p>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-warning" href="/map">{{__('sidebar.manageMaps')}}</a>
                </li>
            @endif
        @else
            <li class="nav-item">
                <a class="nav-link text-white-50" href="{{ route('login') }}">{{__('sidebar.loginStart')}}</a>
            </li>
        @endauth
    </ul>
</nav>