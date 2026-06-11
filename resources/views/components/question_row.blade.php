@props(['question', 'mode', 'currentItemId', 'type'])
@php
    $isInCurrentBank = $question->banks->contains($currentItemId);
    $isInCurrentTest = $question->tests->contains($currentItemId);
    if($type=='bank'){
        $target = $question->banks->firstwhere('id', $currentItemId);
    }
    else{
        $target = $question->tests->firstwhere('id', $currentItemId);
    }
    

@endphp
<div id="question-{{$question->id}}" class="card mb-3 shadow-sm border-0">
    <div class="card-body">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">
            <div class="flex-grow-1 col-md-5">
                <strong class="text-secondary small text-uppercase tracking-wider">{{__('questions.question')}}:</strong>
                <div class="fw-semibold">
                    {{ $questionSlot }}
                </div>

                @if(isset($descriptionSlot))
                    <div class="text-muted small">
                        {{ $descriptionSlot }}
                    </div>
                @endif
            </div>
            <div class="flex-grow-1 col-md-5">
                <strong class="text-secondary small text-uppercase tracking-wider">{{__('questions.answer')}}:</strong>
                <span class="text-success small">
                    {{ $answerSlot }}
                </span>
            </div>
        </div>
        <div class="card-footer bg-white border-0 pt-3">
            <div class="d-flex flex-wrap justify-content-end gap-2 w-100">
                @if($mode == "manage") 
                    @can('update', $question)
                        <button type="button" 
                            class="btn btn-warning edit-question-btn d-flex flex-shrink-1 text-nowrap align-items-center flex-shrink-0" 
                            data-id="{{ $question->id}}">
                            <i class="bi bi-pencil me-2"></i> {{__('questions.edit')}}
                        </button>
                    @endcan
                    @can('delete', $question)
                        <form class="d-inline-flex m-0 delete-question-form" id="delete-question-form-{{$question->id}}" data-id="{{ $question->id}}" action="{{ route('question.destroy', $question) }}" method="POST" onsubmit="return confirm(@js(__('questions.deleteConfirm')));"> 
                            @csrf 
                            @method('DELETE') 
                            <button type="submit" class="btn d-flex btn-danger flex-shrink-1 text-nowrap delete-question-btn">{{__('buttons.delete')}}</button> 
                        </form>
                    @endcan
                    @if($target && $target->default==FALSE)
                        <form class="d-inline-flex m-0 remove-question-form" id="remove-question-form-{{$question->id}}" data-target-id="{{$currentItemId}}" data-id="{{ $question->id}}" action="{{ route('question.removeFromBank', ['question' => $question->id, 'bank' => $currentItemId]) }}" method="POST" onsubmit="return confirm(@js(__('questions.removeConfirm')));"> 
                            @csrf 
                            @method('DELETE') 
                            <button type="submit" class="btn d-flex btn-danger flex-shrink-1 text-nowrap remove-question-btn">{{__('buttons.remove')}}</button> 
                        </form>
                    @endif
                @elseif($mode == "add")
                    @if($type=='bank' && !$isInCurrentBank)
                        <form class="d-inline-flex m-0 add-existing-question-form" id="add-existing-question-form-{{$question->id}}" data-id="{{ $question->id}}" action="{{ route('question.addToBank', ['question' => $question->id, 'bank' => $currentItemId]) }}" method="POST"> 
                            @csrf
                            <button type="submit" class="btn d-flex btn-primary flex-shrink-1 text-nowrap delete-question-btn">{{__('questions.add')}}</button> 
                        </form>
                    @elseif($type=='test' && !$isInCurrentTest)
                        <form class="d-inline-flex m-0 add-existing-question-form" id="add-existing-question-form-{{$question->id}}" data-id="{{ $question->id}}" action="{{ route('question.addToTest', ['question' => $question->id, 'test' => $currentItemId]) }}" method="POST"> 
                            @csrf
                            <button type="submit" class="btn d-flex btn-primary flex-shrink-1 text-nowrap delete-question-btn">{{__('questions.add')}}</button> 
                        </form>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>