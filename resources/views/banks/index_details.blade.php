@push('styles')
<style>
    .scroll-container .btn {
      flex: 0 0 auto;
    }
    
    .hide-scrollbar::-webkit-scrollbar {
      display: none;
    }
    
    .hide-scrollbar {
      -ms-overflow-style: none;  
      scrollbar-width: none;  
    }
</style>
@endpush
@vite('resources/js/collection_manager.js')
<div class="container">
    <div class="d-flex flex-nowrap overflow-auto scroll-container hide-scrollbar gap-2">
        @foreach ($banks as $bank)
            <button 
                type="button"
                class="btn btn-outline-primary px-4 rounded-pill bank-btn flex-shrink-0"
                data-id="{{ $bank->id }}"
                data-mode="{{$mode}}"
                id="btn-{{ $bank->id }}">
                {{ $bank->name }}
            </button>
        @endforeach
    </div>
</div>
<div id="bank-content" data-id=0 data-mode="{{$mode}}">
    <div class="text-center py-5 text-muted">
        Select a bank above to view questions.
    </div>
</div>