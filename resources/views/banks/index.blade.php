<x-layout> 
    <x-slot name="title"> 
        Banks 
    </x-slot> 
    <div class="row">
        <h1 class="col-md-6 col-lg-4">My banks</h1> 
        <button type="submit" class="btn btn-success col">Create new Bank</button>
    </div>
    @if ($banks->count()) 
        <div class="row"> 
        @foreach ($banks as $bank) 
            <div class="col-md-6 col-lg-4"> 
               {{$bank->name}}
            </div> 
        @endforeach 
        </div> 
    @else 
        <div class="alert alert-info">No events available.</div> 
    @endif 
</x-layout>