@props(['answerType', 'answerMode', 'resultItem', 'choices'=>null])

<div class="card p-3" id="answer-{{$resultItem->id}}">
    <h4>Answer</h4>
    <form id="answer-form">
        @if($answerMode=='single')
            @if($answerType=='App\Models\QuestionText')
                <input type="text" class="form-control" name="question_answer">
            @elseif($answerType=='App\Models\QuestionMap')
                <div class="form-group mb-3 dynamic-field-map">
                    <div id="answer-map" class="interactive-map" style="width: 100%; height: 600px; min-height: 600px; background-color: #ffffff;" 
                        data-config-endpoint="{{ route('game.mapConfig', ['resultItem' => $resultItem->id, 'mode' => 'answer']) }}">
                    </div>
                    <input type="hidden" class="selected-target" name="answer_map_target">
                </div>
            @endif
        @elseif($answerMode=='multiple')
            <div class="container px-0">
                <div class="row g-3 justify-content-center">
                    
                    @foreach($choices as $item)
                        @php
                            $answerCount = $choices->count();
                            $colClass = ($answerCount === 4) ? 'col-md-6' : (($answerCount === 3) ? 'col-md-4' : 'col-md-6');
                        @endphp

                        <div class="col-12 {{ $colClass }}">
                            <button type="button" 
                                    class="multiple-choice-btn btn btn-outline-light text-dark border p-3 w-100 h-100 shadow-sm d-flex flex-column align-items-center justify-content-center option-box"
                                    id="multiple-choice-{{$item->id}}"
                                    data-answer-id="{{ $item->id }}">
                                
                                @if($answerType=='App\Models\QuestionImage')
                                    <img src="{{ asset('storage/' . $item->path) }}" 
                                        class="img-fluid mb-2" 
                                        style="max-height: 120px; object-fit: contain;">
                                @else
                                    <span class="fs-5 fw-medium">{{ $item->text }}</span>
                                @endif
                            </button>
                        </div>
                    @endforeach
                    <input type="hidden" id="multiple-choice" name="multiple_choice">
                </div>
            </div>
        @endif
        <button type="submit" id="submit-btn" class="btn btn-primary mt-4">Submit</button> 
    </form>
</div>