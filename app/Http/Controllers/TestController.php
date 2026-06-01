<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Test;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $tests = $user->tests;
        $mode = "manage";
        $target_id = null;
        return view('tests.index', compact('tests', 'mode', 'target_id'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tests.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = array(
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tests')->where(fn ($query) => 
                    $query->where('user_id', Auth::id())
                ),
            ],
            'description' => 'required|min:5|max:500'
        );    

        $validated = $request->validate($rules);
    
        Test::create([ 
            'name' => $validated['name'], 
            'description' => $validated['description'], 
            'user_id'=> Auth::id(),
            'public'=> FALSE,
        ]); 

        return redirect()->route('test.index')->with('success', 'Test created 
        successfully!');   
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
