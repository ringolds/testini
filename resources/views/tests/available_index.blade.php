<x-layout> 
    <x-slot name="title"> 
        Available tests 
    </x-slot> 
    <div class="container my-5">
        <h2 class="mb-4">Available Public Tests</h2>

        <div class="d-flex justify-content-end mb-3">
            <label class="me-2 align-self-center">Cards per page:</label>
            <a href="{{ request()->fullUrlWithQuery(['per_page' => 12, 'page' => 1]) }}" class="btn btn-sm {{ request('per_page', 12) == 12 ? 'btn-primary' : 'btn-outline-primary' }} me-1">12</a>
            <a href="{{ request()->fullUrlWithQuery(['per_page' => 24, 'page' => 1]) }}" class="btn btn-sm {{ request('per_page') == 24 ? 'btn-primary' : 'btn-outline-primary' }}">24</a>
        </div>

        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
            @foreach ($tests as $test)
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $test->name }}</h5>
                            <p class="card-text text-muted small">Author: {{ $test->user->name}}</p>
                            <p class="card-text text-muted small">Description: {{ $test->description}}</p>
                            <a href="{{ route('game.start', $test->id) }}" class="btn btn-success mt-auto w-100">Play Test</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-5">
            {{ $tests->links('pagination::bootstrap-5') }}
        </div>
    </div>
</x-layout>