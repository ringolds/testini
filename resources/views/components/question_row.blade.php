@props(['question', 'mode', 'currentItemId'])
@php
    $isInCurrentBank = $question->banks->contains($currentItemId);
@endphp
<div id="question-{{$question->id}}" class="list-group-item d-flex justify-content-between align-items-center px-3 py-3 mb-2 bg-white rounded border shadow-sm" style="min-height: 68px;">
    <div class="d-flex align-items-center gap-4 flex-grow-1 overflow-hidden">
        
        <div class="d-flex align-items-center gap-2 overflow-hidden" style="flex: 1;">
            <strong class="text-secondary small text-uppercase tracking-wider">Question:</strong>
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
                <strong class="text-secondary small text-uppercase tracking-wider">Answer:</strong>
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
                                <i class="bi bi-pencil me-2"></i> Edit question
                            </button>
                        @endcan
                        @can('delete', $question)
                            <form class="d-inline m-0" id="delete-question-form" data-id="{{ $question->id}}" action="{{ route('question.destroy', $question) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this question?');"> 
                                @csrf 
                                @method('DELETE') 
                                <button type="submit" class="btn btn-danger delete-question-btn">Delete</button> 
                            </form>
                        @endcan
                    </div>
                </div>
            @elseif($mode == "add")
                <div>
                    <div class="d-flex justify-content-end align-items-center gap-2">
                        @if(!$isInCurrentBank)
                            <button type="button" 
                                class="btn btn-warning edit-question-btn d-flex align-items-center" 
                                data-id="{{ $question->id}}">
                                <i class="bi bi-pencil me-2"></i> Move question
                            </button>
                            <form class="d-inline m-0" id="add-existing-question-form" data-id="{{ $question->id}}" action="{{ route('question.addToBank', ['question' => $question->id, 'bank' => $currentItemId]) }}" method="POST"> 
                                @csrf
                                <button type="submit" class="btn btn-danger delete-question-btn">Add question</button> 
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