<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">{{ $bank->name }}</h5>
    </div>
    <div class="card-body">
        <p class="text-muted small">{{ $bank->description }}</p>
        <x-question_block :item="$bank"></x-question_block>
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
                <form id="delete-bank-form" data-id="{{ $bank->id }}" class="d-inline m-0" action="{{ route('bank.destroy', $bank) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this bank?');"> 
                    @csrf 
                    @method('DELETE') 
                    <button type="submit" class="btn btn-danger delete-bank-btn">Delete</button> 
                </form>
            @endcan
        </div>
    </div>
</div>