<x-layout> 
    <x-slot name="title"> 
        Create a new Test
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

    <h1 class="mb-4">Create a new test</h1> 
    <form method="POST" action="{{ route('test.store') }}"> 
        @csrf 
 
        <div class="mb-3"> 
            <label class="form-label">Name</label> 
            <input type="text" name="name" value = "{{ old('name')}}" class="form-control"> 
        </div> 
 
        <div class="mb-3"> 
            <label class="form-label">Description</label> 
            <textarea name="description" class="form-control" rows="5">{{ old('description') }}</textarea>  
        </div>  
        <button type="submit" class="btn btn-primary">Create Bank</button> 
    </form> 
</x-layout>