@vite('resources/js/question_manager.js')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">{{ $bank->name }}</h5>
    </div>
    <div class="card-body">
        <p class="text-muted small">{{ $bank->description }}</p>
        <x-question_block :item="$bank" :mode="$mode" :currentItemId="$target_id"></x-question_block>
    </div>
    <div class="card-footer bg-white py-2">
        <div class="d-flex justify-content-end align-items-center gap-2">
            @if($mode=="manage")
                @can('update', $bank)
                    <button type="button" 
                        class="btn btn-warning edit-bank-btn d-flex align-items-center" 
                        data-id="{{ $bank->id }}">
                        <i class="bi bi-pencil me-2"></i> Edit bank
                    </button>
                @endcan
                @can('delete', $bank)
                    <form id="delete-bank-form" data-id="{{ $bank->id }}" class="d-inline m-0" action="{{ route('bank.destroy', $bank) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this bank?');"> 
                        @csrf 
                        @method('DELETE') 
                        <button type="submit" class="btn btn-danger delete-bank-btn">Delete</button> 
                    </form>
                @endcan
                @can('addExistingQuestion', $bank)
                    <button type="button" class="btn btn-primary add-existing-question-bank-btn d-flex align-items-center" 
                        data-id="{{ $bank->id }}">
                        <i class="bi bi-pencil me-2"></i> Add existing question
                    </button>
                @endcan
                <button type="button" 
                    class="btn btn-success add-new-question-btn d-flex align-items-center" 
                    data-id="{{ $bank->id }}">
                    <i class="bi bi-pencil me-2"></i> Add new question
                </button>
            @endif
        </div>
    </div>
</div>