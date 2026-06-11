@props(['question', 'resultItem', 'description'=>null])
<div class="card p-3" id="question-{{$resultItem->id}}">
    <p class="text-dark fw-semibold fs-3">{{__('questions.question')}}</p>
    @if($question instanceof \App\Models\QuestionText)
        <p class="text-dark fw-medium fs-5">{{$question->text}}</p>
    @elseif($question instanceof \App\Models\QuestionImage)
        <div class="d-flex justify-content-center w-100">
            <img src="{{ asset('storage/' . $question->path) }}" 
                alt="{{ $question->alt_text }}" 
                class="img-fluid" 
                style="
                    height: 30vh; 
                    width: auto; 
                    object-fit: contain;"
                title="{{ $question->alt_text }}">
        </div>
    @elseif($question instanceof \App\Models\QuestionMap)
        <div class="form-group mb-3 dynamic-field-map">
            <div id="question-map" class="interactive-map" style="width: 100%; height: 600px; min-height: 600px; background-color: #ffffff;" 
                data-config-endpoint="{{ route('game.mapConfig', ['resultItem' => $resultItem->id, 'mode' => 'question']) }}">
            </div>
        </div>
    @endif
    @if($description!=null)
        <p class="text-dark fw-medium fs-5">{{$description->text}}</h3>
    @endif
</div>
