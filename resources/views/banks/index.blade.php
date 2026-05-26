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
<x-layout> 
    <x-slot name="title"> 
        Banks 
    </x-slot> 
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">My banks</h1>
        <a href="{{ route('bank.create') }}" class="btn btn-success d-flex align-items-center">
            <i class="bi bi-plus-lg me-2"></i> Create bank
        </a>
    </div>
    <div class="container">
        <div class="d-flex flex-nowrap overflow-auto scroll-container hide-scrollbar gap-2">
            @foreach ($banks as $bank)
                <button 
                    type="button"
                    class="btn btn-outline-primary px-4 rounded-pill bank-btn flex-shrink-0"
                    data-id="{{ $bank->id }}"
                    id="btn-{{ $bank->id }}">
                    {{ $bank->name }}
                </button>
            @endforeach
        </div>
    </div>
    <div id="bank-content">
        <div class="text-center py-5 text-muted">
            Select a bank above to view questions.
        </div>
    </div>
</x-layout>