<x-layout> 
    <x-slot name="title"> 
        {{__('maps.edit')}}
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

    <h1 class="mb-4">Edit {{$map->name}}</h1> 
    <form method="POST" action="{{ route('map.update', $map)}}" enctype="multipart/form-data"> 
        @csrf
        @method('PUT') 
        <div class="mb-3"> 
            <label class="form-label">{{__('maps.name')}}</label> 
            <input type="text" name="name" value = "{{ old('name', $map->name)}}" class="form-control"> 
        </div> 
        <img id="map-preview" src="{{ asset('storage/' . $map->svg_path) }}" 
                                alt="{{ $map->name }} Map" 
                                class="img-fluid mw-100 mh-100" 
                                style="object-fit: contain; filter: drop-shadow(0px 4px 6px rgba(0,0,0,0.06));">
        <div class="mb-3"> 
            <label>{{__('maps.image')}}</label> 
            <input id="map-input" type="file" class="form-control" name="map_image">
        </div>  
        
        <button type="submit" class="btn btn-primary">{{__('maps.save')}}</button> 
    </form> 
    <script>
        document.getElementById('map-input').addEventListener('change', function(event) {
                const file = event.target.files[0];
                
                if (file) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        document.getElementById('map-preview').src = e.target.result;
                    }
                    
                    reader.readAsDataURL(file);
                }
            });
    </script>
</x-layout>
