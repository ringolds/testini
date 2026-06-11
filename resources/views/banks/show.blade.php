<x-layout>
    @vite('resources/js/collection_manager.js')
    <x-slot name="title"> 
        {{$bank->name}} 
    </x-slot>
    <div class="bank-content" data-id="{{$bank->id}}">
        @include('banks.details', ['bank' => $bank, 'mode' => $mode, 'target_id' => $bank->id, 'collection_type'=>$type])
    </div>
</x-layout>