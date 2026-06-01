@vite('resources/js/map_manager.js')
<x-layout>
    <x-slot name="title"> 
        {{ $map->name }}
    </x-slot> 

    <div class="container py-4">
        <div class="d-flex align-items-center mb-4">
            <h1 class="h2 m-0 font-weight-bold">{{ $map->name }}</h1>
        </div>

        <div class="row">
            <div class="col-lg-9 mb-4">
                <div class="card shadow-sm border-0 rounded-lg overflow-hidden">
                    <div class="card-body p-0 bg-light">
                        <div id="chartdiv" style="width: 100%; height: 600px; min-height: 600px; background-color: #ffffff;" 
                        data-config-endpoint="{{ route('map.config', $map) }}"-></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card shadow-sm border-0 rounded-lg p-3 h-100">
                    <h3 class="h5 font-weight-bold border-bottom pb-2 mb-3">Region Inspector</h3>
                    
                    <div id="inspector-placeholder" class="text-muted small">
                        <p>Hover over or click on a region on the map to view its properties.</p>
                    </div>

                    <div id="inspector-data" style="display: none;">
                        <div class="mb-3">
                            <label class="text-muted d-block small uppercase font-weight-bold">Selected Region Name:</label>
                            <span id="selected-name" class="h5 text-primary font-weight-bold d-block"></span>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted d-block small uppercase font-weight-bold">ISO / Region ID Code:</label>
                            <code id="selected-id" class="d-inline-block bg-light px-2 py-1 rounded text-danger font-weight-bold"></code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row bg-white py-2">
            <div class="d-flex justify-content-end align-items-center gap-2">
                <a href="{{ route('map.edit', $map) }}" class="btn btn-warning d-flex align-items-center">
                    <i class="bi bi-pencil me-2"></i> Edit map
                </a>

                <form class="d-inline m-0" action="{{ route('map.destroy', $map) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this map?');"> 
                    @csrf 
                    @method('DELETE') 
                    <button type="submit" class="btn btn-danger">Delete</button> 
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/map.js"></script>
</x-layout>