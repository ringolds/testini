@props(['count', 'bank', 'test', 'mode'=>'view'])
<div id="bank-{{$bank->id}}-row" class="list-group-item d-flex justify-content-between align-items-center px-3 py-3 mb-2 bg-white rounded border shadow-sm" style="min-height: 68px;">
    <div class="d-flex align-items-center gap-4 flex-grow-1 overflow-hidden">
        <div class="d-flex align-items-center gap-2 overflow-hidden" style="flex: 1;">
            <strong class="text-primary large text-uppercase tracking-wider">{{$bank->name}}</strong>
            <div class="text-dark small fw-medium text-truncate">
                {{$count}} questions
            </div>
        </div>

        <div class="text-muted opacity-25">|</div>
        <div class="d-flex justify-content-end align-items-center gap-2">
            @can('update', [\App\Models\Test::class, $test])
                @if($mode == 'view')
                <button type="button" 
                    class="btn btn-warning edit-bank-count-btn d-flex align-items-center" 
                    data-id="{{ $bank->id}}" data-target-id="{{$test}}">
                    <i class="bi bi-pencil me-2"></i> Change question amount
                </button>
                @endif
                @if($mode == 'change')
                    <form class="d-inline m-0" id="change-bank-count-form" data-target-id="{{$test}}" data-id="{{ $bank->id}}" action="{{ route('test.updateBankCount', ['test' => $test, 'bank' => $bank]) }}" method="POST"> 
                    @csrf
                    @method('PUT') 
                        <label for="count">Choose Amount:</label>
                        <input type="number" id="count" name="count" min="1" max="100" step="1" value="{{ old('count', $count) }}">
                        <button type="submit" class="btn btn-info change-bank-count-btn">Change</button> 
                    </form>
                @endif
                <form class="d-inline m-0" id="remove-bank-form" data-target-id="{{$test}}" data-id="{{ $bank->id}}" action="{{ route('test.removeBank', ['test' => $test, 'bank' => $bank]) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this bank from test?');"> 
                    @csrf 
                    @method('DELETE') 
                    <button type="submit" class="btn btn-danger remove-bank-btn">Remove</button> 
                </form>
            @endcan
        </div>
    </div>
    <div class="ms-3 flex-shrink-0">
        <button class="btn btn-sm btn-outline-danger border-0">
            <i class="bi bi-trash"></i>
        </button>
    </div>
</div>