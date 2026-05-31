<x-layout> 
    <x-slot name="title"> 
        Maps
    </x-slot> 
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Maps</h1>
            <a href="{{ route('map.create') }}" class="btn btn-success d-flex align-items-center">
                <i class="bi bi-plus-lg me-2"></i> Create map
            </a>
        </div>
    <div class="row g-4"> 
        @foreach($maps as $map)
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100 shadow-sm hover-shadow border-0 rounded-lg overflow-hidden transition-all">
                    <div class="bg-light d-flex align-items-center justify-content-center p-3 border-bottom" style="height: 160px;">
                        @if($map->svg_path)
                            <img src="{{ asset('storage/' . $map->svg_path) }}" 
                                alt="{{ $map->name }} Map" 
                                class="img-fluid mw-100 mh-100" 
                                style="object-fit: contain; filter: drop-shadow(0px 4px 6px rgba(0,0,0,0.06));">
                        @else
                            <span class="text-muted small text-uppercase">No Preview</span>
                        @endif
                    </div>

                    <div class="card-body bg-white p-3 d-flex flex-column justify-content-between">
                        <h1 class="card-title text-dark font-weight-bold text-truncate mb-3" title="{{ $map->name }}">
                            {{ $map->name }}
                        </h1>
                    </div>
                    <div class="card-footer bg-white py-2">
                        <div class="d-flex justify-content-end align-items-center gap-2">
                            <a href="{{ route('map.edit', $map) }}" class="btn btn-warning d-flex align-items-center">
                                <i class="bi bi-pencil me-2"></i> Edit map
                            </a>

                            <form class="d-inline m-0" action="{{ route('map.destroy', $map) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this map?');"> 
                                @csrf 
                                @method('DELETE') 
                                <button type="submit" class="btn btn-danger">Delete</button> 
                            </form>

                            <a href="{{ route('map.show', $map) }}" class="btn btn-primary d-flex align-items-center">
                                <i class="bi bi-eye me-2"></i> Interactive view
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-layout>