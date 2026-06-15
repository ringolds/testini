<x-layout>
    @vite('resources/js/collection_manager.js')
    <x-slot name="title"> 
        {{$test->name}} 
    </x-slot>
    <div class="test-content" data-id="{{$test->id}}">
        @include('tests.details', ['test' => $test, 'mode' => $mode, 'target_id' => $test->id, 'collection_type'=>$type])
    </div>
</x-layout>