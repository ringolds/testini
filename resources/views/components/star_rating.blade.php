@props(['stars' => null, 'testId'])
<form class="mb-3 rating-form" data-id="{{ $testId }}">
    
    <label class="form-label">{{ __('game.rateText') }}</label>

    <div class="ajax-errors-rating text-danger small mb-2"></div>

    <div class="star-rating">
        @for ($i = 1; $i <= 5; $i++)
            <i class="bi bi-star fs-3 text-secondary star"
               data-rating="{{ $i }}"></i>
        @endfor
    </div>

    <input type="hidden"
           name="rating"
           class="rating-input"
           value="{{ $stars ?? old('rating', 0) }}">
</form>
@vite('resources/js/rating_manager.js')
