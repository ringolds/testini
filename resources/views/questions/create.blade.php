<x-layout> 
    <x-slot name="title"> 
        Create a new Question
    </x-slot> 
   @include('questions.create_details', ['banks' => $banks, 'tests' => $tests, 'type' => 'separate', 'id' => 0])
</x-layout>
