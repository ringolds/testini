<div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
    @foreach ($tests as $test)
    <div class="col">
        <div class="card h-100 shadow-sm">
            <div class="card-body d-flex flex-column">
                <p class="card-title fw-semibold fs-4">{{ $test->name }}</p>
                <p class="card-text text-muted small">{{__('tests.author')}} : {{ $test->user->name}}</p>
                <p class="card-text text-muted small">{{__('tests.description')}} : {{ $test->description}}</p>
                @if(auth() && $ratingMode=="personal")
                    @php
                        $count = $test->individualRating(auth()->id());
                    @endphp
                @elseif($ratingMode=="general")
                    @php
                        $count = round($test->averageRating(),2);
                    @endphp
                @endif
                @if ($count!=0)
                    <div class="d-flex align-items-center gap-1 mb-3 text-muted small">
                        <span>{{ $count }}</span>
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

<div class="d-flex justify-content-center mt-5" id="pagination-container">
    {{ $tests->links('pagination::bootstrap-5') }}
</div>