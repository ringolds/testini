<x-layout>
    <x-slot name="title"> 
        Question editing
    </x-slot>
    <div id="question-content">
        @include('questions.details_edit', ['question' => $question])
    </div>
</x-layout>