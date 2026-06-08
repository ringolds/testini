<x-layout> 
    <x-slot name="title"> 
        {{__('maps.add')}}
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

    <h1 class="mb-4">{{__('maps.add')}}</h1> 
    <form method="POST" action="{{ route('map.store')}}" enctype="multipart/form-data"> 
        @csrf 
 
        <div class="mb-3"> 
            <label class="form-label">{{__('maps.name')}}</label> 
            <input type="text" name="name" value = "{{ old('name')}}" class="form-control"> 
        </div> 
 
        <div class="mb-3"> 
            <label class="form-label">{{__('maps.js')}}</label> 
            <input type="text" name="js_path" value = "{{ old('js_path')}}" class="form-control"> 
        </div>  

        <div class="mb-3"> 
            <label>{{__('maps.image')}}</label> 
            <input type="file" class="form-control" name="map_image">
        </div>  
        
        <button type="submit" class="btn btn-primary">{{__('maps.addButton')}}</button> 
    </form> 
</x-layout>