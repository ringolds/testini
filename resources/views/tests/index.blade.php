<x-layout> 
    @vite('resources/js/collection_manager.js')
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
    <x-slot name="title"> 
        {{__('tests.tests')}} 
    </x-slot> 
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{__('tests.myTests')}} </h1>
        <a href="{{ route('test.create') }}" class="btn btn-success d-flex align-items-center">
            <i class="bi bi-plus-lg me-2"></i> {{__('tests.createButton')}} 
        </a>
    </div>
    <div class="container">
        <div class="d-flex flex-nowrap overflow-auto scroll-container hide-scrollbar gap-2">
            @foreach ($tests as $test)
                <button 
                    type="button"
                    class="btn btn-outline-primary px-4 rounded-pill test-btn flex-shrink-0"
                    data-id="{{ $test->id }}"
                    data-mode="{{$mode}}"
                    id="btn-{{ $test->id }}">
                    {{ $test->name }}
                </button>
            @endforeach
        </div>
    </div>
    <div id="test-content" data-id=0 data-mode="{{$mode}}" data-target-id="{{$target_id}}">
        <div class="text-center py-5 text-muted">
            {{__('tests.select')}} 
        </div>
    </div>
</x-layout>