@vite('resources/js/question_manager.js')
<div class="card shadow-sm border-0 d-flex flex-colum" style="height: 70vh;">
    <div class="card-header bg-white py-3">
        <h1 class="mb-0">{{ $bank->name }}</h1>
    </div>
    <div class="card-body flex-grow-1 overflow-auto">
        <p class="text-muted small">{{ $bank->description }}</p>
        <x-question_block :item="$bank" :mode="$mode" :currentItemId="$target_id" :collection_type="$type"></x-question_block>
    </div>
    <div class="card-footer bg-white border-0 pt-3">
        <div class="d-flex flex-wrap justify-content-end gap-2 w-100">
            @if($mode=="manage")
                @can('update', $bank)
                    <button type="button" 
                        class="btn btn-warning edit-bank-btn d-flex align-items-center" 
                        data-id="{{ $bank->id }}">
                        <i class="bi bi-pencil me-2"></i>{{__('banks.edit')}}
                    </button>
                @endcan
                @can('delete', $bank)
                    <form id="delete-bank-form" data-id="{{ $bank->id }}" class="d-inline m-0" action="{{ route('bank.destroy', $bank) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this bank?');"> 
                        @csrf 
                        @method('DELETE') 
                        <button type="submit" class="btn btn-danger delete-bank-btn">{{__('buttons.delete')}}</button> 
                    </form>
                @endcan
                @can('addExistingQuestion', $bank)
                    <button type="button" class="btn btn-primary add-existing-question-bank-btn d-flex align-items-center" 
                        data-id="{{ $bank->id }}">
                        <i class="bi bi-pencil me-2"></i> {{__('buttons.addQuestion')}}
                    </button>
                @endcan
                <button type="button" 
                    class="btn btn-success add-new-question-btn d-flex align-items-center" 
                    data-id="{{ $bank->id }}">
                    <i class="bi bi-pencil me-2"></i> {{__('buttons.addNewQuestion')}}
                </button>
            @endif
            @if($mode=="addBank")
                <form id="add-random-questions-form" data-id="{{ $bank->id }}" data-target-id="{{$target_id}}" class="d-inline m-0" action="{{ route('test.saveBank', ['test'=>$target_id, 'bank'=>$bank]) }}" method="POST"> 
                    @csrf
                    <label for="count">{{__('buttons.amount')}}</label>
                    <input type="number" id="count" name="count" min="1" max="100" step="1" value="1">
                    <button type="submit" class="btn btn-success">{{__('buttons.add')}}</button> 
                </form>
            @endif
        </div>
    </div>
</div>