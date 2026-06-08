<x-layout> 
    <x-slot name="title"> 
        {{__('tests.create')}} 
    </x-slot> 
    
    @if ($errors->any())
    <div>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <h1 class="mb-4">{{__('tests.create')}} </h1> 
    <form method="POST" action="{{ route('test.store') }}"> 
        @csrf 
 
        <div class="mb-3"> 
            <label class="form-label">{{__('tests.name')}} </label> 
            <input type="text" name="name" value = "{{ old('name')}}" class="form-control"> 
        </div> 
 
        <div class="mb-3"> 
            <label class="form-label">{{__('tests.description')}} </label> 
            <textarea name="description" class="form-control" rows="5">{{ old('description') }}</textarea>  
        </div>  
        <button type="submit" class="btn btn-primary">{{__('tests.createButton')}} </button> 
    </form> 
</x-layout>