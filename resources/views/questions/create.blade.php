<x-layout> 
    <x-slot name="title"> 
        {{__('questions.create')}}
    </x-slot> 
   @include('questions.create_details', ['banks' => $banks, 'tests' => $tests, 'maps' => $maps, 'type' => 'separate', 'id' => 0])
</x-layout>
