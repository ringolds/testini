<x-layout> 
    <x-slot name="title"> 
        {{__('tests.available')}} 
    </x-slot> 
    <div class="container my-5">
        <h1 class="mb-4">{{__('tests.available')}} </h1>

        <div class="d-flex justify-content-end mb-3">
            <label class="me-2 align-self-center">{{__('tests.cardsPerPage')}} :</label>
            <a href="{{ request()->fullUrlWithQuery(['per_page' => 12, 'page' => 1]) }}" class="btn btn-sm {{ request('per_page', 12) == 12 ? 'btn-primary' : 'btn-outline-primary' }} me-1">12</a>
            <a href="{{ request()->fullUrlWithQuery(['per_page' => 24, 'page' => 1]) }}" class="btn btn-sm {{ request('per_page') == 24 ? 'btn-primary' : 'btn-outline-primary' }}">24</a>
        </div>
        <div class="text-danger small mb-2 ajax-errors-test"></div>
        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
            @foreach ($tests as $test)
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <p class="card-title fw-semibold fs-4">{{ $test->name }}</p>
                            <p class="card-text text-muted small">{{__('tests.author')}} : {{ $test->user->name}}</p>
                            <p class="card-text text-muted small">{{__('tests.description')}} : {{ $test->description}}</p>
                            @php
                                $count = $test->individualRating(auth()->id());
                            @endphp
                            @if ($count!=0)
                                <div class="d-flex align-items-center gap-1 mb-3 text-muted small">
                                    <span>{{ $test->individualRating(auth()->id()) }}</span>
                                    <i class="bi bi-star-fill text-warning"></i>
                                </div>
                            @else
                                <div class="d-flex align-items-center gap-1 mb-3 text-muted small">
                                    <span>{{__('game.noRating')}}</span>
                                </div>
                            @endif
                            
                            @can('start', $test)
                                <a href="{{ route('game.start', $test->id) }}" class="btn btn-success mt-auto w-100 play-btn">{{__('tests.play')}} </a>
                            @endcan
                            @can('continue', $test)
                                <a href="{{ route('game.start', $test->id) }}" class="btn btn-warning mt-auto w-100 play-btn">{{__('tests.continue')}} </a>
                            @endcan
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-5">
            {{ $tests->links('pagination::bootstrap-5') }}
        </div>
    </div>
    <script>
        $(document).on('click', '.play-btn', function(e) {
            e.preventDefault();
            
            const url = $(this).attr('href');

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(async response => {
                if (!response.ok) {
                    const data = await response.json();

                    if (data.error) {
                        $('.ajax-errors-test').html(
                            `<div class="alert alert-danger">${data.error}</div>`
                        );
                        return;
                    }
                }

                window.location.href = url;
            })
            .catch(err => {
                console.error(err);
                alert(window.translations.errors.unexpectedError);
            });
        });
    </script>
</x-layout>