<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Bank;
use App\Models\Test;
use App\Rules\ValidQuestionCount;

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

    public function addBank(Test $test){
        $user = request()->user();
        $used_banks = $test->banks()->get();
        $allBanks = $user->banks()->get();

        $banks = $allBanks->diff($used_banks);

        $mode="addBank";
        $target_id = $test->id;

        if (request()->ajax()) {
                return view('banks.index_details', compact('banks', 'mode', 'target_id'))->render();
            }
        return view('banks.index', compact('banks', 'mode', 'target_id'));
        
    }

    public function saveBank(Request $request, Test $test, Bank $bank){
        $user = $request->user();
        if ($user->can('addBankToTest', [$test, $bank])){

            $rules = [
                'count' => ['required', 'integer', 'min:1', new ValidQuestionCount($bank)]
            ];

            $request->validate($rules);
            
            $test->banks()->attach($bank, ['random_count'=>$request->input('count')]);
            
            $mode="manage";
            $target_id = $test->id;
            $type = "test";

            if ($request->ajax()) {
                $mode = "manage";
                $html = view('tests.details', compact('test', 'mode', 'target_id', 'type'))->render();

                return response()->json([
                    'success' => true,
                    'html'    => $html
                ]);
            }

            return view('tests.show', compact('test', 'mode', 'target_id', 'type'));
        }
        return redirect()->route('home');
    }

    public function removeBank(Request $request, Test $test, Bank $bank){
        $user = $request->user();
        if ($user->can('removeBankFromTest', [$test, $bank])){         
            $test->banks()->detach($bank);
            
            $mode="manage";
            $target_id = $test->id;
            $type = "test";

            if ($request->ajax()) {
                $mode = "manage";
                $html = view('tests.details', compact('test', 'mode', 'target_id', 'type'))->render();

                return response()->json([
                    'success' => true,
                    'html'    => $html
                ]);
            }

            return view('tests.show', compact('test', 'mode', 'target_id', 'type'));
        }
        return redirect()->route('home');
    }

    public function changeBankCount(Test $test, Bank $bank){
        $user = request()->user();
        if ($user->can('changeBankCount', [$test, $bank])){  
            $count = $test->banks->where('id', $bank->id)->first()->pivot->random_count;       
            if (request()->ajax()) {
                $mode = "change";
                $test = $test->id;
                $html = view('components.bank_row', compact('count', 'bank', 'test', 'mode'))->render();

                return response()->json([
                    'success' => true,
                    'html'    => $html
                ]);
            }
        }
        return redirect()->route('home');
    }

    public function updateBankCount(Request $request, Test $test, Bank $bank){
        $user = $request->user();
        if ($user->can('changeBankCount', [$test, $bank])){

            $rules = [
                'count' => ['required', 'integer', 'min:1', new ValidQuestionCount($bank)]
            ];

            $request->validate($rules);
            if ($request->ajax()) {
                $test->banks()->updateExistingPivot($bank->id, [
                    'random_count' => $request->input('count')
                ]);
                $count = $request->input('count');
                $mode = "view";
                $test = $test->id;
                $html = view('components.bank_row', compact('count', 'bank', 'test', 'mode'))->render();

                return response()->json([
                    'success' => true,
                    'html'    => $html
                ]);
            }
        }
        return redirect()->route('home');
    }

    public function availableTestIndex(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        if (!in_array($perPage, [12, 24])) {
            $perPage = 12; 
        }
        $userId = $request->user()->id;
        $tests = \App\Models\Test::with('user')->where(function ($query) use ($userId){
            $query->where('public', true);
            if ($userId) {
                $query->orWhere('user_id', $userId);
            }
        })
        ->latest()
        ->paginate($perPage)
        ->withQueryString();

        return view('tests.available_index', compact('tests'));
    }
}
