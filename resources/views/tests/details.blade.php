@vite('resources/js/question_manager.js')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">{{ $test->name }}</h5>
    </div>
    <div class="card-body">
        <p class="text-muted small">{{ $test->description }}</p>
        <x-question_block :item="$test" :mode="$mode" :currentItemId="$target_id" :collection_type="$type"></x-question_block>
    </div>
    <div class="card-footer bg-white py-2">
        <div class="d-flex justify-content-end align-items-center gap-2">
            @if($mode=="manage")
                @can('update', $test)
                    <button type="button" 
                        class="btn btn-warning edit-test-btn d-flex align-items-center" 
                        data-id="{{ $test->id }}">
                        <i class="bi bi-pencil me-2"></i> Edit test
                    </button>
                @endcan
                @can('delete', $test)
                    <form id="delete-test-form" data-id="{{ $test->id }}" class="d-inline m-0" action="{{ route('test.destroy', $test) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this test?');"> 
                        @csrf 
                        @method('DELETE') 
                        <button type="submit" class="btn btn-danger delete-test-btn">Delete</button> 
                    </form>
                @endcan
                <button type="button" class="btn btn-primary add-existing-question-test-btn d-flex align-items-center" 
                    data-id="{{ $test->id }}">
                    <i class="bi bi-pencil me-2"></i> Add existing question
                </button>
                <button type="button" 
                    class="btn btn-success add-new-question-btn d-flex align-items-center" 
                    data-id="{{ $test->id }}">
                    <i class="bi bi-pencil me-2"></i> Add new question
                </button>
                <button type="button" 
                    class="btn btn-success add-random-question-btn d-flex align-items-center" 
                    data-id="{{ $test->id }}">
                    <i class="bi bi-pencil me-2"></i> Add random questions
                </button>
            @endif
        </div>
    </div>
</div>