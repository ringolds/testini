<x-layout> 
    @vite('resources/js/map_manager.js')
    @vite('resources/js/game_manager.js')
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
        {{$test->name}} 
    </x-slot> 
    <div class="container">
        <div class="d-flex flex-nowrap overflow-auto scroll-container hide-scrollbar gap-2">
            @foreach ($questions as $question)
                <button 
                    type="button"
                    class="btn btn-outline-primary px-4 rounded-pill question-btn flex-shrink-0"
                    data-id="{{ $question->id}}"
                    data-target-id="{{$question->result_id}}"
                    id="btn-{{ $question->id }}">
                    {{ $question->order }}
                </button>
            @endforeach
        </div>
    </div>
    <div id="game-content" class="container mt-4" data-id=0 data-target-id="">
        <div class="text-center py-5 text-muted">
            Select a question above!
        </div>
    </div>
</x-layout>

