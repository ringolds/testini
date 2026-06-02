@vite('resources/js/collection_manager.js')
<x-layout>
    <x-slot name="title"> 
        {{$test->name}} 
    </x-slot>
    <div id="test-content" data-id="{{$test->id}}">
        @include('tests.details', ['test' => $test, 'mode' => $mode, 'target_id' => $test->id, 'collection_type'=>$type])
    </div>
</x-layout>