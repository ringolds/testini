@props(['question', 'answerType', 'resultItem', 'description'=>null])
<div id="game-entry-{{ $resultItem->id }}" class="d-flex flex-column gap-4 w-100">
    @include('games.question', ['question'=>$question, 'resultItem'=>$resultItem, 'description'=>$description])
    @include('games.answer', ['answerType'=>$answerType, 'answerMode'=> $answerMode, 'resultItem'=> $resultItem, 'choices'=>$choices])
</div>