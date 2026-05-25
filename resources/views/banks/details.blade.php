<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">{{ $bank->name }}</h5>
    </div>
    <div class="card-body">
        <p class="text-muted small">{{ $bank->description }}</p>
        <div class="list-group list-group-flush mt-3">
            @forelse($bank->questions as $question)
                <x-question_row>
                    <x-slot:questionSlot>
                        @if($question->prompt && $question->prompt->component)
                            @php
                                $type = $question->prompt->component_type;
                                $comp = $question->prompt->component;
                                $description = $question->description;
                            @endphp

                            @if($type === 'App\Models\QuestionText')
                                <span>{{ str($comp->text)->limit(50) }}</span>
                            @elseif($type === 'App\Models\QuestionImage')
                                <img src="{{ asset('storage/' . $comp->path) }}" 
                                    alt="{{$comp->alt_text}}", 
                                    class="rounded border" 
                                    style="width: 45px; height: 45px; object-fit: cover;"
                                    title="{{ $comp->alt_text }}">
                            @else
                                <span class="text-muted fst-italic">Alternative Component</span>
                            @endif
                        @else
                            <span class="text-danger">Missing Prompt</span>
                        @endif
                    </x-slot:questionSlot>
                    @if($question->description && $question->description->component)
                        @php
                            $description = $question->description->component;
                        @endphp
                        <x-slot:descriptionSlot>
                            <span>{{ str($description->text)->limit(50) }}</span>
                        </x-slot:descriptionSlot>
                    @endif
                    <x-slot:answerSlot>
                        @if($question->answer && $question->answer->component)
                            @php
                                $type = $question->answer->component_type;
                                $comp = $question->answer->component;
                            @endphp

                            @if($type === 'App\Models\QuestionText')
                                <span>{{ str($comp->text)->limit(50) }}</span>
                            @elseif($type === 'App\Models\QuestionImage')
                                <img src="{{ asset('storage/' . $comp->path) }}" 
                                    alt="A preview" 
                                    class="rounded border" 
                                    style="width: 45px; height: 45px; object-fit: cover;"
                                    title="{{ $comp->alt_text }}">
                            @else
                                <span class="text-muted fst-italic">Alternative Component</span>
                            @endif
                        @else
                            <span class="text-danger">Missing Answer</span>
                        @endif
                    </x-slot:answerSlot>
                </x-question_row>
            @empty
                <div class="py-3 text-center text-muted">No questions yet.</div>
            @endforelse
        </div>
    </div>
    <div class="card-footer bg-white py-2">
        <div class="d-flex justify-content-end align-items-center gap-2">
            @can('update', $bank)
                <button type="button" 
                    class="btn btn-warning edit-bank-btn d-flex align-items-center" 
                    data-id="{{ $bank->id }}">
                    <i class="bi bi-pencil me-2"></i> Edit bank
                </button>
            @endcan
            @can('delete', $bank)
                <form action="{{ route('bank.destroy', $bank) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this bank?');"> 
                    @csrf 
                    @method('DELETE') 
                    <button type="submit" class="btn btn-danger">Delete</button> 
                </form>
            @endcan
        </div>
    </div>
</div>