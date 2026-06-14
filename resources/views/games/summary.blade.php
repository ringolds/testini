<div class="card p-3">
    <p class="text-dark fw-semibold fs-3">{{__('game.summary')}}</p>
    <p class="text-dark fw-medium fs-4">{{__('game.correct')}}: {{$score}}/{{$total}}</p>
    <p class="text-dark fw-medium fs-4">{{__('game.timeSpent')}}: {{$duration}}</p>
    <x-star_rating :stars="$stars" :testId="$testId"></x-star_rating>
    <a href="/">{{__('buttons.returnHome')}}</a>
</div>
