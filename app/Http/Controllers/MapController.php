<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Map;

class MapController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $maps = Map::all();
        return view('maps.index', compact('maps'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('maps.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'js_path' => 'required|url|starts_with:https://cdn.amcharts.com/lib/5/geodata/|ends_with:.js',
            'map_image' => 'required|file|mimes:svg|mimetypes:image/svg+xml|max:2048',             
        ]);

        $url = $request->input('js_path');

        try {
            $response = Http::timeout(3)->head($url);

            if (! $response->successful()) {
                return back()
                    ->withInput()
                    ->withErrors(['js_path' => 'The map URL is invalid or returned a ' . $response->status() . ' error.']);
            }

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['js_path' => 'Could not connect to the map URL. Please verify the link.']);
        }

        $file = $request->file('map_image');
        $path = $file->store('maps/images', 'public');

        Map::create([
            'name' => $request->name,
            'js_path' => $url,
            'svg_path' => $path
        ]);

        return redirect()->route('map.index')->with('success', 'Map registered successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Map $map)
    {
        return view('maps.show', compact('map'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Map $map)
    {
        return view('maps.edit', compact('map'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Map $map)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'map_image' => 'nullable|file|mimes:svg|mimetypes:image/svg+xml|max:2048',             
        ]);

        $file = $request->file('map_image');
        if($file){
            $path = $file->store('maps/images', 'public');
            $map->svg_path = $path;
        }

        if($request->name){
            $map->name = $request->name;
        }
        
        $map->update();
        
        return redirect()->route('map.index')->with('success', 'Map registered successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Map $map)
    {
        $map->delete();
        return redirect()->route('map.index');
        
    }

    public function getConfig(Map $map){
        return response()->json([
            'js_path' => $map->js_path,
            'mode'    => "create",
        ]);
    }
}
