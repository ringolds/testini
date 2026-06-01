<x-layout>
    <x-slot name="title"> 
        {{$test->name}} 
    </x-slot>
    <div id="bank-content">
        @include('tests.details_edit', ['test' => $test])
    </div>
</x-layout>