<x-layout> 
    <x-slot name="title"> 
        {{__('banks.banks')}}
    </x-slot> 
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{__('banks.myBanks')}}</h1>
        <a href="{{ route('bank.create') }}" class="btn btn-success d-flex align-items-center">
            <i class="bi bi-plus-lg me-2"></i> {{__('banks.createButton')}}
        </a>
    </div>
    @include('banks.index_details', ['banks' => $banks, 'mode' => $mode])
</x-layout>