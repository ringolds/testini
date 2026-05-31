<x-layout> 
    <x-slot name="title"> 
        Add a new Map
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

    <h1 class="mb-4">Add a new Map</h1> 
    <form method="POST" action="{{ route('map.store')}}" enctype="multipart/form-data"> 
        @csrf 
 
        <div class="mb-3"> 
            <label class="form-label">Name</label> 
            <input type="text" name="name" value = "{{ old('name')}}" class="form-control"> 
        </div> 
 
        <div class="mb-3"> 
            <label class="form-label">javascript URL</label> 
            <input type="text" name="js_path" value = "{{ old('js_path')}}" class="form-control"> 
        </div>  

        <div class="mb-3"> 
            <label>Image</label> 
            <input type="file" class="form-control" name="map_image">
        </div>  
        
        <button type="submit" class="btn btn-primary">Add Map</button> 
    </form> 
</x-layout>