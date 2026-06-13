@vite('resources/js/question_manager.js')
<div class="card shadow-sm border-0 d-flex flex-column" style="height: 60vh;">
    <div class="card-header bg-white py-3">
        <h1 class="mb-0">{{ $test->name }}</h1>
    </div>
    <div class="card-body flex-grow-1 overflow-auto">
        <p class="text-muted small">{{ $test->description }}</p>
        <x-question_block :item="$test" :mode="$mode" :currentItemId="$target_id" :collection_type="$type"></x-question_block>
    </div>
    <div class="card-footer bg-white border-0 pt-3">
        <div class="d-flex flex-wrap justify-content-end gap-2 w-100">
            @if($mode=="manage")
                @can('publish', $test)
                    <form id="publish-test-form" method="POST" action="{{ route('test.publish', $test)}}"> 
                        @csrf
                        <button type="submit" class="btn btn-primary">{{__('tests.publish')}} </button> 
                    </form> 
                @endcan
                @can('update', $test)
                    <button type="button" 
                        class="btn btn-warning edit-test-btn d-flex align-items-center" 
                        data-id="{{ $test->id }}">
                        <i class="bi bi-pencil me-2"></i> {{__('tests.edit')}} 
                    </button>
                @endcan
                @can('delete', $test)
                    <form id="delete-test-form" data-id="{{ $test->id }}" class="d-inline m-0" action="{{ route('test.destroy', $test) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this test?');"> 
                        @csrf 
                        @method('DELETE') 
                        <button type="submit" class="btn btn-danger delete-test-btn">{{__('buttons.delete')}} </button> 
                    </form>
                @endcan
                <button type="button" class="btn btn-primary add-existing-question-test-btn d-flex align-items-center" 
                    data-id="{{ $test->id }}">
                    <i class="bi bi-pencil me-2"></i> {{__('buttons.addQuestion')}} 
                </button>
                <button type="button" 
                    class="btn btn-success add-new-question-btn d-flex align-items-center" 
                    data-id="{{ $test->id }}">
                    <i class="bi bi-pencil me-2"></i> {{__('buttons.addNewQuestion')}} 
                </button>
                <button type="button" 
                    class="btn btn-success add-random-question-btn d-flex align-items-center" 
                    data-id="{{ $test->id }}">
                    <i class="bi bi-pencil me-2"></i> {{__('buttons.addRandomQuestion')}} 
                </button>
            @endif
        </div>
    </div>
</div>