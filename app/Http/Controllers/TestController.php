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
    public function show(Test $test)
    {
        $user = request()->user();
        if ($user->cannot('view', $test)){
            return redirect()->route('home');
        }

        $test->load('questions.tests');

        $mode = request()->query('mode', 'manage');
        $target_id = request()->query('target-id', $test->id);
        $type = request()->query('type');

        if (request()->ajax()) {
            return view('tests.details', compact('test', 'mode', 'target_id', 'type'))->render();
        }

        return view('tests.show', compact('test', 'mode', 'target_id', 'type'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Test $test)
    {
        $user = request()->user();
        if ($user->cannot('update', $test)){
            return redirect()->route('home');
        }

        if (request()->ajax()) {
            return view('tests.details_edit', compact('test'))->render();
        }

        return view('tests.edit', compact('test'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Test $test)
    {
        if($request->user()->cannot('update', $test)){
            return redirect()->route('home');
        }

        $rules = array(
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tests')->where(fn ($query) => 
                    $query->where('user_id', Auth::id()))->ignore($test->id),
            ],
            'description' => 'required|min:5|max:500'
        );    

        $validated = $request->validate($rules);

        $request->merge([
            'public' => $request->has('public'),
        ]);

        $test->name = $validated['name'];
        $test->description = $validated['description'];
        $test->public = $request->public;

        $test->save();

        if(request()->ajax()){
                return response()->json([
                'id' => $test->id,
                'name' => $test->name,
                'success' => 'Test updated successfully!'
            ]);
        }

        return redirect()->route('test.index')->with('success', 'Test edited 
        successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Test $test)
    {
        $user = request()->user();
        if ($user->can('delete', $test)){
            $test->delete();

            if (request()->ajax()) {
                return response()->json(['success' => true, 'id' => $test->id]);
            }

            return redirect()->route('test.index');
        }
    }

    public function addQuestion(Test $test){
        $user = request()->user();
        $banks = $user->banks()->get();
        $mode = "add";
        $target_id = $test->id;
        if (request()->ajax()) {
                return view('banks.index_details', compact('banks', 'mode', 'target_id'))->render();
            }
        return view('banks.index', compact('banks', 'mode', 'target_id'));
    }
}
