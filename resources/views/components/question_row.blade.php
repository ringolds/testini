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
<div id="question-{{$question->id}}" class="list-group-item d-flex justify-content-between align-items-center px-3 py-3 mb-2 bg-white rounded border shadow-sm" style="min-height: 68px;">
    <div class="d-flex align-items-center gap-4 flex-grow-1 overflow-hidden">
        <div class="d-flex align-items-center gap-2 overflow-hidden" style="flex: 1;">
            <strong class="text-secondary small text-uppercase tracking-wider">{{__('questions.question')}}:</strong>
            <div class="text-dark small fw-medium text-truncate">
                {{ $questionSlot }}
            </div>
            @if(isset($descriptionSlot))
                <div class="text-dark small fw-medium text-truncate">
                    {{ $descriptionSlot }}
                </div>
            @endif
        </div>

        <div class="text-muted opacity-25">|</div>

        <div class="d-flex align-items-center gap-2 overflow-hidden" style="flex: 1">
            <div class="d-flex align-items-start gap-2 overflow-hidden" style="flex: 1">
                <strong class="text-secondary small text-uppercase tracking-wider">{{__('questions.answer')}}:</strong>
                <div class="text-success small fw-medium text-truncate">
                    {{ $answerSlot }}
                </div>
            </div>
            @if($mode == "manage")
                <div>
                    <div class="d-flex justify-content-end align-items-center gap-2">
                        @can('update', $question)
                            <button type="button" 
                                class="btn btn-warning edit-question-btn d-flex align-items-center" 
                                data-id="{{ $question->id}}">
                                <i class="bi bi-pencil me-2"></i> {{__('questions.edit')}}
                            </button>
                        @endcan
                        @can('delete', $question)
                            <form class="d-inline m-0 delete-question-form" id="delete-question-form-{{$question->id}}" data-id="{{ $question->id}}" action="{{ route('question.destroy', $question) }}" method="POST" onsubmit="return confirm(@js(__('questions.deleteConfirm')));"> 
                                @csrf 
                                @method('DELETE') 
                                <button type="submit" class="btn btn-danger delete-question-btn">{{__('buttons.delete')}}</button> 
                            </form>
                        @endcan
                        @if($target && $target->default==FALSE)
                            <form class="d-inline m-0 remove-question-form" id="remove-question-form-{{$question->id}}" data-target-id="{{$currentItemId}}" data-id="{{ $question->id}}" action="{{ route('question.removeFromBank', ['question' => $question->id, 'bank' => $currentItemId]) }}" method="POST" onsubmit="return confirm(@js(__('questions.removeConfirm')));"> 
                                @csrf 
                                @method('DELETE') 
                                <button type="submit" class="btn btn-danger remove-question-btn">{{__('buttons.remove')}}</button> 
                            </form>
                        @endif
                    </div>
                </div>
            @elseif($mode == "add")
                <div>
                    <div class="d-flex justify-content-end align-items-center gap-2">
                        @if($type=='bank' && !$isInCurrentBank)
                            <form class="d-inline m-0 add-existing-question-form" id="add-existing-question-form-{{$question->id}}" data-id="{{ $question->id}}" action="{{ route('question.addToBank', ['question' => $question->id, 'bank' => $currentItemId]) }}" method="POST"> 
                                @csrf
                                <button type="submit" class="btn btn-primary delete-question-btn">{{__('questions.add')}}</button> 
                            </form>
                        @elseif($type=='test' && !$isInCurrentTest)
                            <form class="d-inline m-0 add-existing-question-form" id="add-existing-question-form-{{$question->id}}" data-id="{{ $question->id}}" action="{{ route('question.addToTest', ['question' => $question->id, 'test' => $currentItemId]) }}" method="POST"> 
                                @csrf
                                <button type="submit" class="btn btn-primary delete-question-btn">{{__('questions.add')}}</button> 
                            </form>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div class="ms-3 flex-shrink-0">
        <button class="btn btn-sm btn-outline-danger border-0">
            <i class="bi bi-trash"></i>
        </button>
    </div>
</div>