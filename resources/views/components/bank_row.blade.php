@props(['count', 'bank', 'test', 'mode'=>'view'])
<div id="bank-{{$bank->id}}-row" class="card mb-3 shadow-sm border-0">
    <div class="card-body">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">
            <div class="flex-grow-1 col-md-5">
                <strong class="text-primary large text-uppercase tracking-wider">{{$bank->name}}</strong>
                <div class="text-dark small fw-medium text-truncate">
                    {{$count}} {{__('questions.questions')}}
                </div>
            </div>
        </div>

        <div class="card-footer bg-white border-0 pt-3">
            <div class="d-flex flex-wrap justify-content-end gap-2 w-100">
                <div id="ajax-errors-bank-{{$bank->id}}" class="text-danger small mb-2"></div>
                @can('update', [\App\Models\Test::class, $test])
                    @if($mode == 'view')
                    <button type="button" 
                        class="btn btn-warning edit-bank-count-btn d-flex align-items-center" 
                        data-id="{{ $bank->id}}" data-target-id="{{$test}}">
                        <i class="bi bi-pencil me-2"></i> {{__('questions.changeAmount')}}
                    </button>
                    @endif
                    @if($mode == 'change')
                        <form class="d-inline m-0 change-bank-count-form" id="change-bank-count-form-{{$bank->id}}" data-target-id="{{$test}}" data-id="{{ $bank->id}}" action="{{ route('test.updateBankCount', ['test' => $test, 'bank' => $bank]) }}" method="POST"> 
                        @csrf
                        @method('PUT') 
                            <label for="count">{{__('buttons.amount')}}</label>
                            <input type="number" id="count" name="count" min="1" max="100" step="1" value="{{ old('count', $count) }}">
                            <button type="submit" class="btn btn-info change-bank-count-btn">{{__('buttons.change')}}</button> 
                        </form>
                    @endif
                    <form class="d-inline m-0 remove-bank-form" id="remove-bank-form-{{$bank->id}}" data-target-id="{{$test}}" data-id="{{ $bank->id}}" action="{{ route('test.removeBank', ['test' => $test, 'bank' => $bank]) }}" method="POST" onsubmit="return confirm(@js(__('banks.removeBankConfirm')));"> 
                        @csrf 
                        @method('DELETE') 
                        <button type="submit" class="btn btn-danger remove-bank-btn">
                            <i class="bi bi-trash me-2"></i> {{__('buttons.remove')}}</button> 
                    </form>
                @endcan
            </div>
        </div>
    </div>
</div>