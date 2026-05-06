<x-layout>
    <x-slot name="title"> 
        {{$bank->name}} 
    </x-slot>
    <div id="bank-content">
        @include('banks.details_edit', ['bank' => $bank])
    </div>
</x-layout>