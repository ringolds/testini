<x-layout>
    <x-slot name="title"> 
        {{__('questions.editing')}}
    </x-slot>
    <div id="question-content">
        @include('questions.details_edit', ['question' => $question])
    </div>
</x-layout>